import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/project-card' ];
const TEMPLATE = [
	[ 'smart-blocks/project-card', { cat: 'WooCommerce',    title: 'Subscription commerce platform',           desc: 'Custom subscription engine on WooCommerce powering a $4M ARR DTC brand. Dunning, proration, and a custom self-serve member portal.', glyph: 'WC', gradient: 'linear-gradient(135deg, #7c5cff 0%, #f0abfc 100%)',  tags: [ 'WooCommerce', 'Stripe', 'Custom Plugin' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Headless',       title: 'Headless WP + Next.js marketing site',     desc: 'Editorial WordPress back-end powering a Next.js front-end with ISR, on-demand revalidation, and a custom block-to-component pipeline.', glyph: 'NX', gradient: 'linear-gradient(135deg, #11121a 0%, #22d3ee 130%)', tags: [ 'Next.js', 'GraphQL', 'ISR' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Block Library',  title: 'Agency-wide Gutenberg block library',      desc: 'A shared block library used across 30+ client sites: design tokens, accessibility baked in, ServerSideRender for editor parity.',     glyph: 'GB', gradient: 'linear-gradient(135deg, #34d399 0%, #22d3ee 100%)', tags: [ 'Gutenberg', 'React', 'Design Tokens' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Performance',    title: 'Core Web Vitals rescue for a SaaS site',   desc: 'LCP from 4.8s → 1.2s, CLS from 0.34 → 0.02. Custom critical-CSS pipeline, image strategy, and DB query refactor.',                     glyph: 'PF', gradient: 'linear-gradient(135deg, #f0abfc 0%, #7c5cff 100%)', tags: [ 'Performance', 'CWV', 'DB Tuning' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Custom Plugin',  title: 'Real-estate listings & MLS sync',          desc: 'Custom listings plugin with nightly MLS RETS sync, map clustering, and saved-search emails. Handled 80k+ properties.',                glyph: 'RE', gradient: 'linear-gradient(135deg, #22d3ee 0%, #7c5cff 100%)', tags: [ 'Custom Plugin', 'Cron', 'REST' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Enterprise',     title: 'Multi-site network migration',             desc: 'Migrated 14 country sites into a unified multisite network with shared theme.json, automated WP-CLI deploys, and an editorial workflow.', glyph: 'MS', gradient: 'linear-gradient(135deg, #11121a 0%, #7c5cff 130%)', tags: [ 'Multisite', 'WP-CLI', 'i18n' ] } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-projects' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-projects__grid' },
			{ allowedBlocks: ALLOWED, template: TEMPLATE, orientation: 'horizontal' }
		);
		return (
			<section { ...blockProps }>
				<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
				<div { ...innerProps } />
			</section>
		);
	},
	save( { attributes } ) {
		const { eyebrow, heading, dek } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-projects sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-projects__grid' } );
		return (
			<section { ...blockProps }>
				<div className="sb-section-head">
					{ eyebrow && <span className="sb-eyebrow">{ eyebrow }</span> }
					{ heading && <h2>{ heading }</h2> }
					{ dek && <p>{ dek }</p> }
				</div>
				<div { ...innerProps } />
			</section>
		);
	},
} );
