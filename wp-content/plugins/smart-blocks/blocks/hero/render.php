<?php
/**
 * Hero block — server render.
 *
 * @var array $attributes
 * @var string $content
 * @var WP_Block $block
 */

use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] )
	? $attributes['headingOverride']
	: 'WordPress development that feels engineered, not assembled.';

$eyebrow = ! empty( $attributes['eyebrowOverride'] )
	? $attributes['eyebrowOverride']
	: 'Senior WordPress Developer · 8+ years';

$metrics = apply_filters( 'smart_blocks/hero/metrics', [
	[ 'value' => '8+',   'label' => 'Years building scalable WordPress' ],
	[ 'value' => '120+', 'label' => 'Custom themes & plugins shipped' ],
	[ 'value' => '40+',  'label' => 'Gutenberg blocks engineered' ],
	[ 'value' => '99',   'label' => 'Median Lighthouse performance' ],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-hero ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'home' ); ?>">
	<div class="ss-hero__inner">
		<span class="ss-hero__badge"><span class="dot" aria-hidden="true"></span><?php echo esc_html( $eyebrow ); ?></span>

		<h1>
			<?php
			// Split heading on last sentence for a gradient highlight on the closing line.
			$parts = preg_split( '/(?<=[.!?])\s+/', $heading, 2 );
			if ( count( $parts ) === 2 ) {
				echo esc_html( $parts[0] ) . ' <span class="ss-gradient">' . esc_html( $parts[1] ) . '</span>';
			} else {
				echo esc_html( $heading );
			}
			?>
		</h1>

		<p class="ss-hero__lede">
			I'm Sachin Suthar — a senior WordPress developer specialising in custom themes, Gutenberg block development,
			WooCommerce, and headless architectures. I help teams ship fast, accessible, and maintainable WordPress products.
		</p>

		<div class="ss-hero__cta">
			<a class="ss-btn ss-btn--primary" href="#contact">
				Start a project <?php echo icon( 'arrow', 18 ); ?>
			</a>
			<a class="ss-btn ss-btn--ghost" href="#work">
				View selected work
			</a>
		</div>

		<div class="ss-hero__metrics" role="list">
			<?php foreach ( $metrics as $m ) : ?>
				<div class="ss-metric" role="listitem">
					<strong><?php echo esc_html( $m['value'] ); ?></strong>
					<span><?php echo esc_html( $m['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
