import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { ICON_OPTIONS, Icon } from '../../shared/icons';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { icon, name, meta } = attributes;
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
					<div className="sb-tech-card__icon"><Icon name={ icon } size={ 22 } /></div>
					<div className="sb-tech-card__body">
						<RichText
							tagName="span"
							className="sb-tech-card__name"
							value={ name }
							onChange={ ( v ) => setAttributes( { name: v } ) }
							placeholder={ __( 'Tech name', 'smart-blocks' ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="span"
							className="sb-tech-card__meta"
							value={ meta }
							onChange={ ( v ) => setAttributes( { meta: v } ) }
							placeholder={ __( 'e.g. 7+ years', 'smart-blocks' ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const { icon, name, meta } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-tech-card' } );
		return (
			<div { ...blockProps }>
				<div className="sb-tech-card__icon"><Icon name={ icon } size={ 22 } /></div>
				<div className="sb-tech-card__body">
					<RichText.Content tagName="span" className="sb-tech-card__name" value={ name } />
					{ meta && <RichText.Content tagName="span" className="sb-tech-card__meta" value={ meta } /> }
				</div>
			</div>
		);
	},
} );
