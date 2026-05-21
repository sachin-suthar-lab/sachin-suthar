<?php
/**
 * Smart Blocks loader.
 *
 * Discovers every block directory under /blocks, registers it via block.json,
 * and wires up the shared editor registration script + front-end assets.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Loader {

	private static ?self $instance = null;

	/** @var string[] Block slugs (without namespace) discovered on init. */
	private array $block_slugs = [];

	public static function instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init',                    [ $this, 'register_blocks' ], 20 );
		add_action( 'wp_enqueue_scripts',      [ $this, 'enqueue_frontend' ] );
		add_filter( 'block_categories_all',    [ $this, 'register_category' ], 10, 1 );
	}

	/**
	 * Adds a "Smart Blocks" category at the top of the block inserter.
	 */
	public function register_category( array $categories ): array {
		array_unshift( $categories, [
			'slug'  => 'smart-blocks',
			'title' => __( 'Smart Blocks', 'smart-blocks' ),
			'icon'  => 'layout',
		] );
		return $categories;
	}

	/**
	 * Scans /blocks/* and registers each via block.json.
	 */
	public function register_blocks(): void {
		$blocks_dir = SMART_BLOCKS_DIR . 'blocks';
		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		// Shared editor script: registers every block with ServerSideRender.
		wp_register_script(
			'smart-blocks-editor',
			SMART_BLOCKS_URL . 'assets/js/register-blocks.js',
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n', 'wp-components' ],
			SMART_BLOCKS_VERSION,
			true
		);

		// Shared editor stylesheet (sidebar tweaks, block previews dark canvas).
		wp_register_style(
			'smart-blocks-editor',
			SMART_BLOCKS_URL . 'assets/css/editor.css',
			[],
			SMART_BLOCKS_VERSION
		);

		// Shared front-end style + reveal-on-scroll.
		wp_register_style(
			'smart-blocks-frontend',
			SMART_BLOCKS_URL . 'assets/css/blocks.css',
			[],
			SMART_BLOCKS_VERSION
		);
		wp_register_script(
			'smart-blocks-reveal',
			SMART_BLOCKS_URL . 'assets/js/reveal.js',
			[],
			SMART_BLOCKS_VERSION,
			true
		);

		foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $block_path ) {
			if ( ! file_exists( $block_path . '/block.json' ) ) {
				continue;
			}
			register_block_type( $block_path );
			$this->block_slugs[] = basename( $block_path );
		}

		// Pass the discovered block list to the shared editor script.
		wp_localize_script( 'smart-blocks-editor', 'SmartBlocksData', [
			'blocks' => array_map(
				static fn( string $slug ): string => 'smart-blocks/' . $slug,
				$this->block_slugs
			),
		] );
	}

	/**
	 * Front-end: enqueue shared CSS + reveal observer on every page that uses any of our blocks.
	 * To keep things simple and zero-config, we always load them — the CSS is ~6KB and the JS ~1KB.
	 */
	public function enqueue_frontend(): void {
		wp_enqueue_style(  'smart-blocks-frontend' );
		wp_enqueue_script( 'smart-blocks-reveal' );
	}
}
