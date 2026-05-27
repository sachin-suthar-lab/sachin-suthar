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

	add_editor_style( [ 'assets/fonts/fonts.css', 'assets/css/editor.css' ] );

	load_theme_textdomain( 'sachin-suthar', get_template_directory() . '/languages' );
} );

/**
 * Front-end assets.
 */
add_action( 'wp_enqueue_scripts', function () {
	// Self-hosted Fraunces (display serif) + Inter (UI body) + JetBrains Mono
	// (metadata). Files + @font-face live in assets/fonts/ — regenerate with
	// `node tools/fetch-fonts.mjs`.
	$fonts_path = get_template_directory() . '/assets/fonts/fonts.css';
	wp_enqueue_style(
		'sachin-suthar-fonts',
		get_template_directory_uri() . '/assets/fonts/fonts.css',
		[],
		file_exists( $fonts_path ) ? filemtime( $fonts_path ) : SACHIN_SUTHAR_VERSION
	);

	$style_path = get_template_directory() . '/assets/css/theme.css';
	wp_enqueue_style(
		'sachin-suthar-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		[ 'sachin-suthar-fonts' ],
		file_exists( $style_path ) ? filemtime( $style_path ) : SACHIN_SUTHAR_VERSION
	);
} );

/**
 * Preload the primary (latin) webfont files so first paint isn't blocked on
 * the stylesheet → font request chain.
 */
add_action( 'wp_head', function () {
	$base = get_template_directory_uri() . '/assets/fonts/';
	$preload = [ 'inter-latin.woff2', 'fraunces-latin.woff2' ];
	foreach ( $preload as $file ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
			esc_url( $base . $file )
		);
	}
}, 2 );

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
