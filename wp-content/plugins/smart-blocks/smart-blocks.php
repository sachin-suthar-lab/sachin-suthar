<?php
/**
 * Plugin Name:       Sachin's Blocks
 * Plugin URI:        https://sachinsuthar.dev
 * Update URI:        https://sachinsuthar.dev/plugins/sachins-blocks/
 * Description:       Custom portfolio Gutenberg blocks for the Sachin Suthar theme: Hero, About, Services, Skills, Experience, Tech Stack, Certifications, Education, Projects, Testimonials carousel, Blog Slider, Marquee, CTA, Contact — plus a custom contact-submissions table and built-in SMTP settings.
 * Version:           1.2.0
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

define( 'SMART_BLOCKS_VERSION', '1.2.0' );
define( 'SMART_BLOCKS_FILE',    __FILE__ );
define( 'SMART_BLOCKS_DIR',     plugin_dir_path( __FILE__ ) );
define( 'SMART_BLOCKS_URL',     plugin_dir_url( __FILE__ ) );

require_once SMART_BLOCKS_DIR . 'includes/helpers.php';
require_once SMART_BLOCKS_DIR . 'includes/class-submissions.php';
require_once SMART_BLOCKS_DIR . 'includes/class-mailer.php';
require_once SMART_BLOCKS_DIR . 'includes/class-loader.php';
require_once SMART_BLOCKS_DIR . 'includes/contact-handler.php';

// Admin classes only need to exist in wp-admin context.
if ( is_admin() ) {
	require_once SMART_BLOCKS_DIR . 'includes/class-submissions-list-table.php';
	require_once SMART_BLOCKS_DIR . 'includes/class-admin.php';
	add_action( 'plugins_loaded', [ \SmartBlocks\Admin::class, 'register' ] );
}

// SMTP hooks need to run on every request that triggers wp_mail (admin + cron).
add_action( 'plugins_loaded', [ \SmartBlocks\Mailer::class, 'register' ] );

// Block registration loader.
add_action( 'plugins_loaded', [ \SmartBlocks\Loader::class, 'instance' ] );

// Create the submissions table on activation.
register_activation_hook( __FILE__, function () {
	require_once __DIR__ . '/includes/class-submissions.php';
	\SmartBlocks\Submissions::maybe_install();
} );
