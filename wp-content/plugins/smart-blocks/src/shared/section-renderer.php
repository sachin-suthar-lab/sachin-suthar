<?php
/**
 * Shared parent-section renderer.
 *
 * Render a "section parent" block: section wrapper + container + heading + inner blocks.
 * Called from each parent block's render.php so the markup stays consistent.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks\Render;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * @param array     $attributes        Block attributes (eyebrow, heading, dek).
 * @param \WP_Block $block             The WP_Block instance for child rendering.
 * @param string    $section_class     Section-specific class (e.g. 'sb-services').
 * @param string    $inner_tag         The HTML tag wrapping inner blocks ('div', 'ol').
 * @param string    $inner_class       Class on inner-blocks wrapper (e.g. 'sb-services__grid').
 * @param string    $extra_section_cls Optional additional class on the <section>.
 * @param string    $pre_inner_html    Optional HTML emitted before the inner wrapper (carousel chrome, etc.).
 * @param string    $post_inner_html   Optional HTML emitted after the inner wrapper.
 */
function section( array $attributes, \WP_Block $block, string $section_class, string $inner_tag = 'div', string $inner_class = '', string $extra_section_cls = '', string $pre_inner_html = '', string $post_inner_html = '' ): string {
	$eyebrow = $attributes['eyebrow'] ?? '';
	$heading = $attributes['heading'] ?? '';
	$dek     = $attributes['dek']     ?? '';

	$inner_html = '';
	if ( ! empty( $block->parsed_block['innerBlocks'] ) ) {
		foreach ( $block->parsed_block['innerBlocks'] as $child ) {
			$inner_html .= render_block( $child );
		}
	}

	$wrapper_cls = trim( 'sb-section ' . $section_class . ' ' . $extra_section_cls . ' sb-reveal' );
	$wrapper     = get_block_wrapper_attributes( [ 'class' => $wrapper_cls ] );

	ob_start();
	?>
	<section <?php echo $wrapper; ?>>
		<div class="sb-container">
			<?php if ( $eyebrow !== '' || $heading !== '' || $dek !== '' ) : ?>
				<div class="sb-section-head">
					<?php if ( $eyebrow !== '' ) : ?><span class="sb-eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></span><?php endif; ?>
					<?php if ( $heading !== '' ) : ?><h2><?php echo wp_kses_post( $heading ); ?></h2><?php endif; ?>
					<?php if ( $dek !== '' )     : ?><p><?php       echo wp_kses_post( $dek );     ?></p><?php endif; ?>
				</div>
			<?php endif; ?>

			<?php echo $pre_inner_html; ?>

			<?php if ( $inner_class !== '' ) : ?>
				<<?php echo esc_attr( $inner_tag ); ?> class="<?php echo esc_attr( $inner_class ); ?>"><?php echo $inner_html; ?></<?php echo esc_attr( $inner_tag ); ?>>
			<?php else : ?>
				<?php echo $inner_html; ?>
			<?php endif; ?>

			<?php echo $post_inner_html; ?>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}
