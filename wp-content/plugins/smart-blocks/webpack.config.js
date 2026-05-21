/**
 * Smart Blocks — wp-scripts webpack override.
 *
 * Default behaviour of @wordpress/scripts picks up src/*.js/scss as entries.
 * We extend it so each block under src/blocks/<slug>/index.js becomes its own
 * build target at build/blocks/<slug>/index.js, and a shared style bundle
 * src/shared.scss → build/shared.css is emitted for use across blocks.
 */
const path = require( 'path' );
const fs = require( 'fs' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const blocksDir = path.resolve( __dirname, 'src/blocks' );
const blockEntries = {};

if ( fs.existsSync( blocksDir ) ) {
	fs.readdirSync( blocksDir ).forEach( ( slug ) => {
		const indexFile = path.join( blocksDir, slug, 'index.js' );
		if ( fs.existsSync( indexFile ) ) {
			blockEntries[ `blocks/${ slug }/index` ] = indexFile;
		}
	} );
}

const sharedStyle = path.resolve( __dirname, 'src/shared.scss' );
if ( fs.existsSync( sharedStyle ) ) {
	blockEntries.shared = sharedStyle;
}

module.exports = {
	...defaultConfig,
	entry: () => blockEntries,
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
		filename: '[name].js',
	},
};
