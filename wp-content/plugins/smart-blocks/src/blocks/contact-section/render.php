<?php
/**
 * Contact Section — server render. @var array $attributes @var \WP_Block $block
 *
 * Renders an enterprise contact section: heading + channel list + accessible
 * form that posts via fetch() to the REST endpoint (view.js). Falls back to the
 * admin-post handler if JS is disabled.
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

$wrapper = get_block_wrapper_attributes( [ 'class' => 'sb-section sb-contact sb-reveal' ] );

$rest_nonce = wp_create_nonce( 'wp_rest' );
$rest_url   = rest_url( 'smart-blocks/v1/contact' );
?>
<section <?php echo $wrapper; ?>>
	<div class="sb-container">
		<div class="sb-section-head">
			<?php if ( $eyebrow !== '' ) : ?><span class="sb-eyebrow"><?php echo wp_kses_post( $eyebrow ); ?></span><?php endif; ?>
			<?php if ( $heading !== '' ) : ?><h2><?php echo wp_kses_post( $heading ); ?></h2><?php endif; ?>
			<?php if ( $dek !== '' )     : ?><p><?php   echo wp_kses_post( $dek );    ?></p><?php endif; ?>
		</div>

		<div class="sb-contact__wrap">
			<aside class="sb-contact__intro">
				<?php if ( $asideTitle !== '' ) : ?><h3><?php echo wp_kses_post( $asideTitle ); ?></h3><?php endif; ?>
				<?php if ( $asideIntro !== '' ) : ?><p><?php  echo wp_kses_post( $asideIntro ); ?></p><?php endif; ?>
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

			<form
				class="sb-contact__form"
				method="post"
				action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
				data-sb-contact
				data-endpoint="<?php echo esc_url( $rest_url ); ?>"
				data-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
				novalidate
			>
				<input type="hidden" name="action" value="smart_blocks_contact">
				<?php wp_nonce_field( 'smart_blocks_contact', 'smart_blocks_nonce' ); ?>

				<div class="sb-contact__form-status" data-sb-status role="status" aria-live="polite"></div>

				<div class="sb-contact__form-row">
					<div class="sb-field" data-sb-field="sb_name">
						<label class="sb-field__label" for="sb_name">
							<?php esc_html_e( 'Your name', 'smart-blocks' ); ?><span class="sb-field__required" aria-hidden="true">*</span>
						</label>
						<input type="text" id="sb_name" name="sb_name" required autocomplete="name" placeholder="Jane Doe" aria-describedby="sb_name_err">
						<div class="sb-field__error" id="sb_name_err"></div>
					</div>
					<div class="sb-field" data-sb-field="sb_email">
						<label class="sb-field__label" for="sb_email">
							<?php esc_html_e( 'Email', 'smart-blocks' ); ?><span class="sb-field__required" aria-hidden="true">*</span>
						</label>
						<input type="email" id="sb_email" name="sb_email" required autocomplete="email" placeholder="jane@company.com" aria-describedby="sb_email_err">
						<div class="sb-field__error" id="sb_email_err"></div>
					</div>
				</div>

				<div class="sb-field" data-sb-field="sb_company">
					<label class="sb-field__label" for="sb_company"><?php esc_html_e( 'Company / Project', 'smart-blocks' ); ?></label>
					<input type="text" id="sb_company" name="sb_company" autocomplete="organization" placeholder="Acme Inc · WooCommerce rebuild">
				</div>

				<div class="sb-field" data-sb-field="sb_message">
					<label class="sb-field__label" for="sb_message">
						<?php esc_html_e( 'Tell me about the project', 'smart-blocks' ); ?><span class="sb-field__required" aria-hidden="true">*</span>
					</label>
					<textarea id="sb_message" name="sb_message" required placeholder="What are you building, what's the timeline, and what does success look like?" aria-describedby="sb_message_err"></textarea>
					<div class="sb-field__error" id="sb_message_err"></div>
				</div>

				<div class="sb-honeypot">
					<label>Leave this empty <input type="text" name="sb_website" tabindex="-1" autocomplete="off"></label>
				</div>

				<div class="sb-contact__form-actions">
					<button type="submit" class="sb-btn sb-btn--primary" data-sb-submit>
						<?php echo esc_html( $submitLabel ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</section>
