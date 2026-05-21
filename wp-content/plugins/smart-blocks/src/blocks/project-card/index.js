import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps, RichText, InspectorControls,
	MediaUpload, MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import TagRepeater from '../../shared/tag-repeater';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { cat, title, desc, glyph, gradient, coverId, coverUrl, coverAlt, url, tags } = attributes;
		const blockProps = useBlockProps( { className: 'sb-project' } );

		const coverStyle = {
			'--cover': coverUrl ? `url(${ coverUrl })` : gradient,
			backgroundImage: coverUrl ? `url(${ coverUrl })` : undefined,
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Cover image', 'smart-blocks' ) } initialOpen>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( m ) => setAttributes( { coverId: m.id, coverUrl: m.url, coverAlt: m.alt || '' } ) }
								allowedTypes={ [ 'image' ] }
								value={ coverId }
								render={ ( { open } ) => (
									<>
										{ coverUrl ? (
											<img src={ coverUrl } alt={ coverAlt } style={ { width: '100%', borderRadius: 8, marginBottom: 8 } } />
										) : (
											<p style={ { color: '#777', fontSize: 12 } }>{ __( 'No cover image set — falls back to gradient.', 'smart-blocks' ) }</p>
										) }
										<Button variant="primary" onClick={ open }>{ coverUrl ? __( 'Replace image', 'smart-blocks' ) : __( 'Upload / Select image', 'smart-blocks' ) }</Button>
										{ coverUrl && (
											<Button variant="link" isDestructive onClick={ () => setAttributes( { coverId: 0, coverUrl: '', coverAlt: '' } ) } style={ { marginLeft: 8 } }>
												{ __( 'Remove', 'smart-blocks' ) }
											</Button>
										) }
									</>
								) }
							/>
						</MediaUploadCheck>
					</PanelBody>

					<PanelBody title={ __( 'Card', 'smart-blocks' ) } initialOpen={ false }>
						<TextControl
							label={ __( 'Cover glyph (used when no image)', 'smart-blocks' ) }
							value={ glyph }
							onChange={ ( v ) => setAttributes( { glyph: v } ) }
							help={ __( 'Two-letter shorthand displayed over the gradient.', 'smart-blocks' ) }
						/>
						<TextControl
							label={ __( 'Cover gradient (CSS)', 'smart-blocks' ) }
							value={ gradient }
							onChange={ ( v ) => setAttributes( { gradient: v } ) }
						/>
						<TextControl
							label={ __( 'Project URL (optional)', 'smart-blocks' ) }
							value={ url }
							onChange={ ( v ) => setAttributes( { url: v } ) }
						/>
					</PanelBody>

					<PanelBody title={ __( 'Tags', 'smart-blocks' ) } initialOpen={ false }>
						<TagRepeater value={ tags } onChange={ ( v ) => setAttributes( { tags: v } ) } />
					</PanelBody>
				</InspectorControls>

				<article { ...blockProps }>
					<div className="sb-project__cover" style={ coverStyle }>
						{ ! coverUrl && glyph && <span className="sb-project__cover-glyph">{ glyph }</span> }
					</div>
					<div className="sb-project__body">
						<RichText tagName="span" className="sb-project__cat"   value={ cat }   onChange={ ( v ) => setAttributes( { cat: v } ) }   placeholder={ __( 'Category', 'smart-blocks' ) } allowedFormats={ [] } />
						<RichText tagName="h3"   className="sb-project__title" value={ title } onChange={ ( v ) => setAttributes( { title: v } ) } placeholder={ __( 'Project title', 'smart-blocks' ) } allowedFormats={ [ 'core/bold' ] } />
						<RichText tagName="p"    className="sb-project__desc"  value={ desc }  onChange={ ( v ) => setAttributes( { desc: v } ) }  placeholder={ __( 'Short case-study summary', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
						{ tags?.length > 0 && (
							<div className="sb-project__tags">
								{ tags.map( ( t, i ) => ( <span key={ i } className="sb-tag">{ t }</span> ) ) }
							</div>
						) }
					</div>
				</article>
			</>
		);
	},
	save: () => null,
} );
