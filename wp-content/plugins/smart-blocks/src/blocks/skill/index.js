import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { ICON_OPTIONS, IconPreview } from '../../shared/icon-options';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { icon, name, proficiency } = attributes;
		const pct = Math.max( 0, Math.min( 100, parseInt( proficiency, 10 ) || 0 ) );
		const blockProps = useBlockProps( { className: 'sb-skill' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Icon', 'smart-blocks' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Choose icon (optional)', 'smart-blocks' ) }
							value={ icon }
							options={ ICON_OPTIONS }
							onChange={ ( v ) => setAttributes( { icon: v } ) }
						/>
					</PanelBody>
					<PanelBody title={ __( 'Proficiency', 'smart-blocks' ) } initialOpen>
						<RangeControl
							label={ __( 'Level (%)', 'smart-blocks' ) }
							value={ pct }
							onChange={ ( v ) => setAttributes( { proficiency: v } ) }
							min={ 0 } max={ 100 } step={ 1 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="sb-skill__head">
						<span className="sb-skill__name">
							{ icon && <span className="sb-skill__icon"><IconPreview name={ icon } /></span> }
							<RichText
								tagName="span"
								value={ name }
								onChange={ ( v ) => setAttributes( { name: v } ) }
								placeholder={ __( 'Skill name', 'smart-blocks' ) }
								allowedFormats={ [] }
							/>
						</span>
						<span className="sb-skill__pct">{ pct }%</span>
					</div>
					<div className="sb-skill__bar" role="progressbar" aria-valuenow={ pct } aria-valuemin={ 0 } aria-valuemax={ 100 }>
						<div className="sb-skill__fill" style={ { width: pct + '%' } } />
					</div>
				</div>
			</>
		);
	},
	save: () => null,
} );
