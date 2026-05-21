<?php
/**
 * Skills Showcase block — server render.
 */

use function SmartBlocks\Helpers\eyebrow;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** @var array $attributes */ $attributes = $attributes ?? [];

$heading = ! empty( $attributes['headingOverride'] ) ? $attributes['headingOverride'] : 'Skills sharpened by 8 years of shipping.';
$ey      = ! empty( $attributes['eyebrowOverride'] ) ? $attributes['eyebrowOverride'] : 'Toolkit';

$skills = apply_filters( 'smart_blocks/skills', [
	[ 'name' => 'PHP',           'pct' => 96 ],
	[ 'name' => 'WordPress',     'pct' => 98 ],
	[ 'name' => 'Gutenberg',     'pct' => 94 ],
	[ 'name' => 'JavaScript',    'pct' => 92 ],
	[ 'name' => 'React',         'pct' => 88 ],
	[ 'name' => 'WooCommerce',   'pct' => 90 ],
	[ 'name' => 'MySQL',         'pct' => 86 ],
	[ 'name' => 'REST API',      'pct' => 92 ],
	[ 'name' => 'Tailwind CSS',  'pct' => 85 ],
	[ 'name' => 'Git',           'pct' => 90 ],
	[ 'name' => 'Linux',         'pct' => 82 ],
	[ 'name' => 'WP-CLI',        'pct' => 90 ],
] );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'ss-section ss-skills ss-reveal ss-section--alt' ] );
?>
<section <?php echo $wrapper; ?> id="<?php echo esc_attr( $attributes['anchor'] ?? 'skills' ); ?>">
	<div class="ss-section-head">
		<?php echo eyebrow( $ey ); ?>
		<h2><?php echo esc_html( $heading ); ?></h2>
		<p>A focused stack centred on WordPress, modern JavaScript, and the surrounding infrastructure that makes products durable.</p>
	</div>

	<div class="ss-skills__list" role="list">
		<?php foreach ( $skills as $s ) :
			$pct = max( 0, min( 100, (int) $s['pct'] ) );
		?>
			<div class="ss-skill" role="listitem">
				<div class="ss-skill__head">
					<span class="ss-skill__name"><?php echo esc_html( $s['name'] ); ?></span>
					<span class="ss-skill__pct" aria-hidden="true"><?php echo $pct; ?>%</span>
				</div>
				<div class="ss-skill__bar" role="progressbar" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php echo esc_attr( $s['name'] ); ?> proficiency">
					<div class="ss-skill__fill" style="width:<?php echo $pct; ?>%"></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
