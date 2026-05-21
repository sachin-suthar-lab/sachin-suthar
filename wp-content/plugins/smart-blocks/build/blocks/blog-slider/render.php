<?php
/**
 * Blog Slider — dynamic render.
 *
 * Queries the latest published posts and emits a carousel-ready markup.
 * Dynamic rendering is the only way to surface live posts in a block, the
 * same architecture core/latest-posts uses.
 *
 * @var array $attributes
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$eyebrow      = $attributes['eyebrow']      ?? '';
$heading      = $attributes['heading']      ?? '';
$dek          = $attributes['dek']          ?? '';
$postsToShow  = max( 2, min( 12, (int) ( $attributes['postsToShow'] ?? 5 ) ) );
$categorySlug = $attributes['categorySlug'] ?? '';
$showDate     = ! empty( $attributes['showDate'] );
$showExcerpt  = ! empty( $attributes['showExcerpt'] );

$args = [
	'post_type'           => 'post',
	'posts_per_page'      => $postsToShow,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => true,
];
if ( $categorySlug ) {
	$args['category_name'] = sanitize_title( $categorySlug );
}
$q = new WP_Query( $args );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-section sb-blog sb-reveal' ] );
?>
<section <?php echo $wrapper; ?>>
	<div class="sb-container">
		<div class="sb-section-head">
			<?php if ( $eyebrow ) : ?><span class="sb-eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></span><?php endif; ?>
			<?php if ( $heading ) : ?><h2><?php echo wp_kses_post( $heading ); ?></h2><?php endif; ?>
			<?php if ( $dek )     : ?><p><?php   echo wp_kses_post( $dek );    ?></p><?php endif; ?>
		</div>

		<?php if ( $q->have_posts() ) : ?>
			<div class="sb-blog__carousel" data-sb-carousel>
				<div class="sb-blog__viewport">
					<div class="sb-blog__track">
						<?php while ( $q->have_posts() ) : $q->the_post();
							$cover = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
							$cats  = get_the_category();
							$cat   = ! empty( $cats ) ? $cats[0]->name : '';
						?>
							<article class="sb-blog__slide sb-blog__card">
								<a class="sb-blog__cover-link" href="<?php the_permalink(); ?>">
									<div class="sb-blog__cover"<?php echo $cover ? ' style="background-image:url(' . esc_url( $cover ) . ');"' : ''; ?>>
										<?php if ( ! $cover ) : ?><span class="sb-blog__cover-glyph"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span><?php endif; ?>
									</div>
								</a>
								<div class="sb-blog__body">
									<?php if ( $cat ) : ?><span class="sb-blog__cat"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
									<h3 class="sb-blog__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<?php if ( $showDate ) : ?>
										<time class="sb-blog__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									<?php endif; ?>
									<?php if ( $showExcerpt ) : ?>
										<p class="sb-blog__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
									<?php endif; ?>
									<a class="sb-blog__more" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read %s', 'smart-blocks' ), get_the_title() ) ); ?>">
										Read article
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
									</a>
								</div>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</div>
				<div class="sb-carousel-controls">
					<div class="sb-carousel-dots" data-sb-dots aria-label="Slide selector"></div>
					<div class="sb-carousel-nav">
						<button type="button" data-sb-prev aria-label="Previous posts">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
						</button>
						<button type="button" data-sb-next aria-label="Next posts">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
						</button>
					</div>
				</div>
			</div>
		<?php else : ?>
			<p style="text-align:center;color:var(--sb-muted);padding:2rem 0;">
				No posts published yet. Once you publish your first post in <strong>Posts → Add New</strong>, it will appear here automatically.
			</p>
		<?php endif; ?>
	</div>
</section>
<?php
