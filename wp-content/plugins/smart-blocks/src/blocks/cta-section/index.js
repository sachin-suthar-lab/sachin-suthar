import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { heading, dek, primaryText, primaryUrl, secondaryText, secondaryUrl } = attributes;
		const blockProps = useBlockProps( { className: 'sb-section sb-cta-wrap sb-section--cream' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Primary button', 'smart-blocks' ) } initialOpen>
						<TextControl label={ __( 'Text', 'smart-blocks' ) } value={ primaryText } onChange={ ( v ) => setAttributes( { primaryText: v } ) } />
						<TextControl label={ __( 'URL', 'smart-blocks' ) }  value={ primaryUrl }  onChange={ ( v ) => setAttributes( { primaryUrl: v } ) } />
					</PanelBody>
					<PanelBody title={ __( 'Secondary button', 'smart-blocks' ) } initialOpen={ false }>
						<TextControl label={ __( 'Text', 'smart-blocks' ) } value={ secondaryText } onChange={ ( v ) => setAttributes( { secondaryText: v } ) } />
						<TextControl label={ __( 'URL', 'smart-blocks' ) }  value={ secondaryUrl }  onChange={ ( v ) => setAttributes( { secondaryUrl: v } ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="sb-container">
						<div className="sb-cta">
							<RichText
								tagName="h2"
								value={ heading }
								onChange={ ( v ) => setAttributes( { heading: v } ) }
								placeholder={ __( 'CTA headline', 'smart-blocks' ) }
								allowedFormats={ [ 'core/bold' ] }
							/>
							<RichText
								tagName="p"
								value={ dek }
								onChange={ ( v ) => setAttributes( { dek: v } ) }
								placeholder={ __( 'Supporting paragraph', 'smart-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
							/>
							<div className="sb-cta__buttons">
								<span className="sb-btn sb-btn--primary">{ primaryText }</span>
								<span className="sb-btn sb-btn--ghost">{ secondaryText }</span>
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save( { attributes } ) {
		const { heading, dek, primaryText, primaryUrl, secondaryText, secondaryUrl } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-cta-wrap sb-section--cream sb-reveal' } );
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<div className="sb-cta">
						{ heading && <RichText.Content tagName="h2" value={ heading } /> }
						{ dek && <RichText.Content tagName="p" value={ dek } /> }
						<div className="sb-cta__buttons">
							{ primaryText && <a className="sb-btn sb-btn--primary" href={ primaryUrl }>{ primaryText }</a> }
							{ secondaryText && <a className="sb-btn sb-btn--ghost" href={ secondaryUrl }>{ secondaryText }</a> }
						</div>
					</div>
				</div>
			</section>
		);
	},
} );
