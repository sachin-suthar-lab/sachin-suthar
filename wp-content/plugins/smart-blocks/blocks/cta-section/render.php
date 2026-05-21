<?php
/**
 * CTA Section block — server render.
 */

use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'Got a WordPress build that needs a senior pair of hands?';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-cta-wrap ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'cta' ); ?>">
	<div class="ss-cta">
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>Let's talk about your roadmap, your existing stack, and the constraints that make your project actually hard. I'll come back with a clear plan and a realistic estimate.</p>
		<div class="ss-cta__buttons">
			<a class="ss-btn ss-btn--primary" href="#contact">
				Book a discovery call <?php echo icon( 'arrow', 18 ); ?>
			</a>
			<a class="ss-btn ss-btn--ghost" href="mailto:hello@sachinsuthar.dev">
				<?php echo icon( 'mail', 16 ); ?> Email me directly
			</a>
		</div>
	</div>
</section>
