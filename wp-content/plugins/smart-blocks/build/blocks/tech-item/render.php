<?php
/**
 * Tech Item — server render.
 *
 * @var array $attributes
 */
use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$icon_slug = $attributes['icon'] ?? 'spark';
$name      = $attributes['name'] ?? '';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-tech-card' ] );
?>
<div <?php echo $wrapper; ?> title="<?php echo esc_attr( wp_strip_all_tags( $name ) ); ?>">
	<div class="sb-tech-card__icon"><?php echo icon( $icon_slug, 28 ); ?></div>
	<div class="sb-tech-card__name"><?php echo wp_kses_post( $name ); ?></div>
</div>
