import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, RangeControl, TextControl } from '@wordpress/components';
import { ICON_OPTIONS, Icon } from '../../shared/icons';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { icon, title, desc, showBar, barLabel, proficiency } = attributes;
		const pct = Math.max( 0, Math.min( 100, parseInt( proficiency, 10 ) || 0 ) );
		const blockProps = useBlockProps( { className: 'sb-service' } );

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
					<PanelBody title={ __( 'Expertise bar', 'smart-blocks' ) } initialOpen>
						<ToggleControl
							label={ __( 'Show expertise bar', 'smart-blocks' ) }
							checked={ showBar }
							onChange={ ( v ) => setAttributes( { showBar: v } ) }
						/>
						{ showBar && (
							<>
								<TextControl label={ __( 'Bar label', 'smart-blocks' ) } value={ barLabel } onChange={ ( v ) => setAttributes( { barLabel: v } ) } />
								<RangeControl label={ __( 'Proficiency (%)', 'smart-blocks' ) } value={ pct } onChange={ ( v ) => setAttributes( { proficiency: v } ) } min={ 0 } max={ 100 } step={ 1 } />
							</>
						) }
					</PanelBody>
				</InspectorControls>

				<article { ...blockProps }>
					<div className="sb-service__head">
						<div className="sb-service__icon"><Icon name={ icon } size={ 22 } /></div>
						<RichText
							tagName="h3"
							className="sb-service__title"
							value={ title }
							onChange={ ( v ) => setAttributes( { title: v } ) }
							placeholder={ __( 'Service title', 'smart-blocks' ) }
							allowedFormats={ [ 'core/bold' ] }
						/>
					</div>
					<RichText
						tagName="p"
						className="sb-service__desc"
						value={ desc }
						onChange={ ( v ) => setAttributes( { desc: v } ) }
						placeholder={ __( 'One-sentence description.', 'smart-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
					{ showBar && (
						<div className="sb-service__bar-wrap">
							<div className="sb-service__bar-head">
								<span className="sb-service__bar-label">{ barLabel }</span>
								<span className="sb-service__bar-pct">{ pct }%</span>
							</div>
							<div className="sb-service__bar" role="progressbar" aria-valuenow={ pct } aria-valuemin={ 0 } aria-valuemax={ 100 }>
								<div className="sb-service__bar-fill" style={ { width: pct + '%' } } />
							</div>
						</div>
					) }
				</article>
			</>
		);
	},
	save( { attributes } ) {
		const { icon, title, desc, showBar, barLabel, proficiency } = attributes;
		const pct = Math.max( 0, Math.min( 100, parseInt( proficiency, 10 ) || 0 ) );
		const blockProps = useBlockProps.save( { className: 'sb-service' } );
		return (
			<article { ...blockProps }>
				<div className="sb-service__head">
					<div className="sb-service__icon"><Icon name={ icon } size={ 22 } /></div>
					{ title && <RichText.Content tagName="h3" className="sb-service__title" value={ title } /> }
				</div>
				{ desc && <RichText.Content tagName="p" className="sb-service__desc" value={ desc } /> }
				{ showBar && (
					<div className="sb-service__bar-wrap">
						<div className="sb-service__bar-head">
							<span className="sb-service__bar-label">{ barLabel }</span>
							<span className="sb-service__bar-pct">{ pct }%</span>
						</div>
						<div className="sb-service__bar" role="progressbar" aria-valuenow={ pct } aria-valuemin={ 0 } aria-valuemax={ 100 }>
							<div className="sb-service__bar-fill" style={ { width: pct + '%' } } />
						</div>
					</div>
				) }
			</article>
		);
	},
} );
