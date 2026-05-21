<?php
/**
 * Smart Blocks — contact form handler.
 *
 * Receives the form from the Contact Section block, validates a nonce + honeypot,
 * then emails the site admin and redirects back with a success / error flag.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks\Contact;

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_post_nopriv_smart_blocks_contact', __NAMESPACE__ . '\\handle' );
add_action( 'admin_post_smart_blocks_contact',        __NAMESPACE__ . '\\handle' );

function handle(): void {
	$referer = wp_get_referer() ?: home_url( '/' );

	if ( ! isset( $_POST['smart_blocks_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['smart_blocks_nonce'] ) ), 'smart_blocks_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'sb_contact', 'invalid', $referer ) . '#contact' );
		exit;
	}

	// Honeypot — silent reject.
	if ( ! empty( $_POST['sb_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sb_contact', 'sent', $referer ) . '#contact' );
		exit;
	}

	$name    = isset( $_POST['sb_name'] )    ? sanitize_text_field( wp_unslash( $_POST['sb_name'] ) )    : '';
	$email   = isset( $_POST['sb_email'] )   ? sanitize_email(      wp_unslash( $_POST['sb_email'] ) )   : '';
	$company = isset( $_POST['sb_company'] ) ? sanitize_text_field( wp_unslash( $_POST['sb_company'] ) ) : '';
	$message = isset( $_POST['sb_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['sb_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		wp_safe_redirect( add_query_arg( 'sb_contact', 'invalid', $referer ) . '#contact' );
		exit;
	}

	$to      = get_option( 'admin_email' );
	$subject = sprintf( '[Portfolio] New enquiry from %s', $name );
	$body    = sprintf(
		"Name:    %s\nEmail:   %s\nCompany: %s\n\nMessage:\n%s\n",
		$name, $email, $company, $message
	);
	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	];

	wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'sb_contact', 'sent', $referer ) . '#contact' );
	exit;
}
