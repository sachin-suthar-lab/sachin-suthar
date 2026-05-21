import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/service' ];
const TEMPLATE = [
	[ 'smart-blocks/service', { icon: 'wp',       title: 'Custom WordPress Development', desc: 'Custom themes, plugins, and bespoke admin tools — architected for long-term maintainability and zero technical debt.' } ],
	[ 'smart-blocks/service', { icon: 'layers',   title: 'Gutenberg Block Development',  desc: 'Production-grade native and dynamic blocks with React, block.json, server-side rendering, and editor parity.' } ],
	[ 'smart-blocks/service', { icon: 'cart',     title: 'WooCommerce Engineering',      desc: 'High-conversion stores, custom checkout flows, subscription logic, and ERP / payment gateway integrations.' } ],
	[ 'smart-blocks/service', { icon: 'plug',     title: 'Headless WordPress',           desc: 'WordPress as a content API powering Next.js, React, and mobile front-ends, with auth, ISR, and caching done right.' } ],
	[ 'smart-blocks/service', { icon: 'gauge',    title: 'Performance Optimisation',     desc: 'Core Web Vitals deep-dives, query profiling, asset budgets, and a measurable path to 90+ Lighthouse on real hardware.' } ],
	[ 'smart-blocks/service', { icon: 'cube',     title: 'Elementor & Bricks',           desc: 'Custom widgets, dynamic data, and clean visual builder workflows that ship designer intent without bloat.' } ],
	[ 'smart-blocks/service', { icon: 'rest',     title: 'API Integrations',             desc: 'REST, GraphQL, and webhook bridges to CRMs, payment processors, mailers, and internal back-office systems.' } ],
	[ 'smart-blocks/service', { icon: 'box',      title: 'Advanced Custom Fields',       desc: 'Modular content models, ACF Pro field groups, and editor experiences that let marketing ship without engineering.' } ],
	[ 'smart-blocks/service', { icon: 'terminal', title: 'WP-CLI Automation',            desc: 'Idempotent CLI scripts for migrations, content imports, environment setup, and CI/CD-friendly deploys.' } ],
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
				<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
				<div { ...innerProps } />
			</section>
		);
	},
	save( { attributes } ) {
		const { eyebrow, heading, dek } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-section sb-services sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-services__grid' } );
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
