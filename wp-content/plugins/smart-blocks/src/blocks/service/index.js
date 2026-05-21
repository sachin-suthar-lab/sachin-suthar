import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { ICON_OPTIONS, Icon } from '../../shared/icons';
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
					<PanelBody title={ __( 'Icon', 'sachins-blocks' ) } initialOpen>
						<SelectControl
							label={ __( 'Choose icon', 'sachins-blocks' ) }
							value={ icon }
							options={ ICON_OPTIONS }
							onChange={ ( v ) => setAttributes( { icon: v } ) }
						/>
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
							placeholder={ __( 'Service title', 'sachins-blocks' ) }
							allowedFormats={ [ 'core/bold' ] }
						/>
					</div>
					<RichText
						tagName="p"
						className="sb-service__desc"
						value={ desc }
						onChange={ ( v ) => setAttributes( { desc: v } ) }
						placeholder={ __( 'One-sentence description.', 'sachins-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
				</article>
			</>
		);
	},
	save( { attributes } ) {
		const { icon, title, desc } = attributes;
		const blockProps = useBlockProps.save( { className: 'sb-service' } );
		return (
			<article { ...blockProps }>
				<div className="sb-service__head">
					<div className="sb-service__icon"><Icon name={ icon } size={ 22 } /></div>
					{ title && <RichText.Content tagName="h3" className="sb-service__title" value={ title } /> }
				</div>
				{ desc && <RichText.Content tagName="p" className="sb-service__desc" value={ desc } /> }
			</article>
		);
	},
} );
