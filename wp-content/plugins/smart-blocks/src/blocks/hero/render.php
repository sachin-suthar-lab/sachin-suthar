<?php
/**
 * Hero block — server render.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$badge      = $attributes['badge']            ?? '';
$badgeDot   = ! empty( $attributes['badgeDot'] );
$headline   = $attributes['headline']         ?? '';
$highlight  = $attributes['highlight']        ?? '';
$lede       = $attributes['lede']             ?? '';
$ctaText    = $attributes['ctaText']          ?? '';
$ctaUrl     = $attributes['ctaUrl']           ?? '#';
$cta2Text   = $attributes['ctaSecondaryText'] ?? '';
$cta2Url    = $attributes['ctaSecondaryUrl']  ?? '#';
$metrics    = is_array( $attributes['metrics'] ?? null ) ? $attributes['metrics'] : [];

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-section sb-hero sb-reveal' ] );
?>
<section <?php echo $wrapper; ?>>
	<div class="sb-hero__inner">
		<?php if ( $badge !== '' ) : ?>
			<span class="sb-hero__badge">
				<?php if ( $badgeDot ) : ?><span class="dot" aria-hidden="true"></span><?php endif; ?>
				<span><?php echo wp_kses_post( $badge ); ?></span>
			</span>
		<?php endif; ?>

		<?php if ( $headline !== '' || $highlight !== '' ) : ?>
			<h1>
				<?php echo wp_kses_post( $headline ); ?>
				<?php if ( $highlight !== '' ) : ?>
					<span class="sb-gradient"><?php echo wp_kses_post( $highlight ); ?></span>
				<?php endif; ?>
			</h1>
		<?php endif; ?>

		<?php if ( $lede !== '' ) : ?>
			<p class="sb-hero__lede"><?php echo wp_kses_post( $lede ); ?></p>
		<?php endif; ?>

		<?php if ( $ctaText || $cta2Text ) : ?>
			<div class="sb-hero__cta">
				<?php if ( $ctaText ) : ?>
					<a class="sb-btn sb-btn--primary" href="<?php echo esc_url( $ctaUrl ); ?>">
						<?php echo esc_html( $ctaText ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $cta2Text ) : ?>
					<a class="sb-btn sb-btn--ghost" href="<?php echo esc_url( $cta2Url ); ?>">
						<?php echo esc_html( $cta2Text ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $metrics ) ) : ?>
			<div class="sb-hero__metrics" role="list">
				<?php foreach ( $metrics as $m ) : ?>
					<div class="sb-metric" role="listitem">
						<strong><?php echo esc_html( $m['value'] ?? '' ); ?></strong>
						<span><?php echo esc_html( $m['label'] ?? '' ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
