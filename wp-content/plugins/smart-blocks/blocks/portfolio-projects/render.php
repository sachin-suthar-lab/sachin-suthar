<?php
/**
 * Portfolio Projects block — server render.
 */

use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'Selected work that shipped.';
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'Selected work';

$projects = apply_filters( 'smart_blocks/projects', [
	[
		'cat'   => 'WooCommerce',
		'title' => 'Subscription commerce platform',
		'desc'  => 'Custom subscription engine on WooCommerce powering a $4M ARR DTC brand. Dunning, proration, and a custom self-serve member portal.',
		'tags'  => [ 'WooCommerce', 'Stripe', 'Custom Plugin' ],
		'glyph' => 'WC',
		'cover' => 'linear-gradient(135deg, #7c5cff 0%, #f0abfc 100%)',
	],
	[
		'cat'   => 'Headless',
		'title' => 'Headless WP + Next.js marketing site',
		'desc'  => 'Editorial WordPress back-end powering a Next.js front-end with ISR, on-demand revalidation, and a custom block-to-component pipeline.',
		'tags'  => [ 'Next.js', 'GraphQL', 'ISR' ],
		'glyph' => 'NX',
		'cover' => 'linear-gradient(135deg, #11121a 0%, #22d3ee 130%)',
	],
	[
		'cat'   => 'Block Library',
		'title' => 'Agency-wide Gutenberg block library',
		'desc'  => 'A shared block library used across 30+ client sites: design tokens, accessibility baked in, ServerSideRender for editor parity.',
		'tags'  => [ 'Gutenberg', 'React', 'Design Tokens' ],
		'glyph' => 'GB',
		'cover' => 'linear-gradient(135deg, #34d399 0%, #22d3ee 100%)',
	],
	[
		'cat'   => 'Performance',
		'title' => 'Core Web Vitals rescue for a SaaS site',
		'desc'  => 'LCP from 4.8s → 1.2s, CLS from 0.34 → 0.02. Custom critical-CSS pipeline, image strategy, and DB query refactor.',
		'tags'  => [ 'Performance', 'CWV', 'DB Tuning' ],
		'glyph' => 'PF',
		'cover' => 'linear-gradient(135deg, #f0abfc 0%, #7c5cff 100%)',
	],
	[
		'cat'   => 'Custom Plugin',
		'title' => 'Real-estate listings & MLS sync',
		'desc'  => 'Custom listings plugin with nightly MLS RETS sync, map clustering, and saved-search emails. Handled 80k+ properties.',
		'tags'  => [ 'Custom Plugin', 'Cron', 'REST' ],
		'glyph' => 'RE',
		'cover' => 'linear-gradient(135deg, #22d3ee 0%, #7c5cff 100%)',
	],
	[
		'cat'   => 'Enterprise',
		'title' => 'Multi-site network migration',
		'desc'  => 'Migrated 14 country sites into a unified multisite network with shared theme.json, automated WP-CLI deploys, and an editorial workflow.',
		'tags'  => [ 'Multisite', 'WP-CLI', 'i18n' ],
		'glyph' => 'MS',
		'cover' => 'linear-gradient(135deg, #11121a 0%, #7c5cff 130%)',
	],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-projects ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'work' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>A snapshot of recent projects across e-commerce, headless, custom plugins, and performance work.</p>
	</div>

	<div class="ss-projects__grid" role="list">
		<?php foreach ( $projects as $p ) : ?>
			<article class="ss-project" role="listitem">
				<div class="ss-project__cover" style="--cover: <?php echo esc_attr( $p['cover'] ); ?>">
					<span class="ss-project__cover-glyph" aria-hidden="true"><?php echo esc_html( $p['glyph'] ); ?></span>
				</div>
				<div class="ss-project__body">
					<span class="ss-project__cat"><?php echo esc_html( $p['cat'] ); ?></span>
					<h3 class="ss-project__title"><?php echo esc_html( $p['title'] ); ?></h3>
					<p class="ss-project__desc"><?php echo esc_html( $p['desc'] ); ?></p>
					<div class="ss-project__tags">
						<?php foreach ( $p['tags'] as $t ) : ?>
							<span class="ss-tag"><?php echo esc_html( $t ); ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
