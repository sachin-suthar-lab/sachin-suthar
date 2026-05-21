import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/service' ];
const TEMPLATE = [
	[ 'smart-blocks/service', { icon: 'wp',       title: 'Custom WordPress Development', desc: 'End-to-end custom themes, plugins, and admin-side tooling. Built to WordPress VIP coding standards with clean architecture.',           proficiency: 96 } ],
	[ 'smart-blocks/service', { icon: 'layers',   title: 'Gutenberg & ACF Block Development', desc: 'Production-grade native blocks plus rich ACF-based blocks. Strong editor UX so marketing can ship without engineering.',           proficiency: 92 } ],
	[ 'smart-blocks/service', { icon: 'cart',     title: 'WooCommerce Engineering',     desc: 'Custom checkouts, subscriptions, payment gateways, and ERP integrations for content-heavy stores.',                                          proficiency: 90 } ],
	[ 'smart-blocks/service', { icon: 'plug',     title: 'API Integrations',            desc: 'REST and third-party API bridges to CRMs, payment processors, mailers, and back-office systems.',                                            proficiency: 91 } ],
	[ 'smart-blocks/service', { icon: 'gauge',    title: 'Performance Optimisation',    desc: 'Core Web Vitals deep-dives, MySQL query optimisation, object caching (Redis, Batcache), and edge caching. 30–50% measured gains.',           proficiency: 93 } ],
	[ 'smart-blocks/service', { icon: 'box',      title: 'Advanced Custom Fields',      desc: 'Modular content models with ACF Pro, custom post types, taxonomies, and user-role architectures for content-heavy platforms.',              proficiency: 95 } ],
	[ 'smart-blocks/service', { icon: 'cube',     title: 'Elementor / WPBakery / Divi', desc: 'Custom widgets, dynamic data, and visual-builder workflows that match designer intent without sacrificing performance.',                     proficiency: 86 } ],
	[ 'smart-blocks/service', { icon: 'terminal', title: 'WP-CLI Automation',           desc: 'Idempotent CLI scripts for migrations, content imports, environment setup, and CI/CD-friendly deploys.',                                     proficiency: 90 } ],
	[ 'smart-blocks/service', { icon: 'spark',    title: 'AI-assisted Development',     desc: 'n8n, Claude Code, Codex, Cursor — leveraging modern AI tooling to ship faster without compromising code quality.',                            proficiency: 85 } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-services' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-services__grid' },
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
	save: () => null, // dynamic — rendered by render.php
} );
