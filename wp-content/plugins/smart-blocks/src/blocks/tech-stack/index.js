import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/tech-item' ];
const TEMPLATE = [
	[ 'smart-blocks/tech-item', { icon: 'wp',       name: 'WordPress',    meta: '7+ yrs · Core, FSE, multisite' } ],
	[ 'smart-blocks/tech-item', { icon: 'php',      name: 'PHP 7 / 8',    meta: '7+ yrs · OOP, Composer' } ],
	[ 'smart-blocks/tech-item', { icon: 'box',      name: 'ACF Pro',      meta: '6+ yrs · Field groups, custom blocks' } ],
	[ 'smart-blocks/tech-item', { icon: 'layers',   name: 'Gutenberg',    meta: '3+ yrs · Block development' } ],
	[ 'smart-blocks/tech-item', { icon: 'cart',     name: 'WooCommerce',  meta: '6+ yrs · Custom checkout, gateways' } ],
	[ 'smart-blocks/tech-item', { icon: 'js',       name: 'JavaScript',   meta: 'ES6+ · DOM, fetch, ESM' } ],
	[ 'smart-blocks/tech-item', { icon: 'react',    name: 'React',        meta: 'Gutenberg block UI' } ],
	[ 'smart-blocks/tech-item', { icon: 'db',       name: 'MySQL',        meta: 'Query tuning, indexes' } ],
	[ 'smart-blocks/tech-item', { icon: 'rest',     name: 'REST API',     meta: 'Custom routes, auth, webhooks' } ],
	[ 'smart-blocks/tech-item', { icon: 'terminal', name: 'WP-CLI',       meta: 'Migrations, deploys, automation' } ],
	[ 'smart-blocks/tech-item', { icon: 'git',      name: 'Git',          meta: 'GitHub, GitLab, CI/CD' } ],
	[ 'smart-blocks/tech-item', { icon: 'linux',    name: 'Linux',        meta: 'Server ops, deployments' } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-tech' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-tech__grid' },
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
		const blockProps = useBlockProps.save( { className: 'sb-section sb-tech sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-tech__grid' } );
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
