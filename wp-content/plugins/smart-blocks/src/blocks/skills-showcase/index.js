import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/skill' ];
const TEMPLATE = [
	[ 'smart-blocks/skill', { name: 'PHP',          proficiency: 96 } ],
	[ 'smart-blocks/skill', { name: 'WordPress',    proficiency: 98 } ],
	[ 'smart-blocks/skill', { name: 'Gutenberg',    proficiency: 94 } ],
	[ 'smart-blocks/skill', { name: 'JavaScript',   proficiency: 92 } ],
	[ 'smart-blocks/skill', { name: 'React',        proficiency: 88 } ],
	[ 'smart-blocks/skill', { name: 'WooCommerce',  proficiency: 90 } ],
	[ 'smart-blocks/skill', { name: 'MySQL',        proficiency: 86 } ],
	[ 'smart-blocks/skill', { name: 'REST API',     proficiency: 92 } ],
	[ 'smart-blocks/skill', { name: 'Tailwind CSS', proficiency: 85 } ],
	[ 'smart-blocks/skill', { name: 'Git',          proficiency: 90 } ],
	[ 'smart-blocks/skill', { name: 'Linux',        proficiency: 82 } ],
	[ 'smart-blocks/skill', { name: 'WP-CLI',       proficiency: 90 } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-skills sb-section--alt' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-skills__grid' },
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
		const blockProps = useBlockProps.save( { className: 'sb-section sb-skills sb-section--alt sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-skills__grid' } );
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
