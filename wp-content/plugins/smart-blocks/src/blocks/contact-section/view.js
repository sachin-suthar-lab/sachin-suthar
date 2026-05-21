/**
 * Smart Blocks — Contact form (AJAX).
 *
 * Endpoint + nonce are injected via wp_localize_script as window.SmartBlocksContact.
 * Form runs inline validation, POSTs JSON to the REST endpoint, renders status.
 */
( function () {
	const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	const CFG = ( typeof window !== 'undefined' && window.SmartBlocksContact ) || {};

	function rule( name, value ) {
		const v = ( value || '' ).trim();
		if ( name === 'sb_name'    && v === '' )      return 'Please enter your name.';
		if ( name === 'sb_email'   && ! EMAIL_RE.test( v ) ) return 'Please enter a valid email address.';
		if ( name === 'sb_message' && v.length < 12 ) return 'Please write at least a sentence about your project.';
		return '';
	}
	function setError( form, name, msg ) {
		const field = form.querySelector( `[data-sb-field="${ name }"]` );
		if ( ! field ) return;
		const input = field.querySelector( 'input, textarea' );
		const err   = field.querySelector( '.sb-field__error' );
		if ( input ) input.setAttribute( 'aria-invalid', msg ? 'true' : 'false' );
		if ( err )   { err.textContent = msg || ''; err.classList.toggle( 'is-visible', !! msg ); }
	}
	function clearAll( form ) {
		form.querySelectorAll( '[data-sb-field]' ).forEach( ( f ) => {
			const input = f.querySelector( 'input, textarea' );
			const err   = f.querySelector( '.sb-field__error' );
			if ( input ) input.setAttribute( 'aria-invalid', 'false' );
			if ( err )   { err.textContent = ''; err.classList.remove( 'is-visible' ); }
		} );
	}
	function setStatus( form, type, msg ) {
		const s = form.querySelector( '[data-sb-status]' );
		if ( ! s ) return;
		s.classList.remove( 'is-ok', 'is-err', 'is-visible' );
		if ( type === 'ok' )  s.classList.add( 'is-ok',  'is-visible' );
		if ( type === 'err' ) s.classList.add( 'is-err', 'is-visible' );
		s.textContent = msg || '';
	}
	function setSubmitting( form, on ) {
		const btn = form.querySelector( '[data-sb-submit]' );
		if ( ! btn ) return;
		btn.disabled = !! on;
		btn.classList.toggle( 'is-loading', !! on );
	}

	function init( form ) {
		// Enable inputs that were rendered disabled-by-default in the editor preview.
		form.querySelectorAll( 'input, textarea, button' ).forEach( ( el ) => { el.disabled = false; } );

		const fields = [ 'sb_name', 'sb_email', 'sb_message' ];
		fields.forEach( ( name ) => {
			const input = form.querySelector( `[name="${ name }"]` );
			if ( ! input ) return;
			input.addEventListener( 'blur',  () => setError( form, name, rule( name, input.value ) ) );
			input.addEventListener( 'input', () => {
				if ( input.getAttribute( 'aria-invalid' ) === 'true' ) setError( form, name, rule( name, input.value ) );
			} );
		} );

		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			clearAll( form );
			setStatus( form, '', '' );

			let bad = false;
			fields.forEach( ( name ) => {
				const input = form.querySelector( `[name="${ name }"]` );
				const msg   = rule( name, input?.value );
				if ( msg ) { setError( form, name, msg ); bad = true; }
			} );
			if ( bad ) {
				setStatus( form, 'err', 'Please fix the highlighted fields and try again.' );
				return;
			}

			if ( ! CFG.endpoint || ! CFG.nonce ) {
				setStatus( form, 'err', 'Form not initialized. Please refresh the page.' );
				return;
			}

			setSubmitting( form, true );
			const data = {
				sb_name:    form.sb_name?.value || '',
				sb_email:   form.sb_email?.value || '',
				sb_company: form.sb_company?.value || '',
				sb_message: form.sb_message?.value || '',
				sb_website: form.sb_website?.value || '',
			};

			try {
				const res = await fetch( CFG.endpoint, {
					method:      'POST',
					credentials: 'same-origin',
					headers:     { 'Content-Type': 'application/json', 'X-WP-Nonce': CFG.nonce },
					body:        JSON.stringify( data ),
				} );
				const json = await res.json().catch( () => ( {} ) );

				if ( res.ok && json.ok ) {
					form.reset();
					setStatus( form, 'ok', json.message || 'Thanks — your message landed.' );
				} else {
					if ( json.errors ) {
						Object.keys( json.errors ).forEach( ( k ) => setError( form, k, json.errors[ k ] ) );
					}
					setStatus( form, 'err', json.message || 'Something went wrong. Please try again.' );
				}
			} catch ( err ) {
				setStatus( form, 'err', 'Network error. Please email me directly.' );
			} finally {
				setSubmitting( form, false );
			}
		} );
	}

	function boot() {
		document.querySelectorAll( '[data-sb-contact]' ).forEach( init );
	}
	if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', boot );
	else boot();
} )();
