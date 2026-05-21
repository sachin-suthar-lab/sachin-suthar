<?php
/**
 * Testimonials block — server render.
 */

use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'What collaborators say.';
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'Testimonials';

$quotes = apply_filters( 'smart_blocks/testimonials', [
	[
		'quote' => 'Sachin rebuilt our marketing site as an FSE theme and a small custom block library — the editor experience went from "open a ticket" to "ship it yourself" overnight.',
		'name'  => 'Priya Menon',
		'role'  => 'Head of Marketing · B2B SaaS',
		'init'  => 'PM',
	],
	[
		'quote' => 'We hired Sachin to fix a 4.8s LCP. Two weeks later we were under 1.5s on real devices, with cleaner code than we started with. Rare combination.',
		'name'  => 'David Lin',
		'role'  => 'CTO · DTC Commerce',
		'init'  => 'DL',
	],
	[
		'quote' => 'He thinks in architectures, not snippets. Our custom plugin survived two WordPress major releases without a single regression — that is the bar.',
		'name'  => 'Marta Hoffmann',
		'role'  => 'Engineering Manager · Agency',
		'init'  => 'MH',
	],
	[
		'quote' => 'Communication, code review, documentation — all senior-level. He raised the standard for the whole team while he was with us.',
		'name'  => 'Rahul Verma',
		'role'  => 'Founder · Product Studio',
		'init'  => 'RV',
	],
	[
		'quote' => 'The Gutenberg block library Sachin built is now reused across 30+ client sites. Real engineering, not just markup.',
		'name'  => 'Sofia Alvarez',
		'role'  => 'Tech Lead · Digital Agency',
		'init'  => 'SA',
	],
	[
		'quote' => 'Migrated 14 country sites into one multisite network with zero downtime and a faster editorial workflow on the other side. Quiet, calm, and exact.',
		'name'  => 'Jonas Berg',
		'role'  => 'Director of Web · Enterprise',
		'init'  => 'JB',
	],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-testimonials ss-reveal ss-section--alt' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'testimonials' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>Feedback from the people I have shipped with — founders, engineering leaders, and product teams.</p>
	</div>

	<div class="ss-testimonials__grid" role="list">
		<?php foreach ( $quotes as $q ) : ?>
			<figure class="ss-testimonial" role="listitem">
				<blockquote class="ss-testimonial__quote"><?php echo esc_html( $q['quote'] ); ?></blockquote>
				<figcaption class="ss-testimonial__person">
					<span class="ss-testimonial__avatar" aria-hidden="true"><?php echo esc_html( $q['init'] ); ?></span>
					<span>
						<span class="ss-testimonial__name"><?php echo esc_html( $q['name'] ); ?></span><br>
						<span class="ss-testimonial__role"><?php echo esc_html( $q['role'] ); ?></span>
					</span>
				</figcaption>
			</figure>
		<?php endforeach; ?>
	</div>
</section>
