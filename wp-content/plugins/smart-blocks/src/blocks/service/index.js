import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { ICON_OPTIONS, IconPreview } from '../../shared/icon-options';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { icon, title, desc } = attributes;
		const blockProps = useBlockProps( { className: 'sb-service' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Icon', 'smart-blocks' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Choose icon', 'smart-blocks' ) }
							value={ icon }
							options={ ICON_OPTIONS }
							onChange={ ( v ) => setAttributes( { icon: v } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<article { ...blockProps }>
					<div className="sb-service__icon"><IconPreview name={ icon } /></div>
					<RichText
						tagName="h3"
						className="sb-service__title"
						value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						placeholder={ __( 'Service title', 'smart-blocks' ) }
						allowedFormats={ [ 'core/bold' ] }
					/>
					<RichText
						tagName="p"
						className="sb-service__desc"
						value={ desc }
						onChange={ ( v ) => setAttributes( { desc: v } ) }
						placeholder={ __( 'One-sentence description.', 'smart-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
				</article>
			</>
		);
	},
	save: () => null, // dynamic — rendered by render.php
} );
