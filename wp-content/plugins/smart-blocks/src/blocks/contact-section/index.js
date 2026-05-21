import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, Button } from '@wordpress/components';
import { ICON_OPTIONS, Icon } from '../../shared/icons';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function ChannelsList( { channels, mode } ) {
	if ( ! channels?.length ) return null;
	return (
		<div className="sb-contact__list">
			{ channels.map( ( c, i ) => {
				const external = ( c.href || '' ).startsWith( 'http' );
				const Tag = mode === 'preview' ? 'span' : 'a';
				const props = mode === 'preview'
					? { className: 'sb-contact__item' }
					: { className: 'sb-contact__item', href: c.href || '#', ...( external ? { target: '_blank', rel: 'noopener' } : {} ) };
				return (
					<Tag key={ i } { ...props }>
						<span className="sb-contact__item-icon"><Icon name={ c.icon } size={ 18 } /></span>
						<span>
							<span className="sb-contact__item-label">{ c.label }</span>
							<span className="sb-contact__item-value">{ c.value }</span>
						</span>
					</Tag>
				);
			} ) }
		</div>
	);
}

function ContactForm( { submitLabel, mode } ) {
	const disabled = mode === 'preview';
	return (
		<form className="sb-contact__form" data-sb-contact noValidate>
			<div className="sb-contact__form-status" data-sb-status role="status" aria-live="polite" />
			<div className="sb-contact__form-row">
				<div className="sb-field" data-sb-field="sb_name">
					<label className="sb-field__label" htmlFor="sb_name_input">
						{ __( 'Your name', 'smart-blocks' ) }<span className="sb-field__required" aria-hidden="true">*</span>
					</label>
					<input type="text" id="sb_name_input" name="sb_name" required autoComplete="name" placeholder="Jane Doe" aria-describedby="sb_name_err" disabled={ disabled } />
					<div className="sb-field__error" id="sb_name_err" />
				</div>
				<div className="sb-field" data-sb-field="sb_email">
					<label className="sb-field__label" htmlFor="sb_email_input">
						{ __( 'Email', 'smart-blocks' ) }<span className="sb-field__required" aria-hidden="true">*</span>
					</label>
					<input type="email" id="sb_email_input" name="sb_email" required autoComplete="email" placeholder="jane@company.com" aria-describedby="sb_email_err" disabled={ disabled } />
					<div className="sb-field__error" id="sb_email_err" />
				</div>
			</div>
			<div className="sb-field" data-sb-field="sb_company">
				<label className="sb-field__label" htmlFor="sb_company_input">{ __( 'Company / Project', 'smart-blocks' ) }</label>
				<input type="text" id="sb_company_input" name="sb_company" autoComplete="organization" placeholder="Acme Inc · WooCommerce rebuild" disabled={ disabled } />
			</div>
			<div className="sb-field" data-sb-field="sb_message">
				<label className="sb-field__label" htmlFor="sb_message_input">
					{ __( 'Tell me about the project', 'smart-blocks' ) }<span className="sb-field__required" aria-hidden="true">*</span>
				</label>
				<textarea id="sb_message_input" name="sb_message" required placeholder="What are you building, what's the timeline, and what does success look like?" aria-describedby="sb_message_err" disabled={ disabled } />
				<div className="sb-field__error" id="sb_message_err" />
			</div>
			<div className="sb-honeypot" aria-hidden="true">
				<label>Leave this empty <input type="text" name="sb_website" tabIndex="-1" autoComplete="off" /></label>
			</div>
			<div className="sb-contact__form-actions">
				<button type="submit" className="sb-btn sb-btn--primary" data-sb-submit disabled={ disabled }>
					{ submitLabel }
				</button>
			</div>
		</form>
	);
}

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, heading, dek, asideTitle, asideIntro, submitLabel, channels = [] } = attributes;
		const blockProps = useBlockProps( { className: 'sb-section sb-contact' } );

		const setChannel    = ( i, patch ) => setAttributes( { channels: channels.map( ( c, idx ) => ( idx === i ? { ...c, ...patch } : c ) ) } );
		const addChannel    = () => setAttributes( { channels: [ ...channels, { icon: 'mail', label: 'New', value: '', href: '#' } ] } );
		const removeChannel = ( i ) => setAttributes( { channels: channels.filter( ( _, idx ) => idx !== i ) } );

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Form', 'smart-blocks' ) } initialOpen>
						<TextControl label={ __( 'Submit button label', 'smart-blocks' ) } value={ submitLabel } onChange={ ( v ) => setAttributes( { submitLabel: v } ) } />
					</PanelBody>
					<PanelBody title={ __( 'Channels', 'smart-blocks' ) } initialOpen>
						{ channels.map( ( c, i ) => (
							<div key={ i } style={ { padding: '.5rem 0', borderBottom: '1px solid #e6e3f1', marginBottom: '.75rem' } }>
								<SelectControl label={ __( 'Icon', 'smart-blocks' ) } value={ c.icon } options={ ICON_OPTIONS } onChange={ ( v ) => setChannel( i, { icon: v } ) } />
								<TextControl label={ __( 'Label', 'smart-blocks' ) } value={ c.label } onChange={ ( v ) => setChannel( i, { label: v } ) } />
								<TextControl label={ __( 'Value', 'smart-blocks' ) } value={ c.value } onChange={ ( v ) => setChannel( i, { value: v } ) } />
								<TextControl label={ __( 'URL', 'smart-blocks' ) } value={ c.href } onChange={ ( v ) => setChannel( i, { href: v } ) } />
								<Button isDestructive variant="link" onClick={ () => removeChannel( i ) }>{ __( 'Remove channel', 'smart-blocks' ) }</Button>
							</div>
						) ) }
						<Button variant="secondary" onClick={ addChannel }>{ __( '+ Add channel', 'smart-blocks' ) }</Button>
					</PanelBody>
				</InspectorControls>

				<section { ...blockProps }>
					<div className="sb-container">
						<div className="sb-section-head">
							<RichText tagName="span" className="sb-eyebrow" value={ eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder={ __( 'Eyebrow', 'smart-blocks' ) } allowedFormats={ [] } />
							<RichText tagName="h2"   value={ heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder={ __( 'Heading', 'smart-blocks' ) } allowedFormats={ [ 'core/bold' ] } />
							<RichText tagName="p"    value={ dek }     onChange={ ( v ) => setAttributes( { dek: v } ) }     placeholder={ __( 'Description', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
						</div>
						<div className="sb-contact__wrap">
							<aside className="sb-contact__intro">
								<RichText tagName="h3" value={ asideTitle } onChange={ ( v ) => setAttributes( { asideTitle: v } ) } placeholder={ __( 'Aside title', 'smart-blocks' ) } allowedFormats={ [] } />
								<RichText tagName="p"  value={ asideIntro } onChange={ ( v ) => setAttributes( { asideIntro: v } ) } placeholder={ __( 'Aside intro', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<ChannelsList channels={ channels } mode="preview" />
							</aside>
							<ContactForm submitLabel={ submitLabel } mode="preview" />
						</div>
					</div>
				</section>
			</>
		);
	},
	save( { attributes } ) {
		const { eyebrow, heading, dek, asideTitle, asideIntro, submitLabel, channels = [] } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-contact sb-reveal' } );
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<div className="sb-section-head">
						{ eyebrow && <RichText.Content tagName="span" className="sb-eyebrow" value={ eyebrow } /> }
						{ heading && <RichText.Content tagName="h2" value={ heading } /> }
						{ dek && <RichText.Content tagName="p" value={ dek } /> }
					</div>
					<div className="sb-contact__wrap">
						<aside className="sb-contact__intro">
							{ asideTitle && <RichText.Content tagName="h3" value={ asideTitle } /> }
							{ asideIntro && <RichText.Content tagName="p" value={ asideIntro } /> }
							<ChannelsList channels={ channels } mode="save" />
						</aside>
						<ContactForm submitLabel={ submitLabel } mode="save" />
					</div>
				</div>
			</section>
		);
	},
} );
