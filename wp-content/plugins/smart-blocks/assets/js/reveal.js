/* Smart Blocks — reveal-on-scroll.
   Adds html.sb-anim-ready to gate the CSS opacity:0; observer adds .is-visible
   as each .sb-reveal / .ss-reveal enters the viewport. Hero (already in view)
   is shown synchronously to avoid first-paint flash. */
( function () {
	if ( typeof window === 'undefined' ) return;

	// Keep footer copyright year current.
	function stampYear() {
		var y = String( new Date().getFullYear() );
		document.querySelectorAll( '[data-sb-year]' ).forEach( function ( el ) { el.textContent = y; } );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', stampYear );
	} else {
		stampYear();
	}

	var SEL = '.sb-reveal, .ss-reveal';

	function isInView( el ) {
		var r = el.getBoundingClientRect();
		var vh = window.innerHeight || document.documentElement.clientHeight;
		return r.top < vh && r.bottom > 0;
	}

	function run() {
		// Arm the CSS reveal styles.
		document.documentElement.classList.add( 'sb-anim-ready' );

		var nodes = Array.prototype.slice.call( document.querySelectorAll( SEL ) );

		// Immediately reveal anything already in view to avoid a flash.
		nodes.forEach( function ( el ) {
			if ( isInView( el ) ) el.classList.add( 'is-visible' );
		} );

		var mql = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' );
		if ( ( mql && mql.matches ) || ! ( 'IntersectionObserver' in window ) ) {
			nodes.forEach( function ( el ) { el.classList.add( 'is-visible' ); } );
			return;
		}

		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					io.unobserve( entry.target );
				}
			} );
		}, { rootMargin: '0px 0px -5% 0px', threshold: 0.06 } );

		nodes.forEach( function ( el ) {
			if ( ! el.classList.contains( 'is-visible' ) ) io.observe( el );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', run );
	} else {
		run();
	}
} )();
