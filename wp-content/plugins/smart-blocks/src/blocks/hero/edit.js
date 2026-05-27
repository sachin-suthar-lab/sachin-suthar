import { __ } from '@wordpress/i18n';
import {
	useBlockProps, RichText, InspectorControls,
	MediaUpload, MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody, ToggleControl, TextControl, Button,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const {
		badge, badgeDot,
		headline, highlight,
		lede,
		ctaText, ctaUrl,
		ctaSecondaryText, ctaSecondaryUrl,
		imageId, imageUrl, imageAlt,
		badgeKicker, badgeTitle, tagLabel, tagText,
		metrics,
	} = attributes;

	const blockProps = useBlockProps( { className: 'sb-section sb-hero' } );

	const updateMetric = ( i, patch ) => setAttributes( { metrics: metrics.map( ( m, idx ) => ( idx === i ? { ...m, ...patch } : m ) ) } );
	const addMetric    = () => setAttributes( { metrics: [ ...metrics, { value: '0', label: 'Label' } ] } );
	const removeMetric = ( i ) => setAttributes( { metrics: metrics.filter( ( _, idx ) => idx !== i ) } );

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
										<p style={ { color: '#615b76', fontSize: 12 } }>{ __( 'No image set — portrait placeholder will appear.', 'smart-blocks' ) }</p>
									) }
									<Button variant="primary" onClick={ open }>{ imageUrl ? __( 'Replace image', 'smart-blocks' ) : __( 'Upload / Select image', 'smart-blocks' ) }</Button>
									{ imageUrl && (
										<Button variant="link" isDestructive onClick={ () => setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } ) } style={ { marginLeft: 8 } }>
											{ __( 'Remove', 'smart-blocks' ) }
										</Button>
									) }
									<TextControl
										label={ __( 'Image alt text', 'smart-blocks' ) }
										value={ imageAlt }
										onChange={ ( v ) => setAttributes( { imageAlt: v } ) }
										__nextHasNoMarginBottom
									/>
								</>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>

				<PanelBody title={ __( 'Image badge + status tag', 'smart-blocks' ) } initialOpen={ false }>
					<TextControl label={ __( 'Badge kicker', 'smart-blocks' ) } value={ badgeKicker } onChange={ ( v ) => setAttributes( { badgeKicker: v } ) } />
					<TextControl label={ __( 'Badge title', 'smart-blocks' ) } value={ badgeTitle } onChange={ ( v ) => setAttributes( { badgeTitle: v } ) } />
					<TextControl label={ __( 'Status label', 'smart-blocks' ) } value={ tagLabel } onChange={ ( v ) => setAttributes( { tagLabel: v } ) } />
					<TextControl label={ __( 'Status text', 'smart-blocks' ) } value={ tagText } onChange={ ( v ) => setAttributes( { tagText: v } ) } />
				</PanelBody>

				<PanelBody title={ __( 'Primary CTA', 'smart-blocks' ) } initialOpen={ false }>
					<TextControl label={ __( 'Text', 'smart-blocks' ) } value={ ctaText } onChange={ ( v ) => setAttributes( { ctaText: v } ) } />
					<TextControl label={ __( 'URL', 'smart-blocks' ) }  value={ ctaUrl }  onChange={ ( v ) => setAttributes( { ctaUrl: v } ) } />
				</PanelBody>

				<PanelBody title={ __( 'Secondary CTA', 'smart-blocks' ) } initialOpen={ false }>
					<TextControl label={ __( 'Text', 'smart-blocks' ) } value={ ctaSecondaryText } onChange={ ( v ) => setAttributes( { ctaSecondaryText: v } ) } />
					<TextControl label={ __( 'URL', 'smart-blocks' ) }  value={ ctaSecondaryUrl }  onChange={ ( v ) => setAttributes( { ctaSecondaryUrl: v } ) } />
				</PanelBody>

				<PanelBody title={ __( 'Badge', 'smart-blocks' ) } initialOpen={ false }>
					<ToggleControl
						label={ __( 'Show status dot', 'smart-blocks' ) }
						checked={ badgeDot }
						onChange={ ( v ) => setAttributes( { badgeDot: v } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Metrics', 'smart-blocks' ) } initialOpen={ false }>
					{ metrics.map( ( m, i ) => (
						<div key={ i } style={ { marginBottom: '1rem', paddingBottom: '.75rem', borderBottom: '1px solid #e6e3f1' } }>
							<TextControl label={ __( 'Value', 'smart-blocks' ) } value={ m.value } onChange={ ( v ) => updateMetric( i, { value: v } ) } />
							<TextControl label={ __( 'Label', 'smart-blocks' ) } value={ m.label } onChange={ ( v ) => updateMetric( i, { label: v } ) } />
							<Button isDestructive variant="link" onClick={ () => removeMetric( i ) }>{ __( 'Remove metric', 'smart-blocks' ) }</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addMetric }>{ __( '+ Add metric', 'smart-blocks' ) }</Button>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="sb-container">
					<div className="sb-hero__layout">
						<div className="sb-hero__copy">
							<span className="sb-hero__badge">
								{ badgeDot && <span className="dot" aria-hidden="true" /> }
								<RichText
									tagName="span"
									value={ badge }
									onChange={ ( v ) => setAttributes( { badge: v } ) }
									placeholder={ __( 'Badge text', 'smart-blocks' ) }
									allowedFormats={ [] }
								/>
							</span>

							<h1>
								<RichText
									tagName="span"
									value={ headline }
									onChange={ ( v ) => setAttributes( { headline: v } ) }
									placeholder={ __( 'Main headline', 'smart-blocks' ) }
									allowedFormats={ [ 'core/bold' ] }
								/>{ ' ' }
								<RichText
									tagName="span"
									className="sb-gradient"
									value={ highlight }
									onChange={ ( v ) => setAttributes( { highlight: v } ) }
									placeholder={ __( 'Gradient phrase', 'smart-blocks' ) }
									allowedFormats={ [ 'core/bold' ] }
								/>
							</h1>

							<RichText
								tagName="p"
								className="sb-hero__lede"
								value={ lede }
								onChange={ ( v ) => setAttributes( { lede: v } ) }
								placeholder={ __( 'Supporting paragraph', 'smart-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
							/>

							<div className="sb-hero__cta">
								<span className="sb-btn sb-btn--primary">{ ctaText }</span>
								<span className="sb-btn sb-btn--ghost">{ ctaSecondaryText }</span>
							</div>
						</div>

						<div className="sb-hero__visual">
							<div className="sb-image-slot">
								{ imageUrl ? (
									<img src={ imageUrl } alt={ imageAlt } />
								) : (
									<span className="sb-image-slot__label">{ __( 'Image goes here', 'smart-blocks' ) }</span>
								) }
							</div>
							{ ( badgeKicker || badgeTitle ) && (
								<div className="sb-hero__mark" aria-hidden="true">
									{ badgeKicker && <span className="sb-hero__mark-kicker">{ badgeKicker }</span> }
									{ badgeTitle && <span className="sb-hero__mark-title">{ badgeTitle }</span> }
								</div>
							) }
							{ ( tagLabel || tagText ) && (
								<div className="sb-hero__statustag">
									{ tagLabel && <div className="sb-hero__statustag-label">{ tagLabel }</div> }
									{ tagText && <div className="sb-hero__statustag-text">{ tagText }</div> }
								</div>
							) }
						</div>
					</div>

					<div className="sb-hero__metrics">
						{ metrics.map( ( m, i ) => (
							<div className="sb-metric" key={ i }>
								<strong>{ m.value }</strong>
								<span>{ m.label }</span>
							</div>
						) ) }
					</div>
				</div>
			</section>
		</>
	);
}
