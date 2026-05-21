import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
import './style.scss';

const BADGE = (
	<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
		<circle cx="12" cy="8" r="6"/>
		<path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
	</svg>
);

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { title, issuer } = attributes;
		const blockProps = useBlockProps( { className: 'sb-cert' } );
		return (
			<div { ...blockProps }>
				<span className="sb-cert__badge">{ BADGE }</span>
				<RichText
					tagName="h3"
					className="sb-cert__title"
					value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) }
					placeholder={ __( 'Certification name', 'smart-blocks' ) }
					allowedFormats={ [ 'core/bold' ] }
				/>
				<RichText
					tagName="span"
					className="sb-cert__issuer"
					value={ issuer }
					onChange={ ( v ) => setAttributes( { issuer: v } ) }
					placeholder={ __( 'Issuer', 'smart-blocks' ) }
					allowedFormats={ [] }
				/>
			</div>
		);
	},
	save: () => null,
} );
