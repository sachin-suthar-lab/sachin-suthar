<?php
/** Timeline Item — server render. @var array $attributes */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$period = $attributes['period'] ?? '';
$role   = $attributes['role']   ?? '';
$org    = $attributes['org']    ?? '';
$desc   = $attributes['desc']   ?? '';
$tags   = is_array( $attributes['tags'] ?? null ) ? $attributes['tags'] : [];

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-timeline-item' ] );
?>
<li <?php echo $wrapper; ?>>
	<div class="sb-timeline-item__dot" aria-hidden="true"></div>
	<?php if ( $period ) : ?><span class="sb-timeline-item__period"><?php echo wp_kses_post( $period ); ?></span><?php endif; ?>
	<?php if ( $role )   : ?><h3   class="sb-timeline-item__role"><?php   echo wp_kses_post( $role );   ?></h3><?php   endif; ?>
	<?php if ( $org )    : ?><p    class="sb-timeline-item__org"><?php    echo wp_kses_post( $org );    ?></p><?php    endif; ?>
	<?php if ( $desc )   : ?><p    class="sb-timeline-item__desc"><?php   echo wp_kses_post( $desc );   ?></p><?php   endif; ?>
	<?php if ( ! empty( $tags ) ) : ?>
		<div class="sb-timeline-item__tags">
			<?php foreach ( $tags as $t ) : ?><span class="sb-tag"><?php echo esc_html( $t ); ?></span><?php endforeach; ?>
		</div>
	<?php endif; ?>
</li>
