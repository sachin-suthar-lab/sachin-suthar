import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps, RichText, InspectorControls,
	MediaUpload, MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, heading, body, imageId, imageUrl, imageAlt, highlights = [] } = attributes;
		const blockProps = useBlockProps( { className: 'sb-section sb-about' } );

		const updateH = ( i, patch ) => setAttributes( { highlights: highlights.map( ( h, idx ) => ( idx === i ? { ...h, ...patch } : h ) ) } );
		const addH    = () => setAttributes( { highlights: [ ...highlights, { title: 'New highlight', meta: '' } ] } );
		const removeH = ( i ) => setAttributes( { highlights: highlights.filter( ( _, idx ) => idx !== i ) } );

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Portrait image', 'smart-blocks' ) } initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { imageId: m.id, imageUrl: m.url, imageAlt: m.alt || '' } ) }
								allowedTypes={ [ 'image' ] }
								value={ imageId }
								render={ ( { open } ) => (
									<>
										{ imageUrl ? (
											<img src={ imageUrl } alt={ imageAlt } style={ { width: '100%', borderRadius: 12, marginBottom: 10 } } />
										) : (
											<p style={ { color: '#615b76', fontSize: 12 } }>{ __( 'No image set — placeholder will appear.', 'smart-blocks' ) }</p>
										) }
										<Button variant="primary" onClick={ open }>{ imageUrl ? __( 'Replace image', 'smart-blocks' ) : __( 'Upload / Select image', 'smart-blocks' ) }</Button>
										{ imageUrl && (
											<Button variant="link" isDestructive onClick={ () => setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } ) } style={ { marginLeft: 8 } }>
												{ __( 'Remove', 'smart-blocks' ) }
											</Button>
										) }
										<TextControl label={ __( 'Image alt text', 'smart-blocks' ) } value={ imageAlt } onChange={ ( v ) => setAttributes( { imageAlt: v } ) } />
									</>
								) }
							/>
						</MediaUploadCheck>
					</PanelBody>

					<PanelBody title={ __( 'Highlights', 'smart-blocks' ) } initialOpen={ false }>
						{ highlights.map( ( h, i ) => (
							<div key={ i } style={ { paddingBottom: '.75rem', marginBottom: '.75rem', borderBottom: '1px solid #e6e3f1' } }>
								<TextControl label={ __( 'Title', 'smart-blocks' ) } value={ h.title } onChange={ ( v ) => updateH( i, { title: v } ) } />
								<TextControl label={ __( 'Meta', 'smart-blocks' ) }  value={ h.meta }  onChange={ ( v ) => updateH( i, { meta: v } ) } />
								<Button isDestructive variant="link" onClick={ () => removeH( i ) }>{ __( 'Remove', 'smart-blocks' ) }</Button>
							</div>
						) ) }
						<Button variant="secondary" onClick={ addH }>{ __( '+ Add highlight', 'smart-blocks' ) }</Button>
					</PanelBody>
				</InspectorControls>

				<section { ...blockProps }>
					<div className="sb-container">
						<div className="sb-about__layout">
							<div className="sb-about__visual">
								<div className="sb-image-slot">
									{ imageUrl
										? <img src={ imageUrl } alt={ imageAlt } />
										: <span className="sb-image-slot__label">{ __( 'Image goes here', 'smart-blocks' ) }</span>
									}
								</div>
							</div>
							<div className="sb-about__copy">
								<RichText tagName="span" className="sb-eyebrow" value={ eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder={ __( 'Eyebrow', 'smart-blocks' ) } allowedFormats={ [] } />
								<RichText tagName="h2"   value={ heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder={ __( 'Section heading', 'smart-blocks' ) } allowedFormats={ [ 'core/bold' ] } />
								<RichText tagName="p"    value={ body }    onChange={ ( v ) => setAttributes( { body: v } ) }    placeholder={ __( 'About copy', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
								{ highlights.length > 0 && (
									<div className="sb-about__highlights">
										{ highlights.map( ( h, i ) => (
											<div className="sb-about__highlight" key={ i }>
												<strong>{ h.title }</strong>
												<span>{ h.meta }</span>
											</div>
										) ) }
									</div>
								) }
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
