<?php
/**
 * Skill — server render.
 *
 * @var array $attributes
 */
use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$icon_slug = $attributes['icon'] ?? '';
$name      = $attributes['name'] ?? '';
$pct       = max( 0, min( 100, (int) ( $attributes['proficiency'] ?? 0 ) ) );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-skill' ] );
?>
<div <?php echo $wrapper; ?>>
	<div class="sb-skill__head">
		<span class="sb-skill__name">
			<?php if ( $icon_slug ) : ?>
				<span class="sb-skill__icon"><?php echo icon( $icon_slug, 18 ); ?></span>
			<?php endif; ?>
			<span><?php echo wp_kses_post( $name ); ?></span>
		</span>
		<span class="sb-skill__pct"><?php echo $pct; ?>%</span>
	</div>
	<div class="sb-skill__bar" role="progressbar" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php echo esc_attr( wp_strip_all_tags( $name ) ); ?>">
		<div class="sb-skill__fill" style="width:<?php echo $pct; ?>%"></div>
	</div>
</div>
