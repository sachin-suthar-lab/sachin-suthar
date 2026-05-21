<?php
/**
 * Idempotent homepage provisioner for the Sachin Suthar portfolio.
 *
 * Run via:
 *   wp eval-file tools/setup-homepage.php
 *
 * What it does:
 *   1. Activates the sachin-suthar theme (if not already active).
 *   2. Activates the smart-blocks plugin (if not already active).
 *   3. Creates / updates a page slug "home" containing the full 9-block sequence.
 *   4. Sets the static front page to that page.
 *
 * Safe to run repeatedly: looks up the page by slug, updates its content,
 * and only flips options that aren't already correct.
 *
 * @package SachinSuthar
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file tools/setup-homepage.php\n" );
	exit( 1 );
}

/* ------------------------------------------------------------------ */
/* 1. Theme                                                            */
/* ------------------------------------------------------------------ */
$theme = wp_get_theme( 'sachin-suthar' );
if ( ! $theme->exists() ) {
	WP_CLI::error( 'Theme "sachin-suthar" not found in wp-content/themes/.' );
}
if ( get_stylesheet() !== 'sachin-suthar' ) {
	switch_theme( 'sachin-suthar' );
	WP_CLI::log( '✓ Activated theme sachin-suthar' );
} else {
	WP_CLI::log( '· Theme sachin-suthar already active' );
}

/* ------------------------------------------------------------------ */
/* 2. Plugin                                                           */
/* ------------------------------------------------------------------ */
if ( ! function_exists( 'activate_plugin' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$plugin_file = 'smart-blocks/smart-blocks.php';
if ( ! is_plugin_active( $plugin_file ) ) {
	$result = activate_plugin( $plugin_file );
	if ( is_wp_error( $result ) ) {
		WP_CLI::error( 'Could not activate smart-blocks: ' . $result->get_error_message() );
	}
	WP_CLI::log( '✓ Activated plugin smart-blocks' );
} else {
	WP_CLI::log( '· Plugin smart-blocks already active' );
}

/* ------------------------------------------------------------------ */
/* 3. Homepage content                                                 */
/* ------------------------------------------------------------------ */
$content  = "<!-- wp:smart-blocks/hero /-->\n\n";
$content .= "<!-- wp:smart-blocks/services-grid /-->\n\n";
$content .= "<!-- wp:smart-blocks/skills-showcase /-->\n\n";
$content .= "<!-- wp:smart-blocks/experience-timeline /-->\n\n";
$content .= "<!-- wp:smart-blocks/tech-stack /-->\n\n";
$content .= "<!-- wp:smart-blocks/portfolio-projects /-->\n\n";
$content .= "<!-- wp:smart-blocks/testimonials /-->\n\n";
$content .= "<!-- wp:smart-blocks/cta-section /-->\n\n";
$content .= "<!-- wp:smart-blocks/contact-section /-->\n";

$existing = get_page_by_path( 'home', OBJECT, 'page' );

$postarr = [
	'post_title'   => 'Home',
	'post_name'    => 'home',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_content' => $content,
	'post_author'  => 1,
	'meta_input'   => [
		'_wp_page_template' => 'front-page',
	],
];

if ( $existing ) {
	$postarr['ID'] = $existing->ID;
	$page_id = wp_update_post( $postarr, true );
	$action  = 'updated';
} else {
	$page_id = wp_insert_post( $postarr, true );
	$action  = 'created';
}

if ( is_wp_error( $page_id ) ) {
	WP_CLI::error( 'Failed to write home page: ' . $page_id->get_error_message() );
}
WP_CLI::log( sprintf( '✓ Home page %s (ID %d)', $action, $page_id ) );

/* ------------------------------------------------------------------ */
/* 4. Static front page                                                */
/* ------------------------------------------------------------------ */
if ( 'page' !== get_option( 'show_on_front' ) ) {
	update_option( 'show_on_front', 'page' );
	WP_CLI::log( '✓ show_on_front → page' );
}
if ( (int) get_option( 'page_on_front' ) !== (int) $page_id ) {
	update_option( 'page_on_front', $page_id );
	WP_CLI::log( '✓ page_on_front → ' . $page_id );
}

WP_CLI::success( sprintf(
	"Portfolio ready. Front page: %s",
	get_permalink( $page_id )
) );
