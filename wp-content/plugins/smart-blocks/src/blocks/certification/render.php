<?php
/** Certification — server render. @var array $attributes */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$title  = $attributes['title']  ?? '';
$issuer = $attributes['issuer'] ?? '';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-cert' ] );
?>
<div <?php echo $wrapper; ?>>
	<span class="sb-cert__badge">
		<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
	</span>
	<?php if ( $title !== '' )  : ?><h3 class="sb-cert__title"><?php echo wp_kses_post( $title ); ?></h3><?php endif; ?>
	<?php if ( $issuer !== '' ) : ?><span class="sb-cert__issuer"><?php echo wp_kses_post( $issuer ); ?></span><?php endif; ?>
</div>
