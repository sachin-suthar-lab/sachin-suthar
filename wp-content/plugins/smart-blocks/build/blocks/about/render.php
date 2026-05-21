<?php
/** About — server render. @var array $attributes */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$eyebrow    = $attributes['eyebrow']  ?? '';
$heading    = $attributes['heading']  ?? '';
$body       = $attributes['body']     ?? '';
$imageUrl   = $attributes['imageUrl'] ?? '';
$imageAlt   = $attributes['imageAlt'] ?? '';
$highlights = is_array( $attributes['highlights'] ?? null ) ? $attributes['highlights'] : [];

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-section sb-about sb-reveal' ] );
?>
<section <?php echo $wrapper; ?>>
	<div class="sb-container">
		<div class="sb-about__layout">
			<div class="sb-about__visual">
				<div class="sb-image-slot">
					<?php if ( $imageUrl ) : ?>
						<img src="<?php echo esc_url( $imageUrl ); ?>" alt="<?php echo esc_attr( $imageAlt ); ?>" loading="lazy" />
					<?php else : ?>
						<span class="sb-image-slot__label">Image goes here</span>
					<?php endif; ?>
				</div>
			</div>
			<div class="sb-about__copy">
				<?php if ( $eyebrow !== '' ) : ?><span class="sb-eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></span><?php endif; ?>
				<?php if ( $heading !== '' ) : ?><h2><?php echo wp_kses_post( $heading ); ?></h2><?php endif; ?>
				<?php if ( $body !== '' )    : ?><p><?php echo wp_kses_post( $body ); ?></p><?php endif; ?>
				<?php if ( ! empty( $highlights ) ) : ?>
					<div class="sb-about__highlights">
						<?php foreach ( $highlights as $h ) : ?>
							<div class="sb-about__highlight">
								<strong><?php echo esc_html( $h['title'] ?? '' ); ?></strong>
								<span><?php echo esc_html( $h['meta'] ?? '' ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
