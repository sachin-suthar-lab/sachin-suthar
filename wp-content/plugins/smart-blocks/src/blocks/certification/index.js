import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps, RichText, InspectorControls,
	MediaUpload, MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const FallbackBadge = () => (
	<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
		<circle cx="12" cy="8" r="6" />
		<path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />
	</svg>
);

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { title, issuer, year, badgeId, badgeUrl, badgeAlt, url } = attributes;
		const blockProps = useBlockProps( { className: 'sb-cert' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Badge image', 'smart-blocks' ) } initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { badgeId: m.id, badgeUrl: m.url, badgeAlt: m.alt || '' } ) }
								allowedTypes={ [ 'image' ] }
								value={ badgeId }
								render={ ( { open } ) => (
									<>
										{ badgeUrl ? (
											<img src={ badgeUrl } alt={ badgeAlt } style={ { width: 80, height: 80, borderRadius: 12, objectFit: 'contain', marginBottom: 8, background: '#f6f5fb', padding: 6 } } />
										) : (
											<p style={ { color: '#615b76', fontSize: 12 } }>{ __( 'No badge image — fallback award icon will appear.', 'smart-blocks' ) }</p>
										) }
										<Button variant="primary" onClick={ open }>{ badgeUrl ? __( 'Replace badge', 'smart-blocks' ) : __( 'Upload / Select badge', 'smart-blocks' ) }</Button>
										{ badgeUrl && (
											<Button variant="link" isDestructive onClick={ () => setAttributes( { badgeId: 0, badgeUrl: '', badgeAlt: '' } ) } style={ { marginLeft: 8 } }>
												{ __( 'Remove', 'smart-blocks' ) }
											</Button>
										) }
										<TextControl label={ __( 'Image alt text', 'smart-blocks' ) } value={ badgeAlt } onChange={ ( v ) => setAttributes( { badgeAlt: v } ) } __nextHasNoMarginBottom />
									</>
								) }
							/>
						</MediaUploadCheck>
					</PanelBody>
					<PanelBody title={ __( 'Meta', 'smart-blocks' ) } initialOpen={ false }>
						<TextControl label={ __( 'Year (optional)', 'smart-blocks' ) } value={ year } onChange={ ( v ) => setAttributes( { year: v } ) } />
						<TextControl label={ __( 'Verification URL (optional)', 'smart-blocks' ) } value={ url } onChange={ ( v ) => setAttributes( { url: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<span className="sb-cert__badge">
						{ badgeUrl ? <img src={ badgeUrl } alt={ badgeAlt } /> : <FallbackBadge /> }
					</span>
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
					{ year && <span className="sb-cert__year">{ year }</span> }
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const { title, issuer, year, badgeUrl, badgeAlt, url } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-cert' } );
		const body = (
			<>
				<span className="sb-cert__badge">
					{ badgeUrl ? <img src={ badgeUrl } alt={ badgeAlt } /> : <FallbackBadge /> }
				</span>
				{ title && <RichText.Content tagName="h3" className="sb-cert__title" value={ title } /> }
				{ issuer && <RichText.Content tagName="span" className="sb-cert__issuer" value={ issuer } /> }
				{ year && <span className="sb-cert__year">{ year }</span> }
			</>
		);
		return (
			<div { ...blockProps }>
				{ url
					? <a className="sb-cert__link" href={ url } rel="noopener" target="_blank">{ body }</a>
					: body
				}
			</div>
		);
	},
} );
