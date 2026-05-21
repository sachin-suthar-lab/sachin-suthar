import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	__experimentalLinkControl as LinkControl,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	Button,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const {
		badge, badgeDot,
		headline, highlight,
		lede,
		ctaText, ctaUrl,
		ctaSecondaryText, ctaSecondaryUrl,
		metrics,
	} = attributes;

	const blockProps = useBlockProps( { className: 'sb-section sb-hero' } );

	const updateMetric = ( index, patch ) => {
		const next = metrics.map( ( m, i ) => ( i === index ? { ...m, ...patch } : m ) );
		setAttributes( { metrics: next } );
	};
	const addMetric    = () => setAttributes( { metrics: [ ...metrics, { value: '0', label: 'Label' } ] } );
	const removeMetric = ( index ) => setAttributes( { metrics: metrics.filter( ( _, i ) => i !== index ) } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Primary CTA', 'smart-blocks' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Button text', 'smart-blocks' ) }
						value={ ctaText }
						onChange={ ( v ) => setAttributes( { ctaText: v } ) }
					/>
					<TextControl
						label={ __( 'Button URL', 'smart-blocks' ) }
						value={ ctaUrl }
						onChange={ ( v ) => setAttributes( { ctaUrl: v } ) }
						help={ __( 'Use #contact for in-page scroll.', 'smart-blocks' ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Secondary CTA', 'smart-blocks' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Button text', 'smart-blocks' ) }
						value={ ctaSecondaryText }
						onChange={ ( v ) => setAttributes( { ctaSecondaryText: v } ) }
					/>
					<TextControl
						label={ __( 'Button URL', 'smart-blocks' ) }
						value={ ctaSecondaryUrl }
						onChange={ ( v ) => setAttributes( { ctaSecondaryUrl: v } ) }
					/>
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
						<div key={ i } style={ { marginBottom: '1rem', paddingBottom: '1rem', borderBottom: '1px solid #2a2b39' } }>
							<TextControl
								label={ __( 'Value', 'smart-blocks' ) }
								value={ m.value }
								onChange={ ( v ) => updateMetric( i, { value: v } ) }
							/>
							<TextControl
								label={ __( 'Label', 'smart-blocks' ) }
								value={ m.label }
								onChange={ ( v ) => updateMetric( i, { label: v } ) }
							/>
							<Button isDestructive variant="link" onClick={ () => removeMetric( i ) }>
								{ __( 'Remove metric', 'smart-blocks' ) }
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addMetric }>
						{ __( '+ Add metric', 'smart-blocks' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="sb-hero__inner">
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
