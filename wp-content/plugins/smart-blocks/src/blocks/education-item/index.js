import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
import './style.scss';

const CapIcon = () => (
	<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
		<path d="M22 10 12 4 2 10l10 6 10-6Z" />
		<path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5" />
	</svg>
);

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { degree, institution, years, detail } = attributes;
		const blockProps = useBlockProps( { className: 'sb-edu' } );
		return (
			<div { ...blockProps }>
				<span className="sb-edu__icon"><CapIcon /></span>
				<div className="sb-edu__body">
					<RichText
						tagName="h3"
						className="sb-edu__degree"
						value={ degree }
						onChange={ ( v ) => setAttributes( { degree: v } ) }
						placeholder={ __( 'Degree (e.g. MCA)', 'sachins-blocks' ) }
						allowedFormats={ [ 'core/bold' ] }
					/>
					<RichText
						tagName="span"
						className="sb-edu__inst"
						value={ institution }
						onChange={ ( v ) => setAttributes( { institution: v } ) }
						placeholder={ __( 'Institution', 'sachins-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="span"
						className="sb-edu__years"
						value={ years }
						onChange={ ( v ) => setAttributes( { years: v } ) }
						placeholder={ __( 'Years · grade', 'sachins-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="sb-edu__detail"
						value={ detail }
						onChange={ ( v ) => setAttributes( { detail: v } ) }
						placeholder={ __( 'Notes (optional)', 'sachins-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
				</div>
			</div>
		);
	},
	save( { attributes } ) {
		const { degree, institution, years, detail } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-edu' } );
		return (
			<div { ...blockProps }>
				<span className="sb-edu__icon"><CapIcon /></span>
				<div className="sb-edu__body">
					{ degree      && <RichText.Content tagName="h3" className="sb-edu__degree" value={ degree } /> }
					{ institution && <RichText.Content tagName="span" className="sb-edu__inst" value={ institution } /> }
					{ years       && <RichText.Content tagName="span" className="sb-edu__years" value={ years } /> }
					{ detail      && <RichText.Content tagName="p" className="sb-edu__detail" value={ detail } /> }
				</div>
			</div>
		);
	},
} );
