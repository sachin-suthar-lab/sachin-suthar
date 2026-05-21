<?php
/**
 * Contact Section block — server render.
 *
 * Form posts to admin-post with a nonce; handler emails the site admin.
 *
 * @var array     $attributes Block attributes injected by WordPress.
 * @var string    $content    Block inner content injected by WordPress.
 * @var WP_Block  $block      Block instance injected by WordPress.
 */

use function SmartBlocks\Helpers\icon;
use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : "Let's build something durable together.";
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'Contact';

$channels = apply_filters( 'smart_blocks/contact_channels', [
	[ 'icon' => 'mail',     'label' => 'Email',    'value' => 'hello@sachinsuthar.dev',      'href' => 'mailto:hello@sachinsuthar.dev' ],
	[ 'icon' => 'linkedin', 'label' => 'LinkedIn', 'value' => 'in/sachinsuthar',             'href' => 'https://www.linkedin.com/in/sachinsuthar' ],
	[ 'icon' => 'github',   'label' => 'GitHub',   'value' => 'github.com/sachinsuthar',     'href' => 'https://github.com/sachinsuthar' ],
	[ 'icon' => 'twitter',  'label' => 'X',        'value' => '@sachinsuthar',               'href' => 'https://twitter.com/sachinsuthar' ],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-contact ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'contact' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>Drop a note about your project, timeline, and stack. I read every message and reply within two working days.</p>
	</div>

	<?php
	$state = isset( $_GET['sb_contact'] ) ? sanitize_key( $_GET['sb_contact'] ) : '';
	if ( 'sent' === $state ) {
		echo '<div class="ss-contact__notice ss-contact__notice--ok" role="status">Thanks — your message landed. I will reply within two working days.</div>';
	} elseif ( 'invalid' === $state ) {
		echo '<div class="ss-contact__notice ss-contact__notice--err" role="alert">Hmm, something looked off with that submission. Please try again.</div>';
	}
	?>

	<div class="ss-contact__wrap">
		<aside class="ss-contact__intro">
			<h2 style="font-size:1.5rem;font-weight:700;margin:0 0 1rem">Channels</h2>
			<p>Pick whatever's easiest. For project enquiries, email or the form below gets the fastest reply.</p>
			<div class="ss-contact__list">
				<?php foreach ( $channels as $c ) : ?>
					<a class="ss-contact__item" href="<?php echo esc_url( $c['href'] ); ?>" rel="noopener" <?php echo ( strpos( $c['href'], 'http' ) === 0 ) ? 'target="_blank"' : ''; ?>>
						<span class="ss-contact__item-icon"><?php echo icon( $c['icon'], 18 ); ?></span>
						<span>
							<span class="ss-contact__item-label"><?php echo esc_html( $c['label'] ); ?></span>
							<span class="ss-contact__item-value"><?php echo esc_html( $c['value'] ); ?></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</aside>

		<form class="ss-contact__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="smart_blocks_contact">
			<?php wp_nonce_field( 'smart_blocks_contact', 'smart_blocks_nonce' ); ?>

			<div class="ss-contact__form-row">
				<label>
					<span>Your name</span>
					<input type="text" name="sb_name" required autocomplete="name" placeholder="Jane Doe">
				</label>
				<label>
					<span>Email</span>
					<input type="email" name="sb_email" required autocomplete="email" placeholder="jane@company.com">
				</label>
			</div>

			<label>
				<span>Company / Project</span>
				<input type="text" name="sb_company" autocomplete="organization" placeholder="Acme Inc · WooCommerce rebuild">
			</label>

			<label>
				<span>Tell me about the project</span>
				<textarea name="sb_message" required placeholder="What are you building, what's the timeline, and what does success look like?"></textarea>
			</label>

			<!-- Honeypot -->
			<div style="position:absolute;left:-9999px" aria-hidden="true">
				<label>Leave this empty <input type="text" name="sb_website" tabindex="-1" autocomplete="off"></label>
			</div>

			<button type="submit" class="ss-btn ss-btn--primary">
				Send message <?php echo icon( 'arrow', 18 ); ?>
			</button>
		</form>
	</div>
</section>
