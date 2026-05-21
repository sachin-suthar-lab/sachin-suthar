<?php
/**
 * Services Grid block — server render.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

use function SmartBlocks\Helpers\icon;
use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'Services tuned for ambitious WordPress products.';
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'What I do';

$services = apply_filters( 'smart_blocks/services', [
	[ 'icon' => 'wp',       'title' => 'Custom WordPress Development', 'desc' => 'Custom themes, plugins, and bespoke admin tools — architected for long-term maintainability and zero technical debt.' ],
	[ 'icon' => 'layers',   'title' => 'Gutenberg Block Development',  'desc' => 'Production-grade native and dynamic blocks with React, block.json, server-side rendering, and editor parity.' ],
	[ 'icon' => 'cart',     'title' => 'WooCommerce Engineering',      'desc' => 'High-conversion stores, custom checkout flows, subscription logic, and ERP / payment gateway integrations.' ],
	[ 'icon' => 'plug',     'title' => 'Headless WordPress',           'desc' => 'WordPress as a content API powering Next.js, React, and mobile front-ends, with auth, ISR, and caching done right.' ],
	[ 'icon' => 'gauge',    'title' => 'Performance Optimisation',     'desc' => 'Core Web Vitals deep-dives, query profiling, asset budgets, and a measurable path to 90+ Lighthouse on real hardware.' ],
	[ 'icon' => 'cube',     'title' => 'Elementor & Bricks',           'desc' => 'Custom widgets, dynamic data, and clean visual builder workflows that ship designer intent without bloat.' ],
	[ 'icon' => 'rest',     'title' => 'API Integrations',             'desc' => 'REST, GraphQL, and webhook bridges to CRMs, payment processors, mailers, and internal back-office systems.' ],
	[ 'icon' => 'box',      'title' => 'Advanced Custom Fields',       'desc' => 'Modular content models, ACF Pro field groups, and editor experiences that let marketing ship without engineering.' ],
	[ 'icon' => 'terminal', 'title' => 'WP-CLI Automation',            'desc' => 'Idempotent CLI scripts for migrations, content imports, environment setup, and CI/CD-friendly deploys.' ],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-services ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'services' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>Specialist services across the modern WordPress stack — from custom block development to headless architectures and performance engineering.</p>
	</div>

	<div class="ss-services__grid" role="list">
		<?php foreach ( $services as $s ) : ?>
			<article class="ss-service-card" role="listitem">
				<div class="ss-service-card__icon"><?php echo icon( $s['icon'], 22 ); ?></div>
				<h3><?php echo esc_html( $s['title'] ); ?></h3>
				<p><?php echo esc_html( $s['desc'] ); ?></p>
			</article>
		<?php endforeach; ?>
	</div>
</section>
