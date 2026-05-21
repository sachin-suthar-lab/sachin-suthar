<?php
/**
 * Contact submissions data layer.
 *
 * Owns the `{$prefix}sb_contact_submissions` table: schema, CRUD,
 * query helpers. Created via dbDelta on plugin activation, upgraded
 * transparently when the schema version bumps.
 *
 * @package SachinsBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Submissions {

	/** Bump this when the table schema changes — triggers dbDelta on next admin load. */
	const SCHEMA_VERSION = '1.0';
	const OPTION_KEY     = 'sb_submissions_schema';

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'sb_contact_submissions';
	}

	/**
	 * Create or upgrade the table. Called from activation + admin_init guard.
	 */
	public static function maybe_install(): void {
		if ( get_option( self::OPTION_KEY ) === self::SCHEMA_VERSION ) {
			return;
		}
		global $wpdb;
		$charset = $wpdb->get_charset_collate();
		$table   = self::table();
		$sql = "CREATE TABLE {$table} (
			id              BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			submitted_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			name            VARCHAR(191) NOT NULL DEFAULT '',
			email           VARCHAR(191) NOT NULL DEFAULT '',
			company         VARCHAR(191) NOT NULL DEFAULT '',
			message         LONGTEXT NOT NULL,
			ip_address      VARCHAR(45) NOT NULL DEFAULT '',
			user_agent      TEXT NOT NULL,
			status          VARCHAR(20) NOT NULL DEFAULT 'new',
			notes           TEXT NOT NULL,
			PRIMARY KEY (id),
			KEY status (status),
			KEY submitted_at (submitted_at),
			KEY email (email)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( self::OPTION_KEY, self::SCHEMA_VERSION, false );
	}

	/**
	 * Insert a sanitised submission. Returns the new ID or false.
	 */
	public static function insert( array $data ) {
		global $wpdb;
		$ok = $wpdb->insert(
			self::table(),
			[
				'submitted_at' => current_time( 'mysql' ),
				'name'         => $data['name'] ?? '',
				'email'        => $data['email'] ?? '',
				'company'      => $data['company'] ?? '',
				'message'      => $data['message'] ?? '',
				'ip_address'   => self::client_ip(),
				'user_agent'   => substr( $_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500 ),
				'status'       => 'new',
				'notes'        => '',
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
		);
		return $ok ? (int) $wpdb->insert_id : false;
	}

	public static function get( int $id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . self::table() . ' WHERE id = %d', $id ), ARRAY_A );
		return $row ?: null;
	}

	public static function update_fields( int $id, array $fields ): bool {
		global $wpdb;
		$allowed = [ 'status' => '%s', 'notes' => '%s' ];
		$data    = []; $format = [];
		foreach ( $allowed as $key => $fmt ) {
			if ( array_key_exists( $key, $fields ) ) {
				$data[ $key ] = $fields[ $key ];
				$format[]     = $fmt;
			}
		}
		if ( empty( $data ) ) return false;
		return false !== $wpdb->update( self::table(), $data, [ 'id' => $id ], $format, [ '%d' ] );
	}

	public static function delete( int $id ): bool {
		global $wpdb;
		return false !== $wpdb->delete( self::table(), [ 'id' => $id ], [ '%d' ] );
	}

	public static function delete_many( array $ids ): int {
		global $wpdb;
		$ids = array_filter( array_map( 'intval', $ids ) );
		if ( empty( $ids ) ) return 0;
		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		$sql = "DELETE FROM " . self::table() . " WHERE id IN ($placeholders)";
		return (int) $wpdb->query( $wpdb->prepare( $sql, ...$ids ) );
	}

	/**
	 * Paginated list. Supports search, status filter, ordering.
	 */
	public static function query( array $args = [] ): array {
		global $wpdb;
		$a = wp_parse_args( $args, [
			'per_page' => 20,
			'page'     => 1,
			'search'   => '',
			'status'   => '',
			'orderby'  => 'submitted_at',
			'order'    => 'DESC',
		] );

		$orderby_allowed = [ 'id', 'name', 'email', 'submitted_at', 'status' ];
		$orderby = in_array( $a['orderby'], $orderby_allowed, true ) ? $a['orderby'] : 'submitted_at';
		$order   = strtoupper( $a['order'] ) === 'ASC' ? 'ASC' : 'DESC';

		$where = [ '1=1' ];
		$bind  = [];
		if ( $a['status'] !== '' ) {
			$where[] = 'status = %s';
			$bind[]  = $a['status'];
		}
		if ( $a['search'] !== '' ) {
			$where[] = '( name LIKE %s OR email LIKE %s OR message LIKE %s OR company LIKE %s )';
			$like = '%' . $wpdb->esc_like( $a['search'] ) . '%';
			$bind = array_merge( $bind, [ $like, $like, $like, $like ] );
		}
		$where_sql = implode( ' AND ', $where );
		$table     = self::table();

		$total_sql = "SELECT COUNT(*) FROM $table WHERE $where_sql";
		$total     = (int) $wpdb->get_var( $bind ? $wpdb->prepare( $total_sql, ...$bind ) : $total_sql );

		$per_page = max( 1, (int) $a['per_page'] );
		$offset   = max( 0, ( (int) $a['page'] - 1 ) * $per_page );
		$list_sql = "SELECT * FROM $table WHERE $where_sql ORDER BY $orderby $order LIMIT %d OFFSET %d";
		$list_bind = array_merge( $bind, [ $per_page, $offset ] );
		$rows = $wpdb->get_results( $wpdb->prepare( $list_sql, ...$list_bind ), ARRAY_A );

		return [ 'rows' => $rows ?: [], 'total' => $total ];
	}

	public static function counts_by_status(): array {
		global $wpdb;
		$rows = $wpdb->get_results( 'SELECT status, COUNT(*) AS n FROM ' . self::table() . ' GROUP BY status', ARRAY_A );
		$out  = [ 'all' => 0, 'new' => 0, 'read' => 0, 'replied' => 0, 'archived' => 0 ];
		foreach ( (array) $rows as $r ) {
			$status = $r['status'] ?: 'new';
			$n      = (int) $r['n'];
			$out['all']      += $n;
			$out[ $status ]   = ( $out[ $status ] ?? 0 ) + $n;
		}
		return $out;
	}

	private static function client_ip(): string {
		$candidates = [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ];
		foreach ( $candidates as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip = explode( ',', $_SERVER[ $key ] )[0];
				$ip = trim( $ip );
				if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) return $ip;
			}
		}
		return '';
	}
}
