<?php
/** Testimonials — server render (carousel chrome on front-end). @var array $attributes @var \WP_Block $block */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Carousel chrome wraps the inner blocks. view.js wires nav buttons + dots + drag.
ob_start();
?>
<div class="sb-testimonials__carousel" data-sb-carousel>
	<div class="sb-testimonials__viewport">
		<div class="sb-testimonials__track">
<?php
$pre_inner = ob_get_clean();

ob_start();
?>
		</div>
	</div>
	<div class="sb-carousel-controls">
		<div class="sb-carousel-dots" data-sb-dots aria-label="Slide selector"></div>
		<div class="sb-carousel-nav">
			<button type="button" data-sb-prev aria-label="Previous testimonials">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
			</button>
			<button type="button" data-sb-next aria-label="Next testimonials">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
			</button>
		</div>
	</div>
</div>
<?php
$post_inner = ob_get_clean();

echo \SmartBlocks\Render\section(
	$attributes ?? [],
	$block,
	'sb-testimonials',
	'div',
	'',                 // we manage the inner wrapper ourselves above
	'sb-section--alt',
	$pre_inner,
	$post_inner
);
