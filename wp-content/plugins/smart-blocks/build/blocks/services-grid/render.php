<?php
/**
 * Services Grid — server render.
 *
 * @var array     $attributes
 * @var \WP_Block $block
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

echo \SmartBlocks\Render\section(
	$attributes ?? [],
	$block,
	'sb-services',
	'div',
	'sb-services__grid'
);
