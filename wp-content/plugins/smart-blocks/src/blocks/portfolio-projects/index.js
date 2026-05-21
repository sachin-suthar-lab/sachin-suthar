import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/project-card' ];
const TEMPLATE = [
	[ 'smart-blocks/project-card', { cat: 'WooCommerce',     title: 'Subscription commerce platform',           desc: 'Custom subscription engine on WooCommerce powering a high-volume DTC brand. Dunning, proration, and a custom self-serve member portal.', glyph: 'WC', gradient: 'linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%)',  tags: [ 'WooCommerce', 'Stripe', 'Custom Plugin' ] } ],
	[ 'smart-blocks/project-card', { cat: 'LMS',             title: 'Learning management system',                desc: 'Custom LMS built on WordPress with LearnDash extensions, certificate generation, drip content, and reporting dashboards.',          glyph: 'LM', gradient: 'linear-gradient(135deg, #7c3aed 0%, #c4b5fd 100%)', tags: [ 'LMS', 'LearnDash', 'ACF Pro' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Block Library',   title: 'Reusable ACF block library',                desc: 'Shared ACF-based Gutenberg block library used across 30+ client sites: design tokens, accessibility, and editor parity.',            glyph: 'BL', gradient: 'linear-gradient(135deg, #16a34a 0%, #a78bfa 100%)', tags: [ 'ACF Blocks', 'Gutenberg', 'Design Tokens' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Performance',     title: 'Core Web Vitals rescue',                    desc: 'Reduced LCP from 4.8s → 1.2s, CLS from 0.34 → 0.02. Custom critical-CSS pipeline, image strategy, and DB query refactor.',           glyph: 'PF', gradient: 'linear-gradient(135deg, #a78bfa 0%, #6d28d9 100%)', tags: [ 'Performance', 'CWV', 'Caching' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Custom Plugin',   title: 'Internal workflow automation',              desc: 'Custom plugin architecture that automates internal business workflows: ticket routing, status sync, and reporting back to stakeholders.', glyph: 'WF', gradient: 'linear-gradient(135deg, #6d28d9 0%, #16a34a 100%)', tags: [ 'Custom Plugin', 'REST API', 'Automation' ] } ],
	[ 'smart-blocks/project-card', { cat: 'Enterprise CMS',  title: 'Corporate multisite migration',             desc: 'Migrated multiple country sites into a unified multisite network with shared theme.json, automated WP-CLI deploys, and editorial workflow.', glyph: 'MS', gradient: 'linear-gradient(135deg, #4c1d95 0%, #a78bfa 100%)', tags: [ 'Multisite', 'WP-CLI', 'i18n' ] } ],
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
				<div className="sb-container">
					<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save( { attributes } ) {
		const { eyebrow, heading, dek } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-projects sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-projects__grid' } );
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<div className="sb-section-head">
						{ eyebrow && <span className="sb-eyebrow">{ eyebrow }</span> }
						{ heading && <h2>{ heading }</h2> }
						{ dek && <p>{ dek }</p> }
					</div>
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
} );
