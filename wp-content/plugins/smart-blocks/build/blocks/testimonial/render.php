<?php
/** Testimonial — server render. @var array $attributes */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$quote     = $attributes['quote']     ?? '';
$name      = $attributes['name']      ?? '';
$role      = $attributes['role']      ?? '';
$avatarUrl = $attributes['avatarUrl'] ?? '';

$initials = '';
$parts = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
if ( ! empty( $parts[0] ) ) {
	$initials .= mb_substr( $parts[0], 0, 1 );
	if ( ! empty( $parts[1] ) ) { $initials .= mb_substr( $parts[1], 0, 1 ); }
}
$initials = mb_strtoupper( $initials );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-testimonial sb-testimonials__slide' ] );
?>
<figure <?php echo $wrapper; ?>>
	<blockquote class="sb-testimonial__quote"><?php echo wp_kses_post( $quote ); ?></blockquote>
	<figcaption class="sb-testimonial__person">
		<span class="sb-testimonial__avatar" aria-hidden="true">
			<?php if ( $avatarUrl ) : ?>
				<img src="<?php echo esc_url( $avatarUrl ); ?>" alt="" />
			<?php else : ?>
				<?php echo esc_html( $initials ); ?>
			<?php endif; ?>
		</span>
		<span>
			<span class="sb-testimonial__name"><?php echo wp_kses_post( $name ); ?></span><br>
			<span class="sb-testimonial__role"><?php echo wp_kses_post( $role ); ?></span>
		</span>
	</figcaption>
</figure>
