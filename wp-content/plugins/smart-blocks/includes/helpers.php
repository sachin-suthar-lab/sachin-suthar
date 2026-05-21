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
 * Inline SVG icon library — lightweight, no font, no sprite sheet.
 * Stroked, 24x24, currentColor.
 */
function icon( string $name, int $size = 24 ): string {
	$paths = [
		'code'        => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
		'spark'       => '<path d="M12 3l2.39 5.84L20 11.21l-4.5 3.91L17 21l-5-3.1L7 21l1.5-5.88L4 11.21l5.61-2.37L12 3z"/>',
		'box'         => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
		'cart'        => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
		'plug'        => '<path d="M9 2v6"/><path d="M15 2v6"/><path d="M6 8h12v4a6 6 0 0 1-12 0V8z"/><path d="M12 18v4"/>',
		'bolt'        => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
		'layers'      => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
		'terminal'    => '<polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/>',
		'gauge'       => '<path d="M12 14l4-4"/><circle cx="12" cy="13" r="9"/>',
		'cube'        => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>',
		'mail'        => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
		'github'      => '<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>',
		'linkedin'    => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
		'twitter'     => '<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>',
		'arrow'       => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
		'check'       => '<polyline points="20 6 9 17 4 12"/>',
		'wp'          => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10M12 2a15.3 15.3 0 0 0-4 10 15.3 15.3 0 0 0 4 10"/>',
		'react'       => '<circle cx="12" cy="12" r="2"/><ellipse cx="12" cy="12" rx="10" ry="4"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(120 12 12)"/>',
		'php'         => '<ellipse cx="12" cy="12" rx="10" ry="5"/><text x="12" y="15" font-size="6" text-anchor="middle" fill="currentColor" stroke="none" font-family="monospace" font-weight="700">PHP</text>',
		'js'          => '<rect x="3" y="3" width="18" height="18" rx="3"/><text x="12" y="16" font-size="7" text-anchor="middle" fill="currentColor" stroke="none" font-family="monospace" font-weight="700">JS</text>',
		'db'          => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v6c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/><path d="M3 11v6c0 1.66 4.03 3 9 3s9-1.34 9-3v-6"/>',
		'git'         => '<circle cx="6" cy="18" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="18" cy="18" r="2"/><path d="M6 16V8a2 2 0 0 1 2-2h8"/><path d="M18 8v8"/>',
		'linux'       => '<ellipse cx="12" cy="17" rx="6" ry="3"/><path d="M9 12c0-3 1-7 3-7s3 4 3 7"/><circle cx="10" cy="11" r="1"/><circle cx="14" cy="11" r="1"/>',
		'tailwind'    => '<path d="M3 12c2-5 5-5 9-3 4 2 6 2 9-1-2 5-5 5-9 3-4-2-6-2-9 1z"/>',
		'rest'        => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7 9l-2 3 2 3M17 9l2 3-2 3M14 7l-4 10"/>',
	];
	$d = $paths[ $name ] ?? $paths['box'];
	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		$size,
		$d
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
