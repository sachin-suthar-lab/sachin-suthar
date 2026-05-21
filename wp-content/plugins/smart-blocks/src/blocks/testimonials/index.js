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
		const blockProps = useBlockProps( { className: 'sb-section sb-testimonials' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-testimonials__grid' },
			{ allowedBlocks: ALLOWED, template: TEMPLATE, orientation: 'horizontal' }
		);
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
					{ /* Editor uses a plain grid for direct editing; front-end view.js converts to carousel. */ }
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save: () => null,
} );
