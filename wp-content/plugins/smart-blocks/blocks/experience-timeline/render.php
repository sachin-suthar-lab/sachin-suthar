<?php
/**
 * Experience Timeline block — server render.
 */

use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'A practical journey through the WordPress ecosystem.';
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'Experience';

$items = apply_filters( 'smart_blocks/experience', [
	[
		'period' => '2022 — Present',
		'role'   => 'Senior WordPress Engineer · Freelance',
		'org'    => 'SaaS, agencies & enterprise clients · Remote',
		'desc'   => 'Lead WordPress engineer on headless and FSE builds for SaaS marketing sites, agency clients, and enterprise CMS migrations. Architecting custom block libraries, performance-tuned WooCommerce stores, and CI-friendly WP-CLI workflows.',
		'tags'   => [ 'FSE Themes', 'Gutenberg Blocks', 'Headless', 'Performance' ],
	],
	[
		'period' => '2020 — 2022',
		'role'   => 'WordPress Tech Lead',
		'org'    => 'Digital product studio',
		'desc'   => 'Led a team of four building custom plugins and themes for venture-backed SaaS startups. Owned Gutenberg block architecture, code review standards, and release engineering. Delivered 40+ shipped products with measurable Core Web Vitals improvements.',
		'tags'   => [ 'Team Lead', 'Plugin Architecture', 'Code Review', 'CI/CD' ],
	],
	[
		'period' => '2018 — 2020',
		'role'   => 'Senior WordPress Developer',
		'org'    => 'Boutique agency · Remote / Hybrid',
		'desc'   => 'WooCommerce-heavy practice: custom checkout flows, subscription billing, ERP integrations, and multilingual storefronts. Migrated legacy classic-editor sites to Gutenberg with custom block parity.',
		'tags'   => [ 'WooCommerce', 'Custom Plugins', 'Migrations', 'ACF Pro' ],
	],
	[
		'period' => '2017 — 2018',
		'role'   => 'WordPress Developer',
		'org'    => 'Product-led startup',
		'desc'   => 'First in-house WordPress hire. Built the marketing platform, custom block library, REST integrations with the product app, and a publish workflow that took editors from idea to live in minutes.',
		'tags'   => [ 'Custom Themes', 'REST API', 'Editor UX' ],
	],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-experience ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'experience' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>Eight years of building WordPress products across SaaS, agencies, e-commerce, and enterprise CMS programmes.</p>
	</div>

	<ol class="ss-timeline">
		<?php foreach ( $items as $i ) : ?>
			<li class="ss-timeline__item">
				<div class="ss-timeline__dot" aria-hidden="true"></div>
				<span class="ss-timeline__period"><?php echo esc_html( $i['period'] ); ?></span>
				<h3 class="ss-timeline__role"><?php echo esc_html( $i['role'] ); ?></h3>
				<p class="ss-timeline__org"><?php echo esc_html( $i['org'] ); ?></p>
				<p class="ss-timeline__desc"><?php echo esc_html( $i['desc'] ); ?></p>
				<?php if ( ! empty( $i['tags'] ) ) : ?>
					<div class="ss-timeline__tags">
						<?php foreach ( $i['tags'] as $t ) : ?>
							<span class="ss-tag"><?php echo esc_html( $t ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
