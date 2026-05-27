/**
 * Icon registry generator — single source of truth.
 *
 * Brand logos come from the `simple-icons` package (real, recognizable marks).
 * Generic UI glyphs are clean Feather-style outline icons.
 *
 * Emits two data files that are byte-identical in their SVG bodies, so the
 * React save() output and the PHP provisioner output always match (no block
 * validation errors):
 *   - src/shared/icon-data.js   (export const ICONS)
 *   - includes/icon-data.php    (return [...])
 *
 * Run:  node tools/gen-icons.mjs
 */
import * as si from 'simple-icons';
import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const root = join( __dirname, '..' );

/* ---- Generic outline (Feather-style) glyphs: raw inner SVG markup ---- */
const OUTLINE = {
	code:     '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
	terminal: '<polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/>',
	layers:   '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
	cart:     '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
	plug:     '<path d="M9 2v6"/><path d="M15 2v6"/><path d="M6 8h12v4a6 6 0 0 1-12 0V8z"/><path d="M12 18v4"/>',
	gauge:    '<circle cx="12" cy="13" r="9"/><path d="M12 14l4-4"/>',
	cube:     '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>',
	box:      '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
	spark:    '<path d="M12 3l2.39 5.84L20 11.21l-4.5 3.91L17 21l-5-3.1L7 21l1.5-5.88L4 11.21l5.61-2.37L12 3z"/>',
	bolt:     '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
	mail:     '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
	arrow:    '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
	check:    '<polyline points="20 6 9 17 4 12"/>',
	award:    '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>',
	rest:     '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7 9l-2 3 2 3M17 9l2 3-2 3M14 7l-4 10"/>',
	linkedin: '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
};

/* ---- Brand logos: simple-icons slug -> our key ---- */
const BRAND = {
	wp:         'siWordpress',
	php:        'siPhp',
	js:         'siJavascript',
	react:      'siReact',
	db:         'siMysql',
	git:        'siGit',
	github:     'siGithub',
	x:          'siX',
	linux:      'siLinux',
	docker:     'siDocker',
	phpmyadmin: 'siPhpmyadmin',
	composer:   'siComposer',
	node:       'siNodedotjs',
	sass:       'siSass',
	redis:      'siRedis',
	elementor:  'siElementor',
	woo:        'siWoocommerce',
	tailwind:   'siTailwindcss',
	npm:        'siNpm',
};

/* ---- Editor dropdown labels & order ---- */
const LABELS = {
	wp: 'WordPress', php: 'PHP', js: 'JavaScript', react: 'React',
	db: 'MySQL', git: 'Git', docker: 'Docker', phpmyadmin: 'phpMyAdmin',
	composer: 'Composer', node: 'Node.js', sass: 'Sass', redis: 'Redis',
	elementor: 'Elementor', woo: 'WooCommerce', tailwind: 'Tailwind CSS',
	npm: 'npm', linux: 'Linux', github: 'GitHub', linkedin: 'LinkedIn', x: 'X (Twitter)',
	layers: 'Gutenberg / Layers', cart: 'Cart', plug: 'Plug / API', gauge: 'Gauge',
	cube: 'Cube', box: 'Box', spark: 'Spark', bolt: 'Bolt', mail: 'Mail',
	arrow: 'Arrow', check: 'Check', award: 'Award', rest: 'REST API', terminal: 'Terminal', code: 'Code',
};

const icons = {};
const missing = [];

// Brand (solid fill) — fall back to outline def if simple-icons lacks it.
for ( const [ key, sym ] of Object.entries( BRAND ) ) {
	const ic = si[ sym ];
	if ( ic && ic.path ) {
		icons[ key ] = { body: `<path d="${ ic.path }"/>`, solid: true };
	} else {
		missing.push( `${ key } (${ sym })` );
		if ( OUTLINE[ key ] ) icons[ key ] = { body: OUTLINE[ key ], solid: false };
	}
}
// Generic (outline) — only add keys not already provided by a brand logo.
for ( const [ key, body ] of Object.entries( OUTLINE ) ) {
	if ( ! icons[ key ] ) icons[ key ] = { body, solid: false };
}

if ( missing.length ) console.error( 'WARN: simple-icons missing → outline fallback:', missing.join( ', ' ) );

/* ---- Build ordered option list for the editor SelectControl ---- */
const ORDER = [
	'', 'wp', 'php', 'js', 'react', 'layers', 'woo', 'db', 'phpmyadmin', 'redis',
	'docker', 'git', 'github', 'composer', 'npm', 'node', 'sass', 'tailwind',
	'terminal', 'linux', 'rest', 'plug', 'elementor', 'cart', 'gauge', 'cube',
	'box', 'spark', 'bolt', 'mail', 'arrow', 'check', 'award', 'code', 'linkedin', 'x',
];
const options = [ { label: '— None —', value: '' } ];
for ( const key of ORDER ) {
	if ( key === '' ) continue;
	if ( ! icons[ key ] ) continue;
	options.push( { label: LABELS[ key ] || key, value: key } );
}

/* ---- Emit JS data file ---- */
const jsLines = [
	'/**',
	' * AUTO-GENERATED by tools/gen-icons.mjs — do not edit by hand.',
	' * Brand logos: simple-icons. Generic glyphs: Feather-style outline.',
	' * Kept byte-identical with includes/icon-data.php for block save parity.',
	' */',
	'',
	'export const ICONS = {',
];
for ( const [ key, def ] of Object.entries( icons ) ) {
	jsLines.push( `\t${ JSON.stringify( key ) }: { body: ${ JSON.stringify( def.body ) }, solid: ${ def.solid } },` );
}
jsLines.push( '};' );
jsLines.push( '' );
jsLines.push( 'export const ICON_OPTIONS = ' + JSON.stringify( options, null, '\t' ) + ';' );
jsLines.push( '' );
writeFileSync( join( root, 'src/shared/icon-data.js' ), jsLines.join( '\n' ) );

/* ---- Emit PHP data file ---- */
const phpLines = [
	'<?php',
	'/**',
	' * AUTO-GENERATED by tools/gen-icons.mjs — do not edit by hand.',
	' * Mirror of src/shared/icon-data.js (byte-identical SVG bodies).',
	' *',
	' * @package SmartBlocks',
	' */',
	'',
	'return [',
];
for ( const [ key, def ] of Object.entries( icons ) ) {
	const body = def.body.replace( /\\/g, '\\\\' ).replace( /'/g, "\\'" );
	phpLines.push( `\t'${ key }' => [ 'body' => '${ body }', 'solid' => ${ def.solid ? 'true' : 'false' } ],` );
}
phpLines.push( '];' );
phpLines.push( '' );
writeFileSync( join( root, 'includes/icon-data.php' ), phpLines.join( '\n' ) );

console.log( `✓ Generated ${ Object.keys( icons ).length } icons → icon-data.js + icon-data.php` );
console.log( `  Options: ${ options.length - 1 }` );
