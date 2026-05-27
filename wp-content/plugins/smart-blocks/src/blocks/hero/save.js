import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function Save( { attributes } ) {
	const {
		badge, badgeDot,
		headline, highlight,
		lede,
		ctaText, ctaUrl,
		ctaSecondaryText, ctaSecondaryUrl,
		imageUrl, imageAlt,
		badgeKicker, badgeTitle, tagLabel, tagText,
		currentlyText, currentlyTarget,
		chips,
		metrics,
	} = attributes;

	const blockProps = useBlockProps.save( { className: 'sb-section sb-hero sb-reveal' } );

	return (
		<section { ...blockProps }>
			<div className="sb-container">
				<div className="sb-hero__layout">
					<div className="sb-hero__copy">
						{ badge && (
							<span className="sb-hero__badge">
								{ badgeDot && <span className="dot" aria-hidden="true" /> }
								<RichText.Content tagName="span" value={ badge } />
							</span>
						) }
						{ ( headline || highlight ) && (
							<h1>
								<RichText.Content tagName="span" value={ headline } />
								{ highlight && (
									<>{ ' ' }<RichText.Content tagName="span" className="sb-gradient" value={ highlight } /></>
								) }
							</h1>
						) }
						{ lede && <RichText.Content tagName="p" className="sb-hero__lede" value={ lede } /> }

						{ ( currentlyText || currentlyTarget ) && (
							<div className="sb-hero__currently">
								<span className="bullet" aria-hidden="true" />
								<span>
									{ currentlyText }{ ' ' }
									{ currentlyTarget && <strong>{ currentlyTarget }</strong> }
								</span>
							</div>
						) }

						{ ( ctaText || ctaSecondaryText ) && (
							<div className="sb-hero__cta">
								{ ctaText          && <a className="sb-btn sb-btn--primary" href={ ctaUrl }>{ ctaText }</a> }
								{ ctaSecondaryText && <a className="sb-btn sb-btn--ghost"   href={ ctaSecondaryUrl }>{ ctaSecondaryText }</a> }
							</div>
						) }

						{ chips?.length > 0 && (
							<div className="sb-hero__chips">
								{ chips.map( ( c, i ) => <span key={ i } className="sb-hero__chips-item">{ c }</span> ) }
							</div>
						) }
					</div>
					<div className="sb-hero__visual">
						<div className="sb-image-slot">
							{ imageUrl
								? <img src={ imageUrl } alt={ imageAlt } loading="eager" />
								: <span className="sb-image-slot__label">Image goes here</span>
							}
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
				{ metrics?.length > 0 && (
					<div className="sb-hero__metrics" role="list">
						{ metrics.map( ( m, i ) => (
							<div className="sb-metric" role="listitem" key={ i }>
								<strong>{ m.value }</strong>
								<span>{ m.label }</span>
							</div>
						) ) }
					</div>
				) }
			</div>
		</section>
	);
}
