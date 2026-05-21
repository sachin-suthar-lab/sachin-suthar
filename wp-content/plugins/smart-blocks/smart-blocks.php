<?php
/**
 * Plugin Name:       Smart Blocks
 * Plugin URI:        https://sachinsuthar.dev
 * Description:       Premium portfolio-section Gutenberg blocks (Hero, Services, Skills, Experience, Tech Stack, Projects, Testimonials, CTA, Contact). Pairs with the Sachin Suthar theme.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Sachin Suthar
 * Author URI:        https://sachinsuthar.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-blocks
 *
 * @package SmartBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMART_BLOCKS_VERSION', '1.0.0' );
define( 'SMART_BLOCKS_FILE',    __FILE__ );
define( 'SMART_BLOCKS_DIR',     plugin_dir_path( __FILE__ ) );
define( 'SMART_BLOCKS_URL',     plugin_dir_url( __FILE__ ) );

require_once SMART_BLOCKS_DIR . 'includes/helpers.php';
require_once SMART_BLOCKS_DIR . 'src/shared/section-renderer.php';
require_once SMART_BLOCKS_DIR . 'includes/class-loader.php';
require_once SMART_BLOCKS_DIR . 'includes/contact-handler.php';

add_action( 'plugins_loaded', [ \SmartBlocks\Loader::class, 'instance' ] );
