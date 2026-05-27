/**
 * Smart Blocks — icon component.
 *
 * Data lives in the auto-generated icon-data.js (brand logos from
 * simple-icons + Feather-style outline glyphs). The same data is mirrored in
 * includes/icon-data.php so the React save() output and the PHP provisioner
 * output are byte-identical (no block-validation mismatches).
 *
 * Regenerate both with:  node tools/gen-icons.mjs
 */
import { ICONS, ICON_OPTIONS } from './icon-data';

export { ICON_OPTIONS };

/**
 * Render an icon by key. Brand logos are solid (fill); generic glyphs are
 * stroked outlines. The wrapper attributes here MUST match icon() in
 * includes/icon-data.php's consumer (helpers.php) exactly.
 */
export function Icon( { name, size = 24 } ) {
	const def = name && ICONS[ name ];
	if ( ! def ) {
		return null;
	}
	const common = {
		xmlns: 'http://www.w3.org/2000/svg',
		width: size,
		height: size,
		viewBox: '0 0 24 24',
		'aria-hidden': 'true',
		focusable: 'false',
		dangerouslySetInnerHTML: { __html: def.body },
	};
	if ( def.solid ) {
		return <svg { ...common } fill="currentColor" />;
	}
	return (
		<svg
			{ ...common }
			fill="none"
			stroke="currentColor"
			strokeWidth="1.6"
			strokeLinecap="round"
			strokeLinejoin="round"
		/>
	);
}
