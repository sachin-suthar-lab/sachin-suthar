<?php
/**
 * Smart Blocks — contact form handler.
 *
 * Exposes a REST endpoint `smart-blocks/v1/contact` that receives the form
 * payload, validates nonce + honeypot + field rules, sends an email, and
 * returns a structured JSON response that the front-end view.js renders.
 *
 * Falls back to the classic admin-post handler for users with JS disabled.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks\Contact;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------- REST endpoint (primary path used by AJAX form) ---------- */
add_action( 'rest_api_init', __NAMESPACE__ . '\\register_route' );

function register_route(): void {
	register_rest_route( 'smart-blocks/v1', '/contact', [
		'methods'             => \WP_REST_Server::CREATABLE,
		'callback'            => __NAMESPACE__ . '\\rest_handle',
		'permission_callback' => '__return_true',
	] );
}

function rest_handle( \WP_REST_Request $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new \WP_REST_Response( [
			'ok'      => false,
			'code'    => 'bad_nonce',
			'message' => __( 'Security check failed. Please refresh and try again.', 'smart-blocks' ),
		], 403 );
	}

	$body = $request->get_json_params() ?: $request->get_body_params();
	$res  = validate_and_send( $body );

	return new \WP_REST_Response( $res, $res['ok'] ? 200 : 400 );
}

/* ---------- Legacy non-JS fallback (admin-post) ---------- */
add_action( 'admin_post_nopriv_smart_blocks_contact', __NAMESPACE__ . '\\fallback_handle' );
add_action( 'admin_post_smart_blocks_contact',        __NAMESPACE__ . '\\fallback_handle' );

function fallback_handle(): void {
	$referer = wp_get_referer() ?: home_url( '/' );
	if ( ! isset( $_POST['smart_blocks_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['smart_blocks_nonce'] ) ), 'smart_blocks_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'sb_contact', 'invalid', $referer ) . '#contact' );
		exit;
	}
	$res = validate_and_send( wp_unslash( $_POST ) );
	wp_safe_redirect( add_query_arg( 'sb_contact', $res['ok'] ? 'sent' : 'invalid', $referer ) . '#contact' );
	exit;
}

/* ---------- Shared validation + send ---------- */
function validate_and_send( $body ): array {
	$body = is_array( $body ) ? $body : [];

	// Honeypot — silent reject reported as success so bots don't iterate.
	if ( ! empty( $body['sb_website'] ) ) {
		return [ 'ok' => true, 'message' => '', 'silent' => true ];
	}

	$name    = isset( $body['sb_name'] )    ? sanitize_text_field( $body['sb_name'] )           : '';
	$email   = isset( $body['sb_email'] )   ? sanitize_email( $body['sb_email'] )               : '';
	$company = isset( $body['sb_company'] ) ? sanitize_text_field( $body['sb_company'] )        : '';
	$message = isset( $body['sb_message'] ) ? sanitize_textarea_field( $body['sb_message'] )    : '';

	$errors = [];
	if ( '' === trim( $name ) )           { $errors['sb_name']    = __( 'Please enter your name.', 'smart-blocks' ); }
	if ( ! is_email( $email ) )           { $errors['sb_email']   = __( 'Please enter a valid email address.', 'smart-blocks' ); }
	if ( strlen( trim( $message ) ) < 12 ) { $errors['sb_message'] = __( 'Please write at least a sentence about your project.', 'smart-blocks' ); }

	if ( ! empty( $errors ) ) {
		return [
			'ok'      => false,
			'code'    => 'validation',
			'message' => __( 'Please fix the highlighted fields and try again.', 'smart-blocks' ),
			'errors'  => $errors,
		];
	}

	$to      = apply_filters( 'smart_blocks/contact/to', get_option( 'admin_email' ) );
	$subject = sprintf( __( '[Portfolio] New enquiry from %s', 'smart-blocks' ), $name );
	$msg     = sprintf(
		"Name:    %s\nEmail:   %s\nCompany: %s\n\nMessage:\n%s\n",
		$name, $email, $company, $message
	);
	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'Reply-To: %s <%s>', $name, $email ),
	];

	$sent = wp_mail( $to, $subject, $msg, $headers );

	// Persist the submission regardless of mail success — never silently lose
	// a message because of an SMTP hiccup. Admin UI surfaces both states.
	$submission_id = \SmartBlocks\Submissions::insert( [
		'name'    => $name,
		'email'   => $email,
		'company' => $company,
		'message' => $message,
	] );

	if ( ! $sent ) {
		return [
			'ok'      => false,
			'code'    => 'mail_failed',
			'id'      => $submission_id,
			'message' => sprintf(
				/* translators: %s: site admin email */
				__( 'We saved your message but the email notification did not send. The site owner will see it in the admin dashboard. You can also reach out directly at %s.', 'sachins-blocks' ),
				get_option( 'admin_email' )
			),
		];
	}

	return [
		'ok'      => true,
		'id'      => $submission_id,
		'message' => __( 'Thanks — your message landed. I will reply within two working days.', 'sachins-blocks' ),
	];
}
