import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { ICON_OPTIONS, Icon } from '../../shared/icons';
import metadata from './block.json';
import './style.scss';

const clampPct = ( v ) => Math.max( 0, Math.min( 100, parseInt( v, 10 ) || 0 ) );

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { icon, name, proficiency } = attributes;
		const pct = clampPct( proficiency );
		const blockProps = useBlockProps( { className: 'sb-skill' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Icon', 'smart-blocks' ) } initialOpen>
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
							{ icon && <span className="sb-skill__icon"><Icon name={ icon } size={ 18 } /></span> }
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
	save( { attributes } ) {
		const { icon, name, proficiency } = attributes;
		const pct = clampPct( proficiency );
		const blockProps = useBlockProps.save( { className: 'sb-skill' } );
		return (
			<div { ...blockProps }>
				<div className="sb-skill__head">
					<span className="sb-skill__name">
						{ icon && <span className="sb-skill__icon"><Icon name={ icon } size={ 18 } /></span> }
						<RichText.Content tagName="span" value={ name } />
					</span>
					<span className="sb-skill__pct">{ pct }%</span>
				</div>
				<div className="sb-skill__bar" role="progressbar" aria-valuenow={ pct } aria-valuemin={ 0 } aria-valuemax={ 100 }>
					<div className="sb-skill__fill" style={ { width: pct + '%' } } />
				</div>
			</div>
		);
	},
} );
