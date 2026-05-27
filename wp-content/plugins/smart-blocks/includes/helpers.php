<?php
/**
 * Smart Blocks shared helpers: SVG icons, section wrapper, render utilities.
 *
 * @package SmartBlocks
 */

namespace SmartBlocks\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline SVG icon. Brand logos (solid fill) come from simple-icons; generic
 * glyphs are Feather-style outlines. Data is the auto-generated icon-data.php,
 * mirrored from src/shared/icon-data.js — the SVG wrapper built here MUST match
 * the React Icon component (src/shared/icons.js) attribute-for-attribute so
 * static-save block markup validates.
 *
 * Regenerate the data with:  node tools/gen-icons.mjs
 */
function icon( string $name, int $size = 24 ): string {
	static $icons = null;
	if ( null === $icons ) {
		$icons = require __DIR__ . '/icon-data.php';
	}
	$def = $icons[ $name ] ?? $icons['box'];
	if ( ! empty( $def['solid'] ) ) {
		return sprintf(
			'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor">%2$s</svg>',
			$size,
			$def['body']
		);
	}
	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">%2$s</svg>',
		$size,
		$def['body']
	);
}

/**
 * Section wrapper opener with reveal hook + alignfull semantics.
 *
 * @param string $id     CSS id for in-page anchor navigation.
 * @param string $extra  Extra class names.
 * @return string Opening tag.
 */
function section_open( string $id, string $extra = '' ): string {
	return sprintf(
		'<section id="%1$s" class="ss-section ss-reveal %2$s">',
		esc_attr( $id ),
		esc_attr( $extra )
	);
}

function section_close(): string {
	return '</section>';
}

/**
 * Renders a small uppercase eyebrow tag above a heading.
 */
function eyebrow( string $text ): string {
	return sprintf(
		'<span class="ss-eyebrow">%s</span>',
		esc_html( $text )
	);
}
