import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { name, proficiency } = attributes;
		const pct = Math.max( 0, Math.min( 100, parseInt( proficiency, 10 ) || 0 ) );
		const blockProps = useBlockProps( { className: 'sb-skill' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Proficiency', 'smart-blocks' ) } initialOpen>
						<RangeControl
							label={ __( 'Level (%)', 'smart-blocks' ) }
							value={ pct }
							onChange={ ( v ) => setAttributes( { proficiency: v } ) }
							min={ 0 }
							max={ 100 }
							step={ 1 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="sb-skill__head">
						<RichText
							tagName="span"
							className="sb-skill__name"
							value={ name }
							onChange={ ( v ) => setAttributes( { name: v } ) }
							placeholder={ __( 'Skill name', 'smart-blocks' ) }
							allowedFormats={ [] }
						/>
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
		const { name, proficiency } = attributes;
		const pct = Math.max( 0, Math.min( 100, parseInt( proficiency, 10 ) || 0 ) );
		const blockProps = useBlockProps.save( { className: 'sb-skill' } );
		return (
			<div { ...blockProps }>
				<div className="sb-skill__head">
					<RichText.Content tagName="span" className="sb-skill__name" value={ name } />
					<span className="sb-skill__pct">{ pct }%</span>
				</div>
				<div className="sb-skill__bar" role="progressbar" aria-valuenow={ pct } aria-valuemin={ 0 } aria-valuemax={ 100 } aria-label={ name }>
					<div className="sb-skill__fill" style={ { width: pct + '%' } } />
				</div>
			</div>
		);
	},
} );
