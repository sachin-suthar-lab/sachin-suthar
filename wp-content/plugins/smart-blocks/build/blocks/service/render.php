<?php
/**
 * Service card — server render.
 *
 * @var array $attributes
 */
use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$icon_slug = $attributes['icon']  ?? 'spark';
$title     = $attributes['title'] ?? '';
$desc      = $attributes['desc']  ?? '';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-service' ] );
?>
<article <?php echo $wrapper; ?>>
	<div class="sb-service__icon"><?php echo icon( $icon_slug, 22 ); ?></div>
	<?php if ( $title !== '' ) : ?><h3 class="sb-service__title"><?php echo wp_kses_post( $title ); ?></h3><?php endif; ?>
	<?php if ( $desc !== ''  ) : ?><p class="sb-service__desc"><?php  echo wp_kses_post( $desc );  ?></p><?php endif; ?>
</article>
