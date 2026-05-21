<?php
/**
 * WP_List_Table for contact submissions.
 *
 * @package SachinsBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

final class Submissions_List_Table extends \WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => 'submission',
			'plural'   => 'submissions',
			'ajax'     => false,
		] );
	}

	public function get_columns(): array {
		return [
			'cb'           => '<input type="checkbox" />',
			'name'         => __( 'From', 'sachins-blocks' ),
			'message'      => __( 'Message', 'sachins-blocks' ),
			'company'      => __( 'Company', 'sachins-blocks' ),
			'status'       => __( 'Status', 'sachins-blocks' ),
			'submitted_at' => __( 'Submitted', 'sachins-blocks' ),
		];
	}

	protected function get_sortable_columns(): array {
		return [
			'name'         => [ 'name', false ],
			'submitted_at' => [ 'submitted_at', true ],
			'status'       => [ 'status', false ],
		];
	}

	protected function get_bulk_actions(): array {
		return [
			'mark_read'     => __( 'Mark as read',     'sachins-blocks' ),
			'mark_replied'  => __( 'Mark as replied',  'sachins-blocks' ),
			'mark_archived' => __( 'Archive',          'sachins-blocks' ),
			'delete'        => __( 'Delete permanently', 'sachins-blocks' ),
		];
	}

	public function column_cb( $item ): string {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d" />', (int) $item['id'] );
	}

	public function column_name( $item ): string {
		$id    = (int) $item['id'];
		$url   = add_query_arg( [ 'page' => 'sachins-blocks-submissions', 'view' => $id ], admin_url( 'admin.php' ) );
		$name  = esc_html( $item['name'] ?: '(no name)' );
		$email = esc_html( $item['email'] );
		$weight = $item['status'] === 'new' ? '700' : '500';

		$actions = [
			'view'   => sprintf( '<a href="%s">%s</a>', esc_url( $url ), __( 'View', 'sachins-blocks' ) ),
			'reply'  => sprintf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $item['email'] ), __( 'Reply', 'sachins-blocks' ) ),
			'delete' => sprintf(
				'<a href="%s" class="sb-confirm" data-confirm="%s" style="color:#b91c1c;">%s</a>',
				esc_url( wp_nonce_url( add_query_arg( [ 'page' => 'sachins-blocks-submissions', 'action' => 'delete', 'id' => $id ], admin_url( 'admin.php' ) ), 'sb_delete_' . $id ) ),
				esc_attr__( 'Delete this submission permanently?', 'sachins-blocks' ),
				__( 'Delete', 'sachins-blocks' )
			),
		];

		return sprintf(
			'<strong style="font-weight:%s"><a href="%s">%s</a></strong><br><span style="color:#646970">%s</span>%s',
			$weight,
			esc_url( $url ),
			$name,
			$email,
			$this->row_actions( $actions )
		);
	}

	public function column_message( $item ): string {
		$snippet = wp_strip_all_tags( $item['message'] );
		$snippet = mb_strlen( $snippet ) > 140 ? mb_substr( $snippet, 0, 140 ) . '…' : $snippet;
		return '<span style="color:#1d2327">' . esc_html( $snippet ) . '</span>';
	}

	public function column_company( $item ): string {
		return $item['company'] ? esc_html( $item['company'] ) : '—';
	}

	public function column_status( $item ): string {
		$status  = $item['status'] ?: 'new';
		$colors  = [
			'new'      => [ 'bg' => '#ddd6fe', 'fg' => '#5b21b6', 'label' => 'New' ],
			'read'     => [ 'bg' => '#e2e8f0', 'fg' => '#475569', 'label' => 'Read' ],
			'replied'  => [ 'bg' => '#bbf7d0', 'fg' => '#166534', 'label' => 'Replied' ],
			'archived' => [ 'bg' => '#fef3c7', 'fg' => '#92400e', 'label' => 'Archived' ],
		];
		$c = $colors[ $status ] ?? $colors['new'];
		return sprintf(
			'<span style="display:inline-block;padding:2px 10px;border-radius:999px;background:%s;color:%s;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">%s</span>',
			esc_attr( $c['bg'] ), esc_attr( $c['fg'] ), esc_html( $c['label'] )
		);
	}

	public function column_submitted_at( $item ): string {
		$ts = strtotime( $item['submitted_at'] );
		if ( ! $ts ) return '—';
		$delta = time() - $ts;
		$human = $delta < HOUR_IN_SECONDS * 24
			? sprintf( __( '%s ago', 'sachins-blocks' ), human_time_diff( $ts ) )
			: wp_date( get_option( 'date_format' ) . ' · ' . get_option( 'time_format' ), $ts );
		return '<span title="' . esc_attr( $item['submitted_at'] ) . '">' . esc_html( $human ) . '</span>';
	}

	public function prepare_items(): void {
		$per_page = 20;
		$page     = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
		$search   = isset( $_GET['s'] )      ? sanitize_text_field( wp_unslash( $_GET['s'] ) )      : '';
		$status   = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] )                       : '';
		$orderby  = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] )                     : 'submitted_at';
		$order    = isset( $_GET['order'] )   ? sanitize_key( $_GET['order'] )                       : 'desc';

		$res = Submissions::query( [
			'per_page' => $per_page, 'page' => $page,
			'search'   => $search,   'status' => $status,
			'orderby'  => $orderby,  'order'  => $order,
		] );

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];
		$this->items = $res['rows'];

		$this->set_pagination_args( [
			'total_items' => $res['total'],
			'per_page'    => $per_page,
			'total_pages' => max( 1, (int) ceil( $res['total'] / $per_page ) ),
		] );
	}

	public function no_items() {
		_e( 'No contact submissions yet.', 'sachins-blocks' );
	}

	/** Status filter views above the table. */
	protected function get_views(): array {
		$counts  = Submissions::counts_by_status();
		$current = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
		$base    = admin_url( 'admin.php?page=sachins-blocks-submissions' );

		$row = function ( string $key, string $label ) use ( $counts, $current, $base ) {
			$url = $key === 'all' ? $base : add_query_arg( 'status', $key, $base );
			$is_current = ( $current === $key ) || ( $current === '' && $key === 'all' );
			$class = $is_current ? ' class="current"' : '';
			return sprintf( '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( $url ), $class, esc_html( $label ), (int) ( $counts[ $key ] ?? 0 ) );
		};

		return [
			'all'      => $row( 'all',      __( 'All',      'sachins-blocks' ) ),
			'new'      => $row( 'new',      __( 'New',      'sachins-blocks' ) ),
			'read'     => $row( 'read',     __( 'Read',     'sachins-blocks' ) ),
			'replied'  => $row( 'replied',  __( 'Replied',  'sachins-blocks' ) ),
			'archived' => $row( 'archived', __( 'Archived', 'sachins-blocks' ) ),
		];
	}
}
