<?php
/** Tech Stack — server render. @var array $attributes @var \WP_Block $block */
if ( ! defined( 'ABSPATH' ) ) { exit; }
echo \SmartBlocks\Render\section(
	$attributes ?? [],
	$block,
	'sb-tech',
	'div',
	'sb-tech__grid'
);
