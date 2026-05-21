/**
 * <TagRepeater> — a compact comma-or-button editor for an array of short strings.
 * Used by Timeline Item and Project Card.
 */
import { __ } from '@wordpress/i18n';
import { Button, TextControl } from '@wordpress/components';

export default function TagRepeater( { value = [], onChange, label = __( 'Tags', 'smart-blocks' ) } ) {
	const tags = Array.isArray( value ) ? value : [];
	const update = ( i, v ) => onChange( tags.map( ( t, idx ) => ( idx === i ? v : t ) ) );
	const remove = ( i )    => onChange( tags.filter( ( _, idx ) => idx !== i ) );
	const add    = ()       => onChange( [ ...tags, '' ] );
	return (
		<div>
			<p style={ { margin: '0 0 .5rem', fontWeight: 500 } }>{ label }</p>
			{ tags.map( ( t, i ) => (
				<div key={ i } style={ { display: 'flex', gap: '.4rem', alignItems: 'center', marginBottom: '.4rem' } }>
					<TextControl value={ t } onChange={ ( v ) => update( i, v ) } __nextHasNoMarginBottom hideLabelFromVision label={ `Tag ${ i + 1 }` } />
					<Button isDestructive variant="tertiary" icon="trash" label={ __( 'Remove', 'smart-blocks' ) } onClick={ () => remove( i ) } />
				</div>
			) ) }
			<Button variant="secondary" onClick={ add }>{ __( '+ Add tag', 'smart-blocks' ) }</Button>
		</div>
	);
}
