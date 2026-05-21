/**
 * Smart Blocks — Contact form (enterprise AJAX).
 *
 * Picks up [data-sb-contact] forms, runs client-side validation on input/blur,
 * submits via fetch to the REST endpoint, and renders a friendly status state.
 * Falls back to native HTML submission if fetch is unavailable.
 */
( function () {
	const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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
		if ( err ) {
			err.textContent = msg || '';
			err.classList.toggle( 'is-visible', !! msg );
		}
	}

	function clearAllErrors( form ) {
		form.querySelectorAll( '[data-sb-field]' ).forEach( ( f ) => {
			const input = f.querySelector( 'input, textarea' );
			const err   = f.querySelector( '.sb-field__error' );
			if ( input ) input.setAttribute( 'aria-invalid', 'false' );
			if ( err )   { err.textContent = ''; err.classList.remove( 'is-visible' ); }
		} );
	}

	function showStatus( form, type, msg ) {
		const status = form.querySelector( '[data-sb-status]' );
		if ( ! status ) return;
		status.classList.remove( 'is-ok', 'is-err', 'is-visible' );
		if ( type === 'ok' )  status.classList.add( 'is-ok',  'is-visible' );
		if ( type === 'err' ) status.classList.add( 'is-err', 'is-visible' );
		status.textContent = msg || '';
	}

	function setSubmitting( form, on ) {
		const btn = form.querySelector( '[data-sb-submit]' );
		if ( ! btn ) return;
		btn.disabled = !! on;
		btn.classList.toggle( 'is-loading', !! on );
	}

	function init( form ) {
		const endpoint = form.dataset.endpoint;
		const nonce    = form.dataset.nonce;
		if ( ! endpoint || ! nonce ) return; // No JS handshake — let native submission run.

		const fields = [ 'sb_name', 'sb_email', 'sb_message' ];

		fields.forEach( ( name ) => {
			const input = form.querySelector( `[name="${ name }"]` );
			if ( ! input ) return;
			input.addEventListener( 'blur',  () => setError( form, name, rule( name, input.value ) ) );
			input.addEventListener( 'input', () => {
				if ( input.getAttribute( 'aria-invalid' ) === 'true' ) {
					setError( form, name, rule( name, input.value ) );
				}
			} );
		} );

		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			clearAllErrors( form );
			showStatus( form, '', '' );

			// Client-side validation pass.
			let hasError = false;
			fields.forEach( ( name ) => {
				const input = form.querySelector( `[name="${ name }"]` );
				const msg   = rule( name, input?.value );
				if ( msg ) { setError( form, name, msg ); hasError = true; }
			} );
			if ( hasError ) {
				showStatus( form, 'err', 'Please fix the highlighted fields and try again.' );
				return;
			}

			setSubmitting( form, true );

			const data = {
				sb_name:    form.sb_name?.value || '',
				sb_email:   form.sb_email?.value || '',
				sb_company: form.sb_company?.value || '',
				sb_message: form.sb_message?.value || '',
				sb_website: form.sb_website?.value || '', // honeypot
			};

			try {
				const res = await fetch( endpoint, {
					method:      'POST',
					credentials: 'same-origin',
					headers:     {
						'Content-Type': 'application/json',
						'X-WP-Nonce':   nonce,
					},
					body: JSON.stringify( data ),
				} );
				const json = await res.json().catch( () => ( {} ) );

				if ( res.ok && json.ok ) {
					form.reset();
					showStatus( form, 'ok', json.message || 'Thanks — your message landed.' );
				} else {
					if ( json.errors && typeof json.errors === 'object' ) {
						Object.keys( json.errors ).forEach( ( k ) => setError( form, k, json.errors[ k ] ) );
					}
					showStatus( form, 'err', json.message || 'Something went wrong. Please try again.' );
				}
			} catch ( err ) {
				showStatus( form, 'err', 'Network error. Please email me directly.' );
			} finally {
				setSubmitting( form, false );
			}
		} );
	}

	function boot() {
		document.querySelectorAll( '[data-sb-contact]' ).forEach( init );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
