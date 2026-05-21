<?php
/**
 * Sachin Suthar theme bootstrap.
 *
 * @package SachinSuthar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SACHIN_SUTHAR_VERSION' ) ) {
	define( 'SACHIN_SUTHAR_VERSION', '1.0.0' );
}

/**
 * Theme supports.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', [
		'height'      => 64,
		'width'       => 64,
		'flex-width'  => true,
		'flex-height' => true,
	] );

	add_editor_style( 'assets/css/editor.css' );

	load_theme_textdomain( 'sachin-suthar', get_template_directory() . '/languages' );
} );

/**
 * Front-end assets.
 */
add_action( 'wp_enqueue_scripts', function () {
	$style_path = get_template_directory() . '/assets/css/theme.css';
	wp_enqueue_style(
		'sachin-suthar-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		[],
		file_exists( $style_path ) ? filemtime( $style_path ) : SACHIN_SUTHAR_VERSION
	);

	// Preconnect for Google Fonts fallback if local font missing.
	add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
		}
		return $urls;
	}, 10, 2 );
} );

/**
 * SEO basics: meta description from excerpt / tagline.
 */
add_action( 'wp_head', function () {
	if ( is_singular() ) {
		$desc = wp_strip_all_tags( get_the_excerpt() );
	} else {
		$desc = get_bloginfo( 'description' );
	}
	$desc = trim( preg_replace( '/\s+/', ' ', $desc ) );
	if ( '' === $desc ) {
		return;
	}
	printf(
		"<meta name=\"description\" content=\"%s\" />\n",
		esc_attr( wp_html_excerpt( $desc, 160, '…' ) )
	);
}, 1 );

/**
 * Register reusable block pattern categories.
 */
add_action( 'init', function () {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}
	register_block_pattern_category( 'sachin-suthar', [
		'label'       => __( 'Sachin Suthar', 'sachin-suthar' ),
		'description' => __( 'Patterns for the Sachin Suthar portfolio theme.', 'sachin-suthar' ),
	] );
} );

/**
 * Disable comments globally on the portfolio (single-author site, no engagement surface).
 */
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open',    '__return_false', 20, 2 );

/**
 * Speed: remove emoji script bloat.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
