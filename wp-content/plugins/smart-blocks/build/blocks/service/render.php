<?php
/**
 * Service card — server render.
 *
 * @var array $attributes
 */
use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$icon_slug = $attributes['icon']       ?? 'spark';
$title     = $attributes['title']      ?? '';
$desc      = $attributes['desc']       ?? '';
$showBar   = ! empty( $attributes['showBar'] );
$barLabel  = $attributes['barLabel']   ?? 'Expertise';
$pct       = max( 0, min( 100, (int) ( $attributes['proficiency'] ?? 0 ) ) );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-service' ] );
?>
<article <?php echo $wrapper; ?>>
	<div class="sb-service__head">
		<div class="sb-service__icon"><?php echo icon( $icon_slug, 22 ); ?></div>
		<?php if ( $title !== '' ) : ?>
			<h3 class="sb-service__title"><?php echo wp_kses_post( $title ); ?></h3>
		<?php endif; ?>
	</div>
	<?php if ( $desc !== '' ) : ?>
		<p class="sb-service__desc"><?php echo wp_kses_post( $desc ); ?></p>
	<?php endif; ?>
	<?php if ( $showBar ) : ?>
		<div class="sb-service__bar-wrap">
			<div class="sb-service__bar-head">
				<span class="sb-service__bar-label"><?php echo esc_html( $barLabel ); ?></span>
				<span class="sb-service__bar-pct"><?php echo $pct; ?>%</span>
			</div>
			<div class="sb-service__bar" role="progressbar" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php echo esc_attr( wp_strip_all_tags( $title ) . ' ' . $barLabel ); ?>">
				<div class="sb-service__bar-fill" style="width:<?php echo $pct; ?>%"></div>
			</div>
		</div>
	<?php endif; ?>
</article>
