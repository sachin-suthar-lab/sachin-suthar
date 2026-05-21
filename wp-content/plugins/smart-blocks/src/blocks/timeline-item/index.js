import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import TagRepeater from '../../shared/tag-repeater';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { period, role, org, desc, tags } = attributes;
		const blockProps = useBlockProps( { className: 'sb-timeline-item' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Tags', 'smart-blocks' ) } initialOpen>
						<TagRepeater value={ tags } onChange={ ( v ) => setAttributes( { tags: v } ) } />
					</PanelBody>
				</InspectorControls>
				<li { ...blockProps }>
					<div className="sb-timeline-item__dot" aria-hidden="true" />
					<RichText tagName="span" className="sb-timeline-item__period" value={ period } onChange={ ( v ) => setAttributes( { period: v } ) } placeholder={ __( 'e.g. 2022 — Present', 'smart-blocks' ) } allowedFormats={ [] } />
					<RichText tagName="h3"   className="sb-timeline-item__role"   value={ role }   onChange={ ( v ) => setAttributes( { role:   v } ) } placeholder={ __( 'Role', 'smart-blocks' ) } allowedFormats={ [ 'core/bold' ] } />
					<RichText tagName="p"    className="sb-timeline-item__org"    value={ org }    onChange={ ( v ) => setAttributes( { org:    v } ) } placeholder={ __( 'Organisation', 'smart-blocks' ) } allowedFormats={ [ 'core/link' ] } />
					<RichText tagName="p"    className="sb-timeline-item__desc"   value={ desc }   onChange={ ( v ) => setAttributes( { desc:   v } ) } placeholder={ __( 'What you did', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
					{ tags?.length > 0 && (
						<div className="sb-timeline-item__tags">
							{ tags.map( ( t, i ) => ( <span key={ i } className="sb-tag">{ t }</span> ) ) }
						</div>
					) }
				</li>
			</>
		);
	},
	save( { attributes } ) {
		const { period, role, org, desc, tags = [] } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-timeline-item' } );
		return (
			<li { ...blockProps }>
				<div className="sb-timeline-item__dot" aria-hidden="true" />
				<RichText.Content tagName="span" className="sb-timeline-item__period" value={ period } />
				<RichText.Content tagName="h3"   className="sb-timeline-item__role"   value={ role } />
				<RichText.Content tagName="p"    className="sb-timeline-item__org"    value={ org } />
				<RichText.Content tagName="p"    className="sb-timeline-item__desc"   value={ desc } />
				{ tags.length > 0 && (
					<div className="sb-timeline-item__tags">
						{ tags.map( ( t, i ) => ( <span key={ i } className="sb-tag">{ t }</span> ) ) }
					</div>
				) }
			</li>
		);
	},
} );
