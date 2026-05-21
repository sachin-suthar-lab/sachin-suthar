/**
 * <SectionHead> — reusable editable section heading (eyebrow + h2 + dek).
 * Used by every section block so the markup and editing UX stay consistent.
 */
import { RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

export default function SectionHead( { attributes, setAttributes } ) {
	const { eyebrow = '', heading = '', dek = '' } = attributes;

	return (
		<div className="sb-section-head">
			<RichText
				tagName="span"
				className="sb-eyebrow"
				value={ eyebrow }
				onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
				placeholder={ __( 'Eyebrow', 'smart-blocks' ) }
				allowedFormats={ [] }
			/>
			<RichText
				tagName="h2"
				value={ heading }
				onChange={ ( v ) => setAttributes( { heading: v } ) }
				placeholder={ __( 'Section heading', 'smart-blocks' ) }
				allowedFormats={ [ 'core/bold', 'core/italic' ] }
			/>
			<RichText
				tagName="p"
				value={ dek }
				onChange={ ( v ) => setAttributes( { dek: v } ) }
				placeholder={ __( 'Short supporting paragraph', 'smart-blocks' ) }
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
			/>
		</div>
	);
}
