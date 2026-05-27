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
		const blockProps = useBlockProps( { className: 'sb-skill' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Icon', 'sachins-blocks' ) } initialOpen>
						<SelectControl
							label={ __( 'Choose icon (optional)', 'sachins-blocks' ) }
							value={ icon }
							options={ ICON_OPTIONS }
							onChange={ ( v ) => setAttributes( { icon: v } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					{ icon && <span className="sb-skill__icon"><Icon name={ icon } size={ 20 } /></span> }
					<span className="sb-skill__body">
						<RichText
							tagName="span"
							className="sb-skill__name"
							value={ name }
							onChange={ ( v ) => setAttributes( { name: v } ) }
							placeholder={ __( 'Expertise area', 'sachins-blocks' ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="span"
							className="sb-skill__meta"
							value={ meta }
							onChange={ ( v ) => setAttributes( { meta: v } ) }
							placeholder={ __( 'Short descriptor', 'sachins-blocks' ) }
							allowedFormats={ [] }
						/>
					</span>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const { icon, name, meta } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-skill' } );
		return (
			<div { ...blockProps }>
				{ icon && <span className="sb-skill__icon"><Icon name={ icon } size={ 20 } /></span> }
				<span className="sb-skill__body">
					<RichText.Content tagName="span" className="sb-skill__name" value={ name } />
					{ meta && <RichText.Content tagName="span" className="sb-skill__meta" value={ meta } /> }
				</span>
			</div>
		);
	},
} );
