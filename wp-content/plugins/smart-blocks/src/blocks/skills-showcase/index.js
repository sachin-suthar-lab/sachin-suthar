import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/skill' ];
const TEMPLATE = [
	[ 'smart-blocks/skill', { icon: 'php',      name: 'PHP 7/8 (OOP)',     proficiency: 96 } ],
	[ 'smart-blocks/skill', { icon: 'wp',       name: 'WordPress',          proficiency: 98 } ],
	[ 'smart-blocks/skill', { icon: 'box',      name: 'ACF / ACF Pro',      proficiency: 96 } ],
	[ 'smart-blocks/skill', { icon: 'layers',   name: 'Gutenberg Blocks',   proficiency: 90 } ],
	[ 'smart-blocks/skill', { icon: 'cart',     name: 'WooCommerce',        proficiency: 92 } ],
	[ 'smart-blocks/skill', { icon: 'js',       name: 'JavaScript (ES6+)',  proficiency: 88 } ],
	[ 'smart-blocks/skill', { icon: 'react',    name: 'React (Gutenberg)',  proficiency: 80 } ],
	[ 'smart-blocks/skill', { icon: 'db',       name: 'MySQL & Query Tuning', proficiency: 90 } ],
	[ 'smart-blocks/skill', { icon: 'rest',     name: 'REST API & Webhooks', proficiency: 92 } ],
	[ 'smart-blocks/skill', { icon: 'terminal', name: 'WP-CLI',             proficiency: 92 } ],
	[ 'smart-blocks/skill', { icon: 'git',      name: 'Git / GitHub / GitLab', proficiency: 90 } ],
	[ 'smart-blocks/skill', { icon: 'gauge',    name: 'Core Web Vitals',    proficiency: 88 } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-skills' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-skills__grid' },
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
		const blockProps = useBlockProps.save( { className: 'sb-section sb-skills sb-section--alt sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-skills__grid' } );
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
