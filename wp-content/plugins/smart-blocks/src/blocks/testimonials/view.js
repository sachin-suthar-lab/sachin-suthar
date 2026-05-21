/**
 * Smart Blocks — Testimonials carousel driver.
 *
 * Vanilla, framework-free. Picks up [data-sb-carousel] roots, calculates how
 * many slides fit per page based on container width, wires Prev/Next/dots,
 * supports keyboard nav and touch swipe, and respects prefers-reduced-motion.
 */
( function () {
	const ROOT_SEL = '[data-sb-carousel]';

	function perPage( w ) {
		if ( w >= 1080 ) return 3;
		if ( w >= 720 )  return 2;
		return 1;
	}

	function init( root ) {
		const track    = root.querySelector( '.sb-testimonials__track' );
		const viewport = root.querySelector( '.sb-testimonials__viewport' );
		const dotsBox  = root.querySelector( '[data-sb-dots]' );
		const prevBtn  = root.querySelector( '[data-sb-prev]' );
		const nextBtn  = root.querySelector( '[data-sb-next]' );

		if ( ! track || ! viewport ) return;

		const slides = Array.from( track.children );
		if ( slides.length === 0 ) return;

		let page = 0;
		let pages = 1;
		let pp = perPage( viewport.clientWidth );

		function pagesFor( pp ) {
			return Math.max( 1, Math.ceil( slides.length / pp ) );
		}

		function buildDots() {
			dotsBox.innerHTML = '';
			for ( let i = 0; i < pages; i++ ) {
				const b = document.createElement( 'button' );
				b.type = 'button';
				b.setAttribute( 'aria-label', 'Go to page ' + ( i + 1 ) );
				b.dataset.idx = String( i );
				b.addEventListener( 'click', () => go( i ) );
				dotsBox.appendChild( b );
			}
		}

		function syncControls() {
			Array.from( dotsBox.children ).forEach( ( b, i ) => {
				b.setAttribute( 'aria-current', i === page ? 'true' : 'false' );
			} );
			prevBtn.disabled = page <= 0;
			nextBtn.disabled = page >= pages - 1;
		}

		function go( idx ) {
			page = Math.max( 0, Math.min( pages - 1, idx ) );
			const offset = -( page * 100 );
			track.style.transform = 'translate3d(' + offset + '%, 0, 0)';
			syncControls();
		}

		function recalc() {
			pp = perPage( viewport.clientWidth );
			pages = pagesFor( pp );
			if ( page > pages - 1 ) page = pages - 1;
			buildDots();
			go( page );
		}

		prevBtn?.addEventListener( 'click', () => go( page - 1 ) );
		nextBtn?.addEventListener( 'click', () => go( page + 1 ) );

		root.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'ArrowLeft' )  { e.preventDefault(); go( page - 1 ); }
			if ( e.key === 'ArrowRight' ) { e.preventDefault(); go( page + 1 ); }
		} );
		root.tabIndex = 0;

		/* Touch swipe */
		let touchStartX = 0; let touchDelta = 0;
		viewport.addEventListener( 'touchstart', ( e ) => { touchStartX = e.touches[0].clientX; touchDelta = 0; }, { passive: true } );
		viewport.addEventListener( 'touchmove',  ( e ) => { touchDelta = e.touches[0].clientX - touchStartX; }, { passive: true } );
		viewport.addEventListener( 'touchend',   () => {
			if ( Math.abs( touchDelta ) > 50 ) go( touchDelta < 0 ? page + 1 : page - 1 );
			touchDelta = 0;
		} );

		/* Resize handling */
		let raf = 0;
		const onResize = () => {
			cancelAnimationFrame( raf );
			raf = requestAnimationFrame( recalc );
		};
		window.addEventListener( 'resize', onResize );

		recalc();
	}

	function boot() {
		document.querySelectorAll( ROOT_SEL ).forEach( init );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
