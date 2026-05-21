<?php
/**
 * Smart Blocks loader (pure static-save edition).
 *
 * Registers each compiled block via block.json, wires up the shared front-end
 * stylesheet and reveal observer, and localises the contact-form view script
 * with a fresh REST nonce + endpoint URL.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Loader {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init',                 [ $this, 'register_shared_assets' ] );
		add_action( 'init',                 [ $this, 'register_blocks' ], 20 );
		add_action( 'wp_enqueue_scripts',   [ $this, 'enqueue_shared' ] );
		add_action( 'wp_enqueue_scripts',   [ $this, 'localize_contact' ], 20 );
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
			wp_register_style(
				'smart-blocks-shared',
				SMART_BLOCKS_URL . 'build/shared.css',
				[],
				filemtime( $shared_css )
			);
		}
		$reveal_js = SMART_BLOCKS_DIR . 'assets/js/reveal.js';
		wp_register_script(
			'smart-blocks-reveal',
			SMART_BLOCKS_URL . 'assets/js/reveal.js',
			[],
			file_exists( $reveal_js ) ? filemtime( $reveal_js ) : SMART_BLOCKS_VERSION,
			true
		);
	}

	public function register_blocks(): void {
		$build_dir = SMART_BLOCKS_DIR . 'build/blocks';
		$src_dir   = SMART_BLOCKS_DIR . 'src/blocks';
		$root      = is_dir( $build_dir ) ? $build_dir : $src_dir;
		if ( ! is_dir( $root ) ) {
			return;
		}
		foreach ( glob( $root . '/*', GLOB_ONLYDIR ) as $block_path ) {
			$json = $block_path . '/block.json';
			if ( file_exists( $json ) ) {
				register_block_type( $json );
			}
		}
	}

	/**
	 * Ensure shared front-end CSS + reveal script are always available.
	 * Cheap — both are tiny.
	 */
	public function enqueue_shared(): void {
		if ( wp_style_is( 'smart-blocks-shared', 'registered' ) ) {
			wp_enqueue_style( 'smart-blocks-shared' );
		}
		wp_enqueue_script( 'smart-blocks-reveal' );
	}

	/**
	 * Pass the REST endpoint + a fresh nonce to the contact form's view.js.
	 * The handle name follows the wp-scripts convention: <block-name>-view-script.
	 */
	public function localize_contact(): void {
		$handle = 'smart-blocks-contact-section-view-script';
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			return;
		}
		wp_localize_script( $handle, 'SmartBlocksContact', [
			'endpoint' => rest_url( 'smart-blocks/v1/contact' ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
		] );
	}
}
