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
$meta      = $attributes['meta'] ?? '';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-tech-card' ] );
?>
<div <?php echo $wrapper; ?>>
	<div class="sb-tech-card__icon"><?php echo icon( $icon_slug, 22 ); ?></div>
	<div class="sb-tech-card__body">
		<span class="sb-tech-card__name"><?php echo wp_kses_post( $name ); ?></span>
		<?php if ( $meta !== '' ) : ?>
			<span class="sb-tech-card__meta"><?php echo wp_kses_post( $meta ); ?></span>
		<?php endif; ?>
	</div>
</div>
