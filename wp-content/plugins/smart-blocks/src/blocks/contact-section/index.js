import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, Button } from '@wordpress/components';
import { ICON_OPTIONS, IconPreview } from '../../shared/icon-options';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, heading, dek, asideTitle, asideIntro, submitLabel, channels = [] } = attributes;
		const blockProps = useBlockProps( { className: 'sb-section sb-contact' } );

		const setChannel = ( i, patch ) => setAttributes( {
			channels: channels.map( ( c, idx ) => ( idx === i ? { ...c, ...patch } : c ) ),
		} );
		const addChannel    = () => setAttributes( { channels: [ ...channels, { icon: 'mail', label: 'New', value: '', href: '#' } ] } );
		const removeChannel = ( i ) => setAttributes( { channels: channels.filter( ( _, idx ) => idx !== i ) } );

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Form', 'smart-blocks' ) } initialOpen>
						<TextControl
							label={ __( 'Submit button label', 'smart-blocks' ) }
							value={ submitLabel }
							onChange={ ( v ) => setAttributes( { submitLabel: v } ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Channels', 'smart-blocks' ) } initialOpen>
						{ channels.map( ( c, i ) => (
							<div key={ i } style={ { padding: '.5rem 0', borderBottom: '1px solid #2a2b39', marginBottom: '.75rem' } }>
								<SelectControl
									label={ __( 'Icon', 'smart-blocks' ) }
									value={ c.icon }
									options={ ICON_OPTIONS }
									onChange={ ( v ) => setChannel( i, { icon: v } ) }
								/>
								<TextControl label={ __( 'Label', 'smart-blocks' ) } value={ c.label } onChange={ ( v ) => setChannel( i, { label: v } ) } />
								<TextControl label={ __( 'Value (displayed)', 'smart-blocks' ) } value={ c.value } onChange={ ( v ) => setChannel( i, { value: v } ) } />
								<TextControl label={ __( 'URL', 'smart-blocks' ) } value={ c.href } onChange={ ( v ) => setChannel( i, { href: v } ) } />
								<Button isDestructive variant="link" onClick={ () => removeChannel( i ) }>{ __( 'Remove channel', 'smart-blocks' ) }</Button>
							</div>
						) ) }
						<Button variant="secondary" onClick={ addChannel }>{ __( '+ Add channel', 'smart-blocks' ) }</Button>
					</PanelBody>
				</InspectorControls>

				<section { ...blockProps }>
					<div className="sb-section-head">
						<RichText tagName="span" className="sb-eyebrow" value={ eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder={ __( 'Eyebrow', 'smart-blocks' ) } allowedFormats={ [] } />
						<RichText tagName="h2"   value={ heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder={ __( 'Heading', 'smart-blocks' ) } allowedFormats={ [ 'core/bold' ] } />
						<RichText tagName="p"    value={ dek } onChange={ ( v ) => setAttributes( { dek: v } ) } placeholder={ __( 'Description', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
					</div>

					<div className="sb-contact__wrap">
						<aside className="sb-contact__intro">
							<RichText tagName="h2" value={ asideTitle } onChange={ ( v ) => setAttributes( { asideTitle: v } ) } placeholder={ __( 'Aside title', 'smart-blocks' ) } allowedFormats={ [] } style={ { fontSize: '1.5rem' } } />
							<RichText tagName="p"  value={ asideIntro } onChange={ ( v ) => setAttributes( { asideIntro: v } ) } placeholder={ __( 'Aside intro', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							<div className="sb-contact__list">
								{ channels.map( ( c, i ) => (
									<a key={ i } className="sb-contact__item" href={ c.href || '#' } onClick={ ( e ) => e.preventDefault() }>
										<span className="sb-contact__item-icon"><IconPreview name={ c.icon } /></span>
										<span>
											<span className="sb-contact__item-label">{ c.label }</span>
											<span className="sb-contact__item-value">{ c.value }</span>
										</span>
									</a>
								) ) }
							</div>
						</aside>

						<div className="sb-contact__form" aria-hidden="true">
							<p style={ { color: '#8a8d9b', fontSize: 13, margin: 0 } }>
								{ __( 'Front-end form preview — fields render server-side at view time.', 'smart-blocks' ) }
							</p>
							<div className="sb-contact__form-row">
								<label><span>Name</span><input type="text" disabled placeholder="Jane Doe" /></label>
								<label><span>Email</span><input type="email" disabled placeholder="jane@company.com" /></label>
							</div>
							<label><span>Company / Project</span><input type="text" disabled placeholder="Acme Inc · WooCommerce rebuild" /></label>
							<label><span>Message</span><textarea disabled placeholder="What are you building, what's the timeline?" /></label>
							<span className="sb-btn sb-btn--primary">{ submitLabel }</span>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null, // dynamic — form rendered by render.php for nonce + state
} );
