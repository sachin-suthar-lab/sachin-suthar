<?php
/**
 * Plugin Name:       Sachin's Blocks
 * Plugin URI:        https://sachinsuthar.dev
 * Update URI:        https://sachinsuthar.dev/plugins/sachins-blocks/
 * Description:       Custom portfolio Gutenberg blocks for the Sachin Suthar theme: Hero, About, Services, Skills, Experience, Tech Stack, Certifications, Education, Projects, Testimonials carousel, Blog Slider, Marquee, CTA, Contact.
 * Version:           1.1.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Sachin Suthar
 * Author URI:        https://sachinsuthar.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sachins-blocks
 *
 * @package SachinsBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMART_BLOCKS_VERSION', '1.0.0' );
define( 'SMART_BLOCKS_FILE',    __FILE__ );
define( 'SMART_BLOCKS_DIR',     plugin_dir_path( __FILE__ ) );
define( 'SMART_BLOCKS_URL',     plugin_dir_url( __FILE__ ) );

require_once SMART_BLOCKS_DIR . 'includes/helpers.php';
require_once SMART_BLOCKS_DIR . 'includes/class-loader.php';
require_once SMART_BLOCKS_DIR . 'includes/contact-handler.php';

add_action( 'plugins_loaded', [ \SmartBlocks\Loader::class, 'instance' ] );
