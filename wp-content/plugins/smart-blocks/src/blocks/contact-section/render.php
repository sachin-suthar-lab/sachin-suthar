<?php
/**
 * Contact Section — server render.
 *
 * Reads block attributes, prints section heading + channel list + inquiry form
 * pointing at the admin-post handler in includes/contact-handler.php.
 *
 * @var array $attributes
 */

use function SmartBlocks\Helpers\icon;

if ( ! defined( 'ABSPATH' ) ) { exit; }
/** @var array $attributes */ $attributes = $attributes ?? [];

$eyebrow     = $attributes['eyebrow']     ?? '';
$heading     = $attributes['heading']     ?? '';
$dek         = $attributes['dek']         ?? '';
$asideTitle  = $attributes['asideTitle']  ?? 'Channels';
$asideIntro  = $attributes['asideIntro']  ?? '';
$submitLabel = $attributes['submitLabel'] ?? 'Send message';
$channels    = is_array( $attributes['channels'] ?? null ) ? $attributes['channels'] : [];

$state = isset( $_GET['sb_contact'] ) ? sanitize_key( wp_unslash( $_GET['sb_contact'] ) ) : '';

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-section sb-contact sb-reveal' ] );
?>
<section <?php echo $wrapper; ?>>
	<div class="sb-section-head">
		<?php if ( $eyebrow !== '' ) : ?><span class="sb-eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></span><?php endif; ?>
		<?php if ( $heading !== '' ) : ?><h2><?php echo wp_kses_post( $heading ); ?></h2><?php endif; ?>
		<?php if ( $dek !== '' ) : ?><p><?php echo wp_kses_post( $dek ); ?></p><?php endif; ?>
	</div>

	<?php if ( 'sent' === $state ) : ?>
		<div class="sb-contact__notice sb-contact__notice--ok" role="status">
			<?php esc_html_e( 'Thanks — your message landed. I will reply within two working days.', 'smart-blocks' ); ?>
		</div>
	<?php elseif ( 'invalid' === $state ) : ?>
		<div class="sb-contact__notice sb-contact__notice--err" role="alert">
			<?php esc_html_e( 'Hmm, something looked off with that submission. Please try again.', 'smart-blocks' ); ?>
		</div>
	<?php endif; ?>

	<div class="sb-contact__wrap">
		<aside class="sb-contact__intro">
			<?php if ( $asideTitle !== '' ) : ?>
				<h2 style="font-size:1.5rem;font-weight:700;margin:0 0 1rem"><?php echo wp_kses_post( $asideTitle ); ?></h2>
			<?php endif; ?>
			<?php if ( $asideIntro !== '' ) : ?>
				<p><?php echo wp_kses_post( $asideIntro ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $channels ) ) : ?>
				<div class="sb-contact__list">
					<?php foreach ( $channels as $c ) :
						$icon_slug = $c['icon']  ?? 'mail';
						$label     = $c['label'] ?? '';
						$value     = $c['value'] ?? '';
						$href      = $c['href']  ?? '#';
						$external  = ( strpos( $href, 'http' ) === 0 );
					?>
						<a class="sb-contact__item" href="<?php echo esc_url( $href ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?>>
							<span class="sb-contact__item-icon"><?php echo icon( $icon_slug, 18 ); ?></span>
							<span>
								<span class="sb-contact__item-label"><?php echo esc_html( $label ); ?></span>
								<span class="sb-contact__item-value"><?php echo esc_html( $value ); ?></span>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</aside>

		<form class="sb-contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="smart_blocks_contact">
			<?php wp_nonce_field( 'smart_blocks_contact', 'smart_blocks_nonce' ); ?>

			<div class="sb-contact__form-row">
				<label>
					<span><?php esc_html_e( 'Your name', 'smart-blocks' ); ?></span>
					<input type="text" name="sb_name" required autocomplete="name" placeholder="Jane Doe">
				</label>
				<label>
					<span><?php esc_html_e( 'Email', 'smart-blocks' ); ?></span>
					<input type="email" name="sb_email" required autocomplete="email" placeholder="jane@company.com">
				</label>
			</div>

			<label>
				<span><?php esc_html_e( 'Company / Project', 'smart-blocks' ); ?></span>
				<input type="text" name="sb_company" autocomplete="organization" placeholder="Acme Inc · WooCommerce rebuild">
			</label>

			<label>
				<span><?php esc_html_e( 'Tell me about the project', 'smart-blocks' ); ?></span>
				<textarea name="sb_message" required placeholder="What are you building, what's the timeline, and what does success look like?"></textarea>
			</label>

			<div style="position:absolute;left:-9999px" aria-hidden="true">
				<label>Leave this empty <input type="text" name="sb_website" tabindex="-1" autocomplete="off"></label>
			</div>

			<button type="submit" class="sb-btn sb-btn--primary">
				<?php echo esc_html( $submitLabel ); ?>
			</button>
		</form>
	</div>
</section>
