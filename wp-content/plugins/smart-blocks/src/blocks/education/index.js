import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/education-item' ];
const TEMPLATE = [
	[ 'smart-blocks/education-item', {
		degree:      'Master of Computer Applications (MCA)',
		institution: 'Gujarat Technological University',
		years:       '— · CGPA 8.08',
	} ],
	[ 'smart-blocks/education-item', {
		degree:      'Bachelor of Computer Applications (BCA)',
		institution: 'Hemchandracharya North Gujarat University',
		years:       '— · CGPA 7.25',
	} ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-education' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-education__grid' },
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
		const blockProps = useBlockProps.save( { className: 'sb-section sb-education sb-reveal' } );
		const innerProps = useInnerBlocksProps.save( { className: 'sb-education__grid' } );
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
