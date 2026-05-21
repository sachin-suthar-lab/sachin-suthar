import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/certification' ];
const TEMPLATE = [
	[ 'smart-blocks/certification', { title: 'Enterprise Block Editor',          issuer: 'WordPress VIP' } ],
	[ 'smart-blocks/certification', { title: 'Advanced WordPress Debugging',     issuer: 'WordPress VIP' } ],
	[ 'smart-blocks/certification', { title: 'WordPress VIP Architecture & Tooling', issuer: 'WordPress VIP' } ],
	[ 'smart-blocks/certification', { title: 'Enterprise WordPress Performance', issuer: 'WordPress VIP' } ],
	[ 'smart-blocks/certification', { title: 'Enterprise WordPress Security',    issuer: 'WordPress VIP' } ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-certifications' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-certifications__grid' },
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
	save: () => null,
} );
