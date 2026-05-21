<?php
/** Project Card — server render. @var array $attributes */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$cat      = $attributes['cat']      ?? '';
$title    = $attributes['title']    ?? '';
$desc     = $attributes['desc']     ?? '';
$glyph    = $attributes['glyph']    ?? '';
$gradient = $attributes['gradient'] ?? 'linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%)';
$coverUrl = $attributes['coverUrl'] ?? '';
$coverAlt = $attributes['coverAlt'] ?? '';
$url      = $attributes['url']      ?? '';
$tags     = is_array( $attributes['tags'] ?? null ) ? $attributes['tags'] : [];

$cover_style = $coverUrl
	? sprintf( '--cover:url(%s);background-image:url(%s);', esc_url( $coverUrl ), esc_url( $coverUrl ) )
	: sprintf( '--cover:%s;', esc_attr( $gradient ) );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-project' ] );
?>
<article <?php echo $wrapper; ?>>
	<div class="sb-project__cover" style="<?php echo $cover_style; ?>" <?php if ( $coverUrl ) : ?>role="img" aria-label="<?php echo esc_attr( $coverAlt ); ?>"<?php endif; ?>>
		<?php if ( ! $coverUrl && $glyph !== '' ) : ?>
			<span class="sb-project__cover-glyph"><?php echo esc_html( $glyph ); ?></span>
		<?php endif; ?>
	</div>
	<div class="sb-project__body">
		<?php if ( $cat   !== '' ) : ?><span class="sb-project__cat"><?php echo wp_kses_post( $cat ); ?></span><?php endif; ?>
		<?php if ( $title !== '' ) : ?>
			<h3 class="sb-project__title">
				<?php if ( $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>" rel="noopener" target="_blank"><?php echo wp_kses_post( $title ); ?></a>
				<?php else : ?>
					<?php echo wp_kses_post( $title ); ?>
				<?php endif; ?>
			</h3>
		<?php endif; ?>
		<?php if ( $desc  !== '' ) : ?><p class="sb-project__desc"><?php echo wp_kses_post( $desc ); ?></p><?php endif; ?>
		<?php if ( ! empty( $tags ) ) : ?>
			<div class="sb-project__tags">
				<?php foreach ( $tags as $t ) : ?><span class="sb-tag"><?php echo esc_html( $t ); ?></span><?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
