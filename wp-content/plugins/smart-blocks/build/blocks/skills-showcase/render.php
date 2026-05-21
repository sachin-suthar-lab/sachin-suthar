<?php
/** Skills Showcase — server render.
 * @var array $attributes
 * @var \WP_Block $block */
if ( ! defined( 'ABSPATH' ) ) { exit; }
echo \SmartBlocks\Render\section(
	$attributes ?? [],
	$block,
	'sb-skills',
	'div',
	'sb-skills__grid',
	'sb-section--alt'
);
