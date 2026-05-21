/**
 * Smart Blocks — shared editor registration.
 *
 * Every block is server-rendered (render.php). The editor uses ServerSideRender
 * so what you see in the editor matches the front-end exactly. Blocks expose a
 * single "headingOverride" attribute via InspectorControls so editors can tweak
 * the top headline per instance without leaving Gutenberg; everything else is
 * defined in the render templates as production-quality default content.
 */
( function ( wp, data ) {
	if ( ! wp || ! data || ! Array.isArray( data.blocks ) ) {
		return;
	}

	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps     = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var ServerSideRender  = wp.serverSideRender;
	var el                = wp.element.createElement;
	var Fragment          = wp.element.Fragment;
	var PanelBody         = wp.components.PanelBody;
	var TextControl       = wp.components.TextControl;
	var __                = wp.i18n.__;

	data.blocks.forEach( function ( blockName ) {
		var registered = wp.blocks.getBlockType( blockName );
		if ( registered ) {
			// Already registered via block.json metadata + render.php — extend with edit/save.
			return;
		}
	} );

	data.blocks.forEach( function ( blockName ) {
		try {
			registerBlockType( blockName, {
				edit: function ( props ) {
					var blockProps = useBlockProps( {
						style: { minHeight: '120px' },
					} );

					var attributes = props.attributes || {};
					var setAttributes = props.setAttributes;

					return el(
						Fragment,
						{},
						el(
							InspectorControls,
							{},
							el(
								PanelBody,
								{ title: __( 'Section content', 'smart-blocks' ), initialOpen: true },
								el( TextControl, {
									label: __( 'Heading override (optional)', 'smart-blocks' ),
									help: __( 'Leave blank to use the built-in headline.', 'smart-blocks' ),
									value: attributes.headingOverride || '',
									onChange: function ( v ) { setAttributes( { headingOverride: v } ); },
								} ),
								el( TextControl, {
									label: __( 'Eyebrow override (optional)', 'smart-blocks' ),
									value: attributes.eyebrowOverride || '',
									onChange: function ( v ) { setAttributes( { eyebrowOverride: v } ); },
								} )
							)
						),
						el(
							'div',
							blockProps,
							el( ServerSideRender, {
								block: blockName,
								attributes: attributes,
								EmptyResponsePlaceholder: function () {
									return el( 'div', { style: { padding: '2rem', textAlign: 'center', opacity: 0.6 } },
										__( 'Loading preview…', 'smart-blocks' )
									);
								},
							} )
						)
					);
				},
				save: function () { return null; },
			} );
		} catch ( e ) {
			// If a block was already registered via PHP metadata, skip silently.
			if ( window.console && console.warn ) {
				console.warn( '[Smart Blocks] could not extend ' + blockName + ':', e.message );
			}
		}
	} );
} )( window.wp, window.SmartBlocksData );
