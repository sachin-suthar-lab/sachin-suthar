import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/testimonial' ];
const TEMPLATE = [
	[ 'smart-blocks/testimonial', { quote: 'Sachin rebuilt our marketing site as a custom theme + ACF block library — editor experience went from "open a ticket" to "ship it yourself" overnight.', name: 'Priya Menon',    role: 'Head of Marketing · B2B SaaS' } ],
	[ 'smart-blocks/testimonial', { quote: 'We hired Sachin to fix a 4.8s LCP. Two weeks later we were under 1.5s on real devices, with cleaner code than we started with. Rare combination.',                       name: 'David Lin',      role: 'CTO · DTC Commerce' } ],
	[ 'smart-blocks/testimonial', { quote: 'He thinks in architectures, not snippets. Our custom plugin survived two WordPress major releases without a single regression — that is the bar.',                       name: 'Marta Hoffmann', role: 'Engineering Manager · Agency' } ],
	[ 'smart-blocks/testimonial', { quote: 'Communication, code review, documentation — all senior-level. He raised the standard for the whole team while he was with us.',                                          name: 'Rahul Verma',    role: 'Founder · Product Studio' } ],
	[ 'smart-blocks/testimonial', { quote: 'The ACF block library Sachin built is now reused across 30+ client sites. Real engineering, not just markup.',                                                            name: 'Sofia Alvarez',  role: 'Tech Lead · Digital Agency' } ],
	[ 'smart-blocks/testimonial', { quote: 'Migrated multiple country sites into one multisite network with zero downtime and a faster editorial workflow on the other side. Quiet, calm, and exact.',               name: 'Jonas Berg',     role: 'Director of Web · Enterprise' } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-testimonials sb-section--alt' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-testimonials__grid' },
			{ allowedBlocks: ALLOWED, template: TEMPLATE, orientation: 'horizontal' }
		);
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
					{ /* Editor uses a plain grid; front-end view.js converts to carousel. */ }
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save( { attributes } ) {
		const { eyebrow, heading, dek } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-testimonials sb-section--alt sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-testimonials__track' } );
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<div className="sb-section-head">
						{ eyebrow && <span className="sb-eyebrow">{ eyebrow }</span> }
						{ heading && <h2>{ heading }</h2> }
						{ dek && <p>{ dek }</p> }
					</div>
					<div className="sb-testimonials__carousel" data-sb-carousel>
						<div className="sb-testimonials__viewport">
							<div { ...innerProps } />
						</div>
						<div className="sb-carousel-controls">
							<div className="sb-carousel-dots" data-sb-dots aria-label="Slide selector" />
							<div className="sb-carousel-nav">
								<button type="button" data-sb-prev aria-label="Previous testimonials">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="15 18 9 12 15 6" /></svg>
								</button>
								<button type="button" data-sb-next aria-label="Next testimonials">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="9 18 15 12 9 6" /></svg>
								</button>
							</div>
						</div>
					</div>
				</div>
			</section>
		);
	},
} );
