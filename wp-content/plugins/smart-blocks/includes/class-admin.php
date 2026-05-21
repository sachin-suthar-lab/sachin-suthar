<?php
/**
 * Admin controller for Sachin's Blocks.
 *
 * Top-level menu: "Submissions" with list / detail screens, plus an SMTP
 * settings subpage. Bulk actions and row actions are processed early on
 * admin_init so redirects work cleanly.
 *
 * @package SachinsBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Admin {

	const MENU_SLUG     = 'sachins-blocks-submissions';
	const SETTINGS_SLUG = 'sachins-blocks-settings';
	const CAP           = 'manage_options';

	public static function register(): void {
		add_action( 'admin_init', [ __CLASS__, 'maybe_install_table' ] );
		add_action( 'admin_init', [ __CLASS__, 'handle_actions' ] );
		add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
		add_action( 'admin_notices', [ __CLASS__, 'flash_notices' ] );
	}

	public static function maybe_install_table(): void {
		Submissions::maybe_install();
	}

	public static function menu(): void {
		$unread = (int) ( Submissions::counts_by_status()['new'] ?? 0 );
		$label  = __( 'Submissions', 'sachins-blocks' );
		if ( $unread > 0 ) {
			$label .= sprintf( ' <span class="awaiting-mod count-%d"><span class="pending-count">%d</span></span>', $unread, $unread );
		}

		add_menu_page(
			__( "Sachin's Blocks", 'sachins-blocks' ),
			__( "Sachin's Blocks", 'sachins-blocks' ),
			self::CAP,
			self::MENU_SLUG,
			[ __CLASS__, 'render_router' ],
			'dashicons-feedback',
			26
		);
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Contact Submissions', 'sachins-blocks' ),
			$label,
			self::CAP,
			self::MENU_SLUG,
			[ __CLASS__, 'render_router' ]
		);
		add_submenu_page(
			self::MENU_SLUG,
			__( 'SMTP & Email Settings', 'sachins-blocks' ),
			__( 'SMTP Settings', 'sachins-blocks' ),
			self::CAP,
			self::SETTINGS_SLUG,
			[ __CLASS__, 'render_settings' ]
		);
	}

	/** List vs. detail router. */
	public static function render_router(): void {
		if ( isset( $_GET['view'] ) ) {
			self::render_detail( (int) $_GET['view'] );
		} else {
			self::render_list();
		}
	}

	/* ---------- List screen ---------- */
	public static function render_list(): void {
		$table = new Submissions_List_Table();
		$table->prepare_items();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Contact Submissions', 'sachins-blocks' ); ?></h1>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::SETTINGS_SLUG ) ); ?>" class="page-title-action"><?php esc_html_e( 'SMTP Settings', 'sachins-blocks' ); ?></a>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=' . self::MENU_SLUG . '&action=export_csv' ), 'sb_export_csv' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Export CSV', 'sachins-blocks' ); ?></a>
			<hr class="wp-header-end">
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::MENU_SLUG ); ?>" />
				<?php $table->views(); ?>
				<?php $table->search_box( __( 'Search submissions', 'sachins-blocks' ), 'sb-search' ); ?>
				<?php $table->display(); ?>
			</form>
		</div>
		<?php
	}

	/* ---------- Detail screen ---------- */
	public static function render_detail( int $id ): void {
		$item = Submissions::get( $id );
		if ( ! $item ) {
			echo '<div class="wrap"><h1>' . esc_html__( 'Submission not found', 'sachins-blocks' ) . '</h1>';
			echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ) . '">' . esc_html__( '← Back to list', 'sachins-blocks' ) . '</a></p></div>';
			return;
		}

		// Auto-mark 'new' as 'read' on first view.
		if ( $item['status'] === 'new' ) {
			Submissions::update_fields( $id, [ 'status' => 'read' ] );
			$item['status'] = 'read';
		}

		$status_options = [ 'new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived' ];
		$back_url = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">
				<?php esc_html_e( 'Submission #', 'sachins-blocks' ); ?><?php echo (int) $item['id']; ?>
				&nbsp;<span style="font-weight:400;color:#646970">— <?php echo esc_html( $item['name'] ?: '(no name)' ); ?></span>
			</h1>
			<a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action">← <?php esc_html_e( 'All submissions', 'sachins-blocks' ); ?></a>
			<hr class="wp-header-end">

			<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;margin-top:20px">
				<div>
					<div class="postbox">
						<h2 class="hndle" style="padding:10px 12px"><?php esc_html_e( 'Message', 'sachins-blocks' ); ?></h2>
						<div class="inside">
							<pre style="white-space:pre-wrap;font-family:inherit;font-size:14px;line-height:1.6;margin:0"><?php echo esc_html( $item['message'] ); ?></pre>
						</div>
					</div>

					<div class="postbox">
						<h2 class="hndle" style="padding:10px 12px"><?php esc_html_e( 'Sender', 'sachins-blocks' ); ?></h2>
						<div class="inside">
							<table class="form-table" role="presentation">
								<tr><th><?php esc_html_e( 'Name',    'sachins-blocks' ); ?></th><td><?php echo esc_html( $item['name'] ); ?></td></tr>
								<tr><th><?php esc_html_e( 'Email',   'sachins-blocks' ); ?></th><td>
									<a href="mailto:<?php echo esc_attr( $item['email'] ); ?>"><?php echo esc_html( $item['email'] ); ?></a>
									&nbsp;·&nbsp;
									<a href="mailto:<?php echo esc_attr( $item['email'] ); ?>?subject=<?php echo rawurlencode( 'Re: your enquiry' ); ?>" class="button button-small"><?php esc_html_e( 'Reply', 'sachins-blocks' ); ?></a>
								</td></tr>
								<tr><th><?php esc_html_e( 'Company', 'sachins-blocks' ); ?></th><td><?php echo $item['company'] ? esc_html( $item['company'] ) : '—'; ?></td></tr>
								<tr><th><?php esc_html_e( 'IP',      'sachins-blocks' ); ?></th><td><code><?php echo esc_html( $item['ip_address'] ?: '—' ); ?></code></td></tr>
								<tr><th><?php esc_html_e( 'Browser', 'sachins-blocks' ); ?></th><td><span style="color:#646970;font-size:12px"><?php echo esc_html( $item['user_agent'] ?: '—' ); ?></span></td></tr>
								<tr><th><?php esc_html_e( 'Submitted', 'sachins-blocks' ); ?></th><td>
									<?php echo esc_html( wp_date( get_option( 'date_format' ) . ' · ' . get_option( 'time_format' ), strtotime( $item['submitted_at'] ) ) ); ?>
								</td></tr>
							</table>
						</div>
					</div>
				</div>

				<aside>
					<form method="post" action="">
						<?php wp_nonce_field( 'sb_save_submission_' . $id ); ?>
						<input type="hidden" name="sb_action" value="save_submission" />
						<input type="hidden" name="id" value="<?php echo (int) $id; ?>" />

						<div class="postbox">
							<h2 class="hndle" style="padding:10px 12px"><?php esc_html_e( 'Status', 'sachins-blocks' ); ?></h2>
							<div class="inside">
								<select name="status" style="width:100%">
									<?php foreach ( $status_options as $key => $label ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $item['status'], $key ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="postbox">
							<h2 class="hndle" style="padding:10px 12px"><?php esc_html_e( 'Internal notes', 'sachins-blocks' ); ?></h2>
							<div class="inside">
								<textarea name="notes" rows="6" style="width:100%;font-family:inherit" placeholder="<?php esc_attr_e( 'Private notes about this submission (not sent to the sender).', 'sachins-blocks' ); ?>"><?php echo esc_textarea( $item['notes'] ); ?></textarea>
							</div>
						</div>

						<p>
							<button type="submit" class="button button-primary button-large" style="width:100%"><?php esc_html_e( 'Save', 'sachins-blocks' ); ?></button>
						</p>

						<p>
							<a class="button button-link-delete" style="display:block;text-align:center" href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'page' => self::MENU_SLUG, 'action' => 'delete', 'id' => $id ], admin_url( 'admin.php' ) ), 'sb_delete_' . $id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this submission permanently?', 'sachins-blocks' ) ); ?>')">
								<?php esc_html_e( 'Delete permanently', 'sachins-blocks' ); ?>
							</a>
						</p>
					</form>
				</aside>
			</div>
		</div>
		<?php
	}

	/* ---------- SMTP settings screen ---------- */
	public static function render_settings(): void {
		$s = Mailer::settings();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'SMTP & Email Settings', 'sachins-blocks' ); ?></h1>
			<p style="max-width:680px;color:#646970"><?php esc_html_e( 'Configure outbound mail so contact-form notifications go through your own provider (Gmail, Mailgun, SES, Postmark, etc.) — no separate SMTP plugin required.', 'sachins-blocks' ); ?></p>

			<form method="post" action="">
				<?php wp_nonce_field( 'sb_save_smtp' ); ?>
				<input type="hidden" name="sb_action" value="save_smtp" />

				<table class="form-table" role="presentation">
					<tr><th><label for="enabled"><?php esc_html_e( 'Enable custom SMTP', 'sachins-blocks' ); ?></label></th>
						<td><label><input type="checkbox" id="enabled" name="enabled" value="1" <?php checked( $s['enabled'] ); ?>> <?php esc_html_e( 'Route wp_mail() through the SMTP server below', 'sachins-blocks' ); ?></label></td></tr>

					<tr><th><label for="host"><?php esc_html_e( 'SMTP host',  'sachins-blocks' ); ?></label></th>
						<td><input type="text" id="host" name="host" value="<?php echo esc_attr( $s['host'] ); ?>" class="regular-text" placeholder="smtp.gmail.com"></td></tr>

					<tr><th><label for="port"><?php esc_html_e( 'Port',       'sachins-blocks' ); ?></label></th>
						<td><input type="number" id="port" name="port" value="<?php echo esc_attr( $s['port'] ); ?>" min="1" max="65535" class="small-text">
							<span class="description"><?php esc_html_e( 'Common: 587 (TLS) · 465 (SSL) · 25 (none)', 'sachins-blocks' ); ?></span></td></tr>

					<tr><th><label for="encryption"><?php esc_html_e( 'Encryption', 'sachins-blocks' ); ?></label></th>
						<td><select id="encryption" name="encryption">
							<option value="tls"  <?php selected( $s['encryption'], 'tls' );  ?>>TLS</option>
							<option value="ssl"  <?php selected( $s['encryption'], 'ssl' );  ?>>SSL</option>
							<option value="none" <?php selected( $s['encryption'], 'none' ); ?>><?php esc_html_e( 'None (insecure)', 'sachins-blocks' ); ?></option>
						</select></td></tr>

					<tr><th><label for="auth"><?php esc_html_e( 'Authentication', 'sachins-blocks' ); ?></label></th>
						<td><label><input type="checkbox" id="auth" name="auth" value="1" <?php checked( $s['auth'] ); ?>> <?php esc_html_e( 'Use username + password', 'sachins-blocks' ); ?></label></td></tr>

					<tr><th><label for="username"><?php esc_html_e( 'Username', 'sachins-blocks' ); ?></label></th>
						<td><input type="text" id="username" name="username" value="<?php echo esc_attr( $s['username'] ); ?>" class="regular-text" autocomplete="off"></td></tr>

					<tr><th><label for="password"><?php esc_html_e( 'Password / App password', 'sachins-blocks' ); ?></label></th>
						<td><input type="password" id="password" name="password" value="" class="regular-text" autocomplete="new-password" placeholder="<?php echo $s['password'] ? esc_attr__( '••••••••  (leave blank to keep existing)', 'sachins-blocks' ) : ''; ?>">
							<p class="description"><?php esc_html_e( 'For Gmail, generate an "App Password" in your Google account.', 'sachins-blocks' ); ?></p></td></tr>

					<tr><th colspan="2"><h2 style="margin:0;padding-top:.5em;border-top:1px solid #ddd;padding-top:1em"><?php esc_html_e( 'From address', 'sachins-blocks' ); ?></h2></th></tr>

					<tr><th><label for="from_name"><?php esc_html_e( 'From name',  'sachins-blocks' ); ?></label></th>
						<td><input type="text" id="from_name" name="from_name" value="<?php echo esc_attr( $s['from_name'] ); ?>" class="regular-text"></td></tr>

					<tr><th><label for="from_email"><?php esc_html_e( 'From email', 'sachins-blocks' ); ?></label></th>
						<td><input type="email" id="from_email" name="from_email" value="<?php echo esc_attr( $s['from_email'] ); ?>" class="regular-text"></td></tr>
				</table>

				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Save SMTP settings', 'sachins-blocks' ); ?></button>
				</p>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Send a test email', 'sachins-blocks' ); ?></h2>
			<form method="post" action="" style="display:flex;gap:.6rem;align-items:flex-end;max-width:560px">
				<?php wp_nonce_field( 'sb_test_smtp' ); ?>
				<input type="hidden" name="sb_action" value="test_smtp" />
				<label style="flex:1">
					<span style="display:block;font-weight:600;margin-bottom:4px"><?php esc_html_e( 'Send to', 'sachins-blocks' ); ?></span>
					<input type="email" name="test_email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" required class="regular-text" style="width:100%">
				</label>
				<button type="submit" class="button button-secondary"><?php esc_html_e( 'Send test', 'sachins-blocks' ); ?></button>
			</form>
		</div>
		<?php
	}

	/* ---------- Action dispatcher ---------- */
	public static function handle_actions(): void {
		if ( ! current_user_can( self::CAP ) ) return;

		// POST actions
		$post_action = $_POST['sb_action'] ?? '';
		if ( $post_action === 'save_smtp' && check_admin_referer( 'sb_save_smtp' ) ) {
			Mailer::save( wp_unslash( $_POST ) );
			self::flash( 'success', __( 'SMTP settings saved.', 'sachins-blocks' ) );
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::SETTINGS_SLUG ) );
			exit;
		}
		if ( $post_action === 'test_smtp' && check_admin_referer( 'sb_test_smtp' ) ) {
			$to  = sanitize_email( wp_unslash( $_POST['test_email'] ?? '' ) );
			if ( ! is_email( $to ) ) {
				self::flash( 'error', __( 'Invalid test email address.', 'sachins-blocks' ) );
			} else {
				$res = Mailer::send_test( $to );
				if ( $res['ok'] ) self::flash( 'success', sprintf( __( 'Test message sent to %s.', 'sachins-blocks' ), $to ) );
				else              self::flash( 'error',   sprintf( __( 'Send failed: %s', 'sachins-blocks' ), $res['error'] ?: __( 'Unknown error.', 'sachins-blocks' ) ) );
			}
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::SETTINGS_SLUG ) );
			exit;
		}
		if ( $post_action === 'save_submission' && check_admin_referer( 'sb_save_submission_' . ( (int) ( $_POST['id'] ?? 0 ) ) ) ) {
			$id     = (int) ( $_POST['id'] ?? 0 );
			$status = sanitize_key( $_POST['status'] ?? 'new' );
			$notes  = wp_kses_post( wp_unslash( $_POST['notes'] ?? '' ) );
			Submissions::update_fields( $id, [ 'status' => $status, 'notes' => $notes ] );
			self::flash( 'success', __( 'Submission saved.', 'sachins-blocks' ) );
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::MENU_SLUG . '&view=' . $id ) );
			exit;
		}

		// GET actions
		$get_action = $_GET['action'] ?? '';
		if ( $get_action === 'delete' && isset( $_GET['id'] ) ) {
			$id = (int) $_GET['id'];
			if ( check_admin_referer( 'sb_delete_' . $id ) ) {
				Submissions::delete( $id );
				self::flash( 'success', __( 'Submission deleted.', 'sachins-blocks' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=' . self::MENU_SLUG ) );
				exit;
			}
		}
		if ( $get_action === 'export_csv' && check_admin_referer( 'sb_export_csv' ) ) {
			self::export_csv();
			exit;
		}

		// Bulk actions (from list table form)
		if ( isset( $_REQUEST['_wpnonce'] ) && check_admin_referer( 'bulk-submissions' ) ) {
			$bulk = $_REQUEST['action'] ?? $_REQUEST['action2'] ?? '';
			$ids  = array_map( 'intval', (array) ( $_REQUEST['ids'] ?? [] ) );
			if ( $bulk && $ids ) {
				if ( $bulk === 'delete' )           { Submissions::delete_many( $ids ); self::flash( 'success', sprintf( _n( '%d submission deleted.', '%d submissions deleted.', count( $ids ), 'sachins-blocks' ), count( $ids ) ) ); }
				elseif ( $bulk === 'mark_read' )    { foreach ( $ids as $id ) Submissions::update_fields( $id, [ 'status' => 'read' ] );     self::flash( 'success', __( 'Marked as read.',     'sachins-blocks' ) ); }
				elseif ( $bulk === 'mark_replied' ) { foreach ( $ids as $id ) Submissions::update_fields( $id, [ 'status' => 'replied' ] );  self::flash( 'success', __( 'Marked as replied.',  'sachins-blocks' ) ); }
				elseif ( $bulk === 'mark_archived' ){ foreach ( $ids as $id ) Submissions::update_fields( $id, [ 'status' => 'archived' ] ); self::flash( 'success', __( 'Archived.',           'sachins-blocks' ) ); }
				wp_safe_redirect( wp_get_referer() ?: admin_url( 'admin.php?page=' . self::MENU_SLUG ) );
				exit;
			}
		}
	}

	/* ---------- CSV export ---------- */
	private static function export_csv(): void {
		$res  = Submissions::query( [ 'per_page' => 10000, 'page' => 1 ] );
		$rows = $res['rows'];
		$filename = 'submissions-' . gmdate( 'Y-m-d' ) . '.csv';
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, [ 'ID', 'Submitted', 'Name', 'Email', 'Company', 'Status', 'Message', 'IP', 'User-Agent' ] );
		foreach ( $rows as $r ) {
			fputcsv( $out, [ $r['id'], $r['submitted_at'], $r['name'], $r['email'], $r['company'], $r['status'], $r['message'], $r['ip_address'], $r['user_agent'] ] );
		}
		fclose( $out );
	}

	/* ---------- Flash notices ---------- */
	private static function flash( string $type, string $msg ): void {
		$flash   = get_transient( 'sb_admin_flash' ) ?: [];
		$flash[] = [ 'type' => $type, 'msg' => $msg ];
		set_transient( 'sb_admin_flash', $flash, 30 );
	}
	public static function flash_notices(): void {
		$flash = get_transient( 'sb_admin_flash' );
		if ( ! $flash ) return;
		delete_transient( 'sb_admin_flash' );
		foreach ( (array) $flash as $f ) {
			$cls = 'notice notice-' . ( $f['type'] === 'error' ? 'error' : 'success' ) . ' is-dismissible';
			printf( '<div class="%s"><p>%s</p></div>', esc_attr( $cls ), esc_html( $f['msg'] ) );
		}
	}
}
