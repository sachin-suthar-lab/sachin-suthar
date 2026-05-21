/* Smart Blocks — reveal-on-scroll. Tiny IntersectionObserver, respects reduced motion. */
( function () {
	if ( typeof window === 'undefined' ) return;

	var mql = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' );
	if ( mql && mql.matches ) {
		document.querySelectorAll( '.ss-reveal' ).forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
		return;
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		document.querySelectorAll( '.ss-reveal' ).forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
		return;
	}

	function run() {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					io.unobserve( entry.target );
				}
			} );
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 } );

		document.querySelectorAll( '.ss-reveal' ).forEach( function ( el ) {
			io.observe( el );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', run );
	} else {
		run();
	}
} )();
