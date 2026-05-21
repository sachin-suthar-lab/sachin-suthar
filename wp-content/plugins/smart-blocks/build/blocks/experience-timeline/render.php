<?php
/** Experience Timeline — server render. @var array $attributes @var \WP_Block $block */
if ( ! defined( 'ABSPATH' ) ) { exit; }
echo \SmartBlocks\Render\section(
	$attributes ?? [],
	$block,
	'sb-experience',
	'ol',
	'sb-timeline'
);
