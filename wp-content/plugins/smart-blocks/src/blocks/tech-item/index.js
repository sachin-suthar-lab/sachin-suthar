import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { ICON_OPTIONS, IconPreview } from '../../shared/icon-options';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { icon, name } = attributes;
		const blockProps = useBlockProps( { className: 'sb-tech-card' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Icon', 'smart-blocks' ) } initialOpen>
						<SelectControl
							label={ __( 'Choose icon', 'smart-blocks' ) }
							value={ icon }
							options={ ICON_OPTIONS }
							onChange={ ( v ) => setAttributes( { icon: v } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="sb-tech-card__icon"><IconPreview name={ icon } /></div>
					<RichText
						tagName="div"
						className="sb-tech-card__name"
						value={ name }
						onChange={ ( v ) => setAttributes( { name: v } ) }
						placeholder={ __( 'Tech name', 'smart-blocks' ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save: () => null,
} );
