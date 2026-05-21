import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps, RichText, InspectorControls,
	MediaUpload, MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { title, issuer, year, badgeId, badgeUrl, badgeAlt, url } = attributes;
		const blockProps = useBlockProps( { className: 'sb-cert' } );

		const Body = (
			<>
				<div className="sb-cert__image">
					{ badgeUrl
						? <img src={ badgeUrl } alt={ badgeAlt } />
						: <span className="sb-cert__placeholder">Upload certificate image</span>
					}
				</div>
				<div className="sb-cert__body">
					<RichText
						tagName="h3"
						className="sb-cert__title"
						value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						placeholder={ __( 'Certification name', 'sachins-blocks' ) }
						allowedFormats={ [ 'core/bold' ] }
					/>
					<RichText
						tagName="span"
						className="sb-cert__issuer"
						value={ issuer }
						onChange={ ( v ) => setAttributes( { issuer: v } ) }
						placeholder={ __( 'Issuer', 'sachins-blocks' ) }
						allowedFormats={ [] }
					/>
					{ year && <span className="sb-cert__year">{ year }</span> }
				</div>
			</>
		);

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Certificate image', 'sachins-blocks' ) } initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { badgeId: m.id, badgeUrl: m.url, badgeAlt: m.alt || m.title || '' } ) }
								allowedTypes={ [ 'image' ] }
								value={ badgeId }
								render={ ( { open } ) => (
									<>
										{ badgeUrl ? (
											<img
												src={ badgeUrl }
												alt={ badgeAlt }
												style={ { width: '100%', maxWidth: 220, aspectRatio: '4 / 3', borderRadius: 12, objectFit: 'contain', marginBottom: 10, background: '#fff', padding: 10, border: '1px solid #e6e3f1', display: 'block' } }
											/>
										) : (
											<p style={ { color: '#b91c1c', fontSize: 12, fontWeight: 500, margin: '0 0 8px' } }>
												{ __( '⚠ No image set — upload the certificate image so the card has a visual.', 'sachins-blocks' ) }
											</p>
										) }
										<Button variant="primary" onClick={ open } style={ { width: '100%', justifyContent: 'center' } }>
											{ badgeUrl ? __( 'Replace certificate image', 'sachins-blocks' ) : __( 'Upload certificate image', 'sachins-blocks' ) }
										</Button>
										{ badgeUrl && (
											<Button variant="link" isDestructive onClick={ () => setAttributes( { badgeId: 0, badgeUrl: '', badgeAlt: '' } ) } style={ { marginTop: 6 } }>
												{ __( 'Remove image', 'sachins-blocks' ) }
											</Button>
										) }
										<TextControl
											label={ __( 'Image alt text', 'sachins-blocks' ) }
											value={ badgeAlt }
											onChange={ ( v ) => setAttributes( { badgeAlt: v } ) }
											help={ __( 'Describe the certificate for screen readers.', 'sachins-blocks' ) }
											__nextHasNoMarginBottom
										/>
									</>
								) }
							/>
						</MediaUploadCheck>
					</PanelBody>
					<PanelBody title={ __( 'Meta', 'sachins-blocks' ) } initialOpen={ false }>
						<TextControl label={ __( 'Year (optional)', 'sachins-blocks' ) } value={ year } onChange={ ( v ) => setAttributes( { year: v } ) } />
						<TextControl label={ __( 'Verification URL (optional)', 'sachins-blocks' ) } value={ url } onChange={ ( v ) => setAttributes( { url: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>{ Body }</div>
			</>
		);
	},
	save( { attributes } ) {
		const { title, issuer, year, badgeUrl, badgeAlt, url } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-cert' } );
		const body = (
			<>
				<div className="sb-cert__image">
					{ badgeUrl
						? <img src={ badgeUrl } alt={ badgeAlt } />
						: <span className="sb-cert__placeholder">Image goes here</span>
					}
				</div>
				<div className="sb-cert__body">
					{ title && <RichText.Content tagName="h3" className="sb-cert__title" value={ title } /> }
					{ issuer && <RichText.Content tagName="span" className="sb-cert__issuer" value={ issuer } /> }
					{ year && <span className="sb-cert__year">{ year }</span> }
				</div>
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
