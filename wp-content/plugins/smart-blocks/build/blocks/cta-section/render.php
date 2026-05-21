<?php
/** CTA Section — server render. @var array $attributes */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$heading     = $attributes['heading']       ?? '';
$dek         = $attributes['dek']           ?? '';
$primaryText = $attributes['primaryText']   ?? '';
$primaryUrl  = $attributes['primaryUrl']    ?? '#';
$secondaryText = $attributes['secondaryText'] ?? '';
$secondaryUrl  = $attributes['secondaryUrl']  ?? '#';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-section sb-cta-wrap sb-section--cream sb-reveal' ] );
?>
<section <?php echo $wrapper; ?>>
	<div class="sb-container">
		<div class="sb-cta">
			<?php if ( $heading !== '' ) : ?><h2><?php echo wp_kses_post( $heading ); ?></h2><?php endif; ?>
			<?php if ( $dek !== '' )     : ?><p><?php   echo wp_kses_post( $dek );    ?></p><?php endif; ?>
			<div class="sb-cta__buttons">
				<?php if ( $primaryText )   : ?><a class="sb-btn sb-btn--primary" href="<?php echo esc_url( $primaryUrl );   ?>"><?php echo esc_html( $primaryText );   ?></a><?php endif; ?>
				<?php if ( $secondaryText ) : ?><a class="sb-btn sb-btn--ghost"   href="<?php echo esc_url( $secondaryUrl ); ?>"><?php echo esc_html( $secondaryText ); ?></a><?php endif; ?>
			</div>
		</div>
	</div>
</section>
