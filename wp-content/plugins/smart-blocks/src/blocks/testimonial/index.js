import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps, RichText, InspectorControls,
	MediaUpload, MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function initials( name ) {
	const parts = ( name || '' ).replace( /<[^>]+>/g, '' ).trim().split( /\s+/ ).filter( Boolean );
	if ( ! parts.length ) return '';
	return ( parts[ 0 ][ 0 ] + ( parts[ 1 ]?.[ 0 ] || '' ) ).toUpperCase();
}

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { quote, name, role, avatarId, avatarUrl } = attributes;
		const blockProps = useBlockProps( { className: 'sb-testimonial' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Avatar', 'smart-blocks' ) } initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { avatarId: m.id, avatarUrl: m.url } ) }
								allowedTypes={ [ 'image' ] }
								value={ avatarId }
								render={ ( { open } ) => (
									<>
										{ avatarUrl ? (
											<img src={ avatarUrl } alt="" style={ { width: 60, height: 60, borderRadius: '50%', objectFit: 'cover', marginBottom: 8 } } />
										) : (
											<p style={ { color: '#777', fontSize: 12 } }>{ __( 'No avatar — initials shown.', 'smart-blocks' ) }</p>
										) }
										<Button variant="primary" onClick={ open }>{ avatarUrl ? __( 'Replace', 'smart-blocks' ) : __( 'Upload / Select', 'smart-blocks' ) }</Button>
										{ avatarUrl && (
											<Button variant="link" isDestructive onClick={ () => setAttributes( { avatarId: 0, avatarUrl: '' } ) } style={ { marginLeft: 8 } }>
												{ __( 'Remove', 'smart-blocks' ) }
											</Button>
										) }
									</>
								) }
							/>
						</MediaUploadCheck>
					</PanelBody>
				</InspectorControls>
				<figure { ...blockProps }>
					<RichText
						tagName="blockquote"
						className="sb-testimonial__quote"
						value={ quote }
						onChange={ ( v ) => setAttributes( { quote: v } ) }
						placeholder={ __( 'Quote', 'smart-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<figcaption className="sb-testimonial__person">
						<span className="sb-testimonial__avatar" aria-hidden="true">
							{ avatarUrl ? <img src={ avatarUrl } alt="" /> : initials( name ) }
						</span>
						<span>
							<RichText tagName="span" className="sb-testimonial__name" value={ name } onChange={ ( v ) => setAttributes( { name: v } ) } placeholder={ __( 'Name', 'smart-blocks' ) } allowedFormats={ [] } /><br />
							<RichText tagName="span" className="sb-testimonial__role" value={ role } onChange={ ( v ) => setAttributes( { role: v } ) } placeholder={ __( 'Role / Company', 'smart-blocks' ) } allowedFormats={ [] } />
						</span>
					</figcaption>
				</figure>
			</>
		);
	},
	save( { attributes } ) {
		const { quote, name, role, avatarUrl } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-testimonial sb-testimonials__slide' } );
		return (
			<figure { ...blockProps }>
				{ quote && <RichText.Content tagName="blockquote" className="sb-testimonial__quote" value={ quote } /> }
				<figcaption className="sb-testimonial__person">
					<span className="sb-testimonial__avatar" aria-hidden="true">
						{ avatarUrl ? <img src={ avatarUrl } alt="" /> : initials( name ) }
					</span>
					<span>
						<RichText.Content tagName="span" className="sb-testimonial__name" value={ name } /><br />
						<RichText.Content tagName="span" className="sb-testimonial__role" value={ role } />
					</span>
				</figcaption>
			</figure>
		);
	},
} );
