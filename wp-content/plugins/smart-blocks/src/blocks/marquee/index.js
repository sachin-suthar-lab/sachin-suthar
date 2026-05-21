import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function parseItems( raw ) {
	return ( raw || '' )
		.split( /[,\n]/ )
		.map( ( s ) => s.trim() )
		.filter( Boolean );
}

function MarqueeStrip( { items } ) {
	if ( ! items.length ) return null;
	// Duplicate the list so the CSS animation loops seamlessly.
	const doubled = [ ...items, ...items ];
	return (
		<div className="sb-marquee__track">
			{ doubled.map( ( label, i ) => (
				<span key={ i } className="sb-marquee__item">
					<span className="dot" aria-hidden="true" />
					{ label }
				</span>
			) ) }
		</div>
	);
}

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { items } = attributes;
		const parsed = parseItems( items );
		const blockProps = useBlockProps( { className: 'sb-marquee' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Items', 'smart-blocks' ) } initialOpen>
						<TextareaControl
							label={ __( 'Items (comma- or newline-separated)', 'smart-blocks' ) }
							value={ items }
							onChange={ ( v ) => setAttributes( { items: v } ) }
							help={ __( 'Each item appears in the scrolling ticker. The list is duplicated automatically for a seamless loop.', 'smart-blocks' ) }
							rows={ 6 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<MarqueeStrip items={ parsed } />
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const parsed = parseItems( attributes.items );
		const blockProps = useBlockProps.save( { className: 'sb-marquee' } );
		return (
			<div { ...blockProps }>
				<MarqueeStrip items={ parsed } />
			</div>
		);
	},
} );
