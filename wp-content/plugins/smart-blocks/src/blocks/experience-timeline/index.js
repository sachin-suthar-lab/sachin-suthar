import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/timeline-item' ];
const TEMPLATE = [
	[ 'smart-blocks/timeline-item', {
		period: '2022 — Present',
		role:   'Senior WordPress Engineer · Freelance',
		org:    'SaaS, agencies & enterprise clients · Remote',
		desc:   'Lead WordPress engineer on headless and FSE builds for SaaS marketing sites, agency clients, and enterprise CMS migrations. Architecting custom block libraries, performance-tuned WooCommerce stores, and CI-friendly WP-CLI workflows.',
		tags:   [ 'FSE Themes', 'Gutenberg Blocks', 'Headless', 'Performance' ],
	} ],
	[ 'smart-blocks/timeline-item', {
		period: '2020 — 2022',
		role:   'WordPress Tech Lead',
		org:    'Digital product studio',
		desc:   'Led a team of four building custom plugins and themes for venture-backed SaaS startups. Owned Gutenberg block architecture, code review standards, and release engineering. Delivered 40+ shipped products with measurable Core Web Vitals improvements.',
		tags:   [ 'Team Lead', 'Plugin Architecture', 'Code Review', 'CI/CD' ],
	} ],
	[ 'smart-blocks/timeline-item', {
		period: '2018 — 2020',
		role:   'Senior WordPress Developer',
		org:    'Boutique agency · Remote / Hybrid',
		desc:   'WooCommerce-heavy practice: custom checkout flows, subscription billing, ERP integrations, and multilingual storefronts. Migrated legacy classic-editor sites to Gutenberg with custom block parity.',
		tags:   [ 'WooCommerce', 'Custom Plugins', 'Migrations', 'ACF Pro' ],
	} ],
	[ 'smart-blocks/timeline-item', {
		period: '2017 — 2018',
		role:   'WordPress Developer',
		org:    'Product-led startup',
		desc:   'First in-house WordPress hire. Built the marketing platform, custom block library, REST integrations with the product app, and a publish workflow that took editors from idea to live in minutes.',
		tags:   [ 'Custom Themes', 'REST API', 'Editor UX' ],
	} ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-experience' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-timeline' },
			{ allowedBlocks: ALLOWED, template: TEMPLATE, orientation: 'vertical' }
		);
		return (
			<section { ...blockProps }>
				<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
				<ol { ...innerProps } />
			</section>
		);
	},
	save( { attributes } ) {
		const { eyebrow, heading, dek } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-experience sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-timeline' } );
		return (
			<section { ...blockProps }>
				<div className="sb-section-head">
					{ eyebrow && <span className="sb-eyebrow">{ eyebrow }</span> }
					{ heading && <h2>{ heading }</h2> }
					{ dek && <p>{ dek }</p> }
				</div>
				<ol { ...innerProps } />
			</section>
		);
	},
} );
