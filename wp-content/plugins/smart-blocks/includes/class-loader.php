<?php
/**
 * Smart Blocks loader.
 *
 * Registers each compiled block via block.json, wires up shared front-end
 * assets, force-enqueues viewScripts for static-save blocks that need
 * interactivity (testimonials carousel, contact AJAX), and localises the
 * contact form with a REST nonce + endpoint URL.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Loader {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( ! self::$instance ) self::$instance = new self();
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init',                 [ $this, 'register_shared_assets' ] );
		add_action( 'init',                 [ $this, 'register_blocks' ], 20 );
		add_action( 'wp_enqueue_scripts',   [ $this, 'enqueue_shared' ] );
		add_action( 'wp_enqueue_scripts',   [ $this, 'enqueue_view_scripts' ], 20 );
		add_filter( 'block_categories_all', [ $this, 'register_category' ] );
	}

	public function register_category( array $categories ): array {
		array_unshift( $categories, [
			'slug'  => 'smart-blocks',
			'title' => __( 'Smart Blocks', 'smart-blocks' ),
			'icon'  => 'layout',
		] );
		return $categories;
	}

	public function register_shared_assets(): void {
		$shared_css = SMART_BLOCKS_DIR . 'build/shared.css';
		if ( file_exists( $shared_css ) ) {
			wp_register_style( 'smart-blocks-shared', SMART_BLOCKS_URL . 'build/shared.css', [], filemtime( $shared_css ) );
		}
		$reveal_js = SMART_BLOCKS_DIR . 'assets/js/reveal.js';
		wp_register_script( 'smart-blocks-reveal', SMART_BLOCKS_URL . 'assets/js/reveal.js', [], file_exists( $reveal_js ) ? filemtime( $reveal_js ) : SMART_BLOCKS_VERSION, true );

		$carousel_js = SMART_BLOCKS_DIR . 'assets/js/carousel.js';
		wp_register_script( 'smart-blocks-carousel', SMART_BLOCKS_URL . 'assets/js/carousel.js', [], file_exists( $carousel_js ) ? filemtime( $carousel_js ) : SMART_BLOCKS_VERSION, true );
	}

	public function register_blocks(): void {
		$build_dir = SMART_BLOCKS_DIR . 'build/blocks';
		$src_dir   = SMART_BLOCKS_DIR . 'src/blocks';
		$root      = is_dir( $build_dir ) ? $build_dir : $src_dir;
		if ( ! is_dir( $root ) ) return;
		foreach ( glob( $root . '/*', GLOB_ONLYDIR ) as $block_path ) {
			$json = $block_path . '/block.json';
			if ( file_exists( $json ) ) register_block_type( $json );
		}
	}

	public function enqueue_shared(): void {
		if ( wp_style_is( 'smart-blocks-shared', 'registered' ) ) wp_enqueue_style( 'smart-blocks-shared' );
		wp_enqueue_script( 'smart-blocks-reveal' );
	}

	/**
	 * WordPress doesn't auto-enqueue viewScript for static-save blocks (the
	 * mechanism fires from inside WP_Block::render(), which static-save skips).
	 * We force-enqueue the handles for blocks that actually appear in the
	 * current request's post content, and localise the contact endpoint+nonce
	 * onto its view script.
	 */
	public function enqueue_view_scripts(): void {
		if ( is_admin() ) return;
		$post = get_post();
		if ( ! $post ) return;
		$content = $post->post_content;

		$blocks_with_view = [
			'smart-blocks/testimonials'    => 'smart-blocks-carousel',
			'smart-blocks/blog-slider'     => 'smart-blocks-carousel',
			'smart-blocks/contact-section' => 'smart-blocks-contact-section-view-script',
		];
		foreach ( $blocks_with_view as $block_name => $handle ) {
			if ( has_block( $block_name, $post ) && wp_script_is( $handle, 'registered' ) ) {
				wp_enqueue_script( $handle );
			}
		}

		// Contact form REST endpoint + nonce.
		$contact_handle = 'smart-blocks-contact-section-view-script';
		if ( wp_script_is( $contact_handle, 'enqueued' ) ) {
			wp_localize_script( $contact_handle, 'SmartBlocksContact', [
				'endpoint' => rest_url( 'smart-blocks/v1/contact' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			] );
		}
	}
}
