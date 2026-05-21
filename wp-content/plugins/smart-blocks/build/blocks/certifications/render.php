<?php
/** Certifications — server render. @var array $attributes @var \WP_Block $block */
if ( ! defined( 'ABSPATH' ) ) { exit; }
echo \SmartBlocks\Render\section(
	$attributes ?? [],
	$block,
	'sb-certifications',
	'div',
	'sb-certifications__grid'
);
