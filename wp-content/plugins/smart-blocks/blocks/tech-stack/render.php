<?php
/**
 * Tech Stack block — server render.
 */

use function SmartBlocks\Helpers\icon;
use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'A focused stack — not a buzzword soup.';
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'Tech stack';

$stack = apply_filters( 'smart_blocks/stack', [
	[ 'icon' => 'wp',       'name' => 'WordPress' ],
	[ 'icon' => 'php',      'name' => 'PHP' ],
	[ 'icon' => 'js',       'name' => 'JavaScript' ],
	[ 'icon' => 'react',    'name' => 'React' ],
	[ 'icon' => 'layers',   'name' => 'Gutenberg' ],
	[ 'icon' => 'cart',     'name' => 'WooCommerce' ],
	[ 'icon' => 'db',       'name' => 'MySQL' ],
	[ 'icon' => 'tailwind', 'name' => 'Tailwind' ],
	[ 'icon' => 'rest',     'name' => 'REST API' ],
	[ 'icon' => 'git',      'name' => 'Git' ],
	[ 'icon' => 'linux',    'name' => 'Linux' ],
	[ 'icon' => 'terminal', 'name' => 'WP-CLI' ],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-tech ss-reveal' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'stack' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>The tools I actually use day-to-day, picked for stability, ecosystem health, and team velocity.</p>
	</div>

	<div class="ss-tech__grid" role="list">
		<?php foreach ( $stack as $t ) : ?>
			<div class="ss-tech-card" role="listitem" title="<?php echo esc_attr( $t['name'] ); ?>">
				<div class="ss-tech-card__icon"><?php echo icon( $t['icon'], 28 ); ?></div>
				<div class="ss-tech-card__name"><?php echo esc_html( $t['name'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
