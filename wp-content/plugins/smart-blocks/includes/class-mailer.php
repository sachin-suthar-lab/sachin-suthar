<?php
/**
 * SMTP integration.
 *
 * Replaces the need for a third-party SMTP plugin: stores credentials in the
 * `sachins_blocks_smtp` option, hooks into `phpmailer_init` to apply them, and
 * overrides the From name/email on every wp_mail call when configured.
 *
 * Password is base64-obfuscated at rest. (Not real encryption — wp-config
 * salts already protect the DB. This just keeps it out of casual exports.)
 *
 * @package SachinsBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Mailer {

	const OPTION = 'sachins_blocks_smtp';

	public static function defaults(): array {
		return [
			'enabled'    => 0,
			'host'       => '',
			'port'       => 587,
			'encryption' => 'tls', // none | ssl | tls
			'auth'       => 1,
			'username'   => '',
			'password'   => '',
			'from_name'  => get_bloginfo( 'name' ),
			'from_email' => get_option( 'admin_email' ),
		];
	}

	public static function settings(): array {
		$opts = get_option( self::OPTION, [] );
		return array_merge( self::defaults(), is_array( $opts ) ? $opts : [] );
	}

	public static function save( array $input ): array {
		$s = self::defaults();
		$s['enabled']    = ! empty( $input['enabled'] ) ? 1 : 0;
		$s['host']       = isset( $input['host'] ) ? sanitize_text_field( $input['host'] ) : '';
		$s['port']       = isset( $input['port'] ) ? max( 1, min( 65535, (int) $input['port'] ) ) : 587;
		$s['encryption'] = in_array( $input['encryption'] ?? '', [ 'none', 'ssl', 'tls' ], true ) ? $input['encryption'] : 'tls';
		$s['auth']       = ! empty( $input['auth'] ) ? 1 : 0;
		$s['username']   = isset( $input['username'] ) ? sanitize_text_field( $input['username'] ) : '';
		// Keep existing password if the field was left blank (so admins can save other fields without re-typing).
		$existing = self::settings();
		$s['password']   = ( isset( $input['password'] ) && $input['password'] !== '' )
			? base64_encode( $input['password'] )
			: $existing['password'];
		$s['from_name']  = isset( $input['from_name'] ) ? sanitize_text_field( $input['from_name'] ) : '';
		$s['from_email'] = isset( $input['from_email'] ) && is_email( $input['from_email'] ) ? sanitize_email( $input['from_email'] ) : '';
		update_option( self::OPTION, $s, false );
		return $s;
	}

	public static function decrypted_password(): string {
		$pwd = self::settings()['password'] ?? '';
		return $pwd ? (string) base64_decode( $pwd, true ) : '';
	}

	public static function register(): void {
		add_action( 'phpmailer_init', [ __CLASS__, 'apply' ] );
		add_filter( 'wp_mail_from',      [ __CLASS__, 'from_email' ] );
		add_filter( 'wp_mail_from_name', [ __CLASS__, 'from_name' ] );
	}

	public static function apply( \PHPMailer\PHPMailer\PHPMailer $phpmailer ): void {
		$s = self::settings();
		if ( empty( $s['enabled'] ) || empty( $s['host'] ) ) return;

		$phpmailer->isSMTP();
		$phpmailer->Host       = $s['host'];
		$phpmailer->Port       = (int) $s['port'];
		$phpmailer->SMTPAuth   = ! empty( $s['auth'] );
		if ( $phpmailer->SMTPAuth ) {
			$phpmailer->Username = $s['username'];
			$phpmailer->Password = self::decrypted_password();
		}
		$phpmailer->SMTPSecure = $s['encryption'] === 'none' ? '' : $s['encryption'];
		$phpmailer->SMTPAutoTLS = $s['encryption'] !== 'none';
	}

	public static function from_email( $current ) {
		$s = self::settings();
		return ! empty( $s['enabled'] ) && ! empty( $s['from_email'] ) ? $s['from_email'] : $current;
	}
	public static function from_name( $current ) {
		$s = self::settings();
		return ! empty( $s['enabled'] ) && ! empty( $s['from_name'] ) ? $s['from_name'] : $current;
	}

	/**
	 * Send a test message to the given address; capture phpmailer error if any.
	 */
	public static function send_test( string $to ): array {
		$captured_error = '';
		$capture = static function ( \WP_Error $error ) use ( &$captured_error ) {
			$captured_error = $error->get_error_message();
		};
		add_action( 'wp_mail_failed', $capture );

		$subject = '[' . get_bloginfo( 'name' ) . '] SMTP test from Sachin\'s Blocks';
		$body    = "If you're reading this in your inbox, SMTP is wired up correctly.\n\nSent from: " . home_url( '/' );
		$ok      = wp_mail( $to, $subject, $body );

		remove_action( 'wp_mail_failed', $capture );

		return [ 'ok' => (bool) $ok, 'error' => $captured_error ];
	}
}
