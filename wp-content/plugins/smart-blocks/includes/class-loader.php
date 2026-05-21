<?php
/**
 * Smart Blocks loader (React/wp-scripts edition).
 *
 * Discovers every compiled block directory under /build/blocks, registers it
 * via block.json, and ensures the shared front-end stylesheet + reveal observer
 * are enqueued whenever any of our blocks renders.
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
		add_action( 'init',                 [ $this, 'register_blocks' ], 20 );
		add_action( 'init',                 [ $this, 'register_shared_assets' ] );
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

	/**
	 * Register the shared front-end stylesheet + reveal IO observer.
	 * These are referenced by every block's block.json `viewStyle` / `viewScript`
	 * (via the registered handles below), so they only load on pages that include
	 * one of our blocks.
	 */
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
		wp_register_script(
			'smart-blocks-reveal',
			SMART_BLOCKS_URL . 'assets/js/reveal.js',
			[],
			SMART_BLOCKS_VERSION,
			true
		);
	}

	/**
	 * Scans /build/blocks/* (compiled by wp-scripts) and registers each block.
	 * Falls back to src/blocks/* if no build directory exists yet so the plugin
	 * doesn't fatal-error during development before the first build.
	 */
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

		// Ensure shared front-end script loads whenever our blocks render.
		add_action( 'wp_enqueue_scripts', static function () {
			if ( wp_style_is( 'smart-blocks-shared', 'registered' ) ) {
				wp_enqueue_style( 'smart-blocks-shared' );
			}
			wp_enqueue_script( 'smart-blocks-reveal' );
		} );
	}
}
