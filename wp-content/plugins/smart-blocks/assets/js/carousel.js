/**
 * Smart Blocks — generic carousel driver.
 *
 * Targets any element with [data-sb-carousel]. Finds the inner *__track
 * (looks for whichever child contains `__track` in its class list), then
 * wires Prev / Next / dots, keyboard arrows, touch swipe, responsive
 * pages-per-view, and prefers-reduced-motion.
 */
( function () {
	function trackOf( root ) {
		// Find a descendant whose class contains '__track'.
		const all = root.querySelectorAll( '*' );
		for ( const el of all ) {
			if ( el.classList && Array.from( el.classList ).some( ( c ) => c.endsWith( '__track' ) ) ) return el;
		}
		return null;
	}

	function perPage( w ) {
		if ( w >= 1080 ) return 3;
		if ( w >= 720 )  return 2;
		return 1;
	}

	function init( root ) {
		const track    = trackOf( root );
		const viewport = track ? track.parentElement : null;
		const dotsBox  = root.querySelector( '[data-sb-dots]' );
		const prevBtn  = root.querySelector( '[data-sb-prev]' );
		const nextBtn  = root.querySelector( '[data-sb-next]' );
		if ( ! track || ! viewport ) return;

		const slides = Array.from( track.children );
		if ( slides.length === 0 ) return;

		let page  = 0;
		let pages = 1;
		let pp    = perPage( viewport.clientWidth );

		const reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' );
		if ( reduce && reduce.matches ) {
			track.style.transition = 'none';
		}

		function buildDots() {
			if ( ! dotsBox ) return;
			dotsBox.innerHTML = '';
			for ( let i = 0; i < pages; i++ ) {
				const b = document.createElement( 'button' );
				b.type = 'button';
				b.setAttribute( 'aria-label', 'Go to page ' + ( i + 1 ) );
				b.addEventListener( 'click', () => go( i ) );
				dotsBox.appendChild( b );
			}
		}
		function syncControls() {
			if ( dotsBox ) {
				Array.from( dotsBox.children ).forEach( ( b, i ) => b.setAttribute( 'aria-current', i === page ? 'true' : 'false' ) );
			}
			if ( prevBtn ) prevBtn.disabled = page <= 0;
			if ( nextBtn ) nextBtn.disabled = page >= pages - 1;
		}
		function go( idx ) {
			page = Math.max( 0, Math.min( pages - 1, idx ) );
			track.style.transform = 'translate3d(' + ( -page * 100 ) + '%, 0, 0)';
			syncControls();
		}
		function recalc() {
			pp    = perPage( viewport.clientWidth );
			pages = Math.max( 1, Math.ceil( slides.length / pp ) );
			if ( page > pages - 1 ) page = pages - 1;
			buildDots();
			go( page );
		}

		if ( prevBtn ) prevBtn.addEventListener( 'click', () => go( page - 1 ) );
		if ( nextBtn ) nextBtn.addEventListener( 'click', () => go( page + 1 ) );

		root.tabIndex = 0;
		root.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'ArrowLeft' )  { e.preventDefault(); go( page - 1 ); }
			if ( e.key === 'ArrowRight' ) { e.preventDefault(); go( page + 1 ); }
		} );

		let startX = 0, delta = 0;
		viewport.addEventListener( 'touchstart', ( e ) => { startX = e.touches[0].clientX; delta = 0; }, { passive: true } );
		viewport.addEventListener( 'touchmove',  ( e ) => { delta = e.touches[0].clientX - startX; }, { passive: true } );
		viewport.addEventListener( 'touchend',   () => {
			if ( Math.abs( delta ) > 50 ) go( delta < 0 ? page + 1 : page - 1 );
			delta = 0;
		} );

		let raf = 0;
		window.addEventListener( 'resize', () => { cancelAnimationFrame( raf ); raf = requestAnimationFrame( recalc ); } );

		recalc();
	}

	function boot() { document.querySelectorAll( '[data-sb-carousel]' ).forEach( init ); }
	if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', boot );
	else boot();
} )();
