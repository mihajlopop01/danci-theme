/**
 * Danci Shop interactions (mirrors the mockup's DCLogic state machine):
 * mobile menu, home-card swatches + size chips + quick add, product
 * colour/size/qty/gallery, size-guide popup, AJAX add-to-cart +
 * "Dodato u korpu" drawer, cart +/-.
 */
( function () {
	'use strict';

	const $ = ( sel, root = document ) => root.querySelector( sel );
	const $$ = ( sel, root = document ) => Array.from( root.querySelectorAll( sel ) );

	/* ---------- Overlays (size guide, drawer) ---------- */
	let lastFocus = null;
	function openOverlay( el ) {
		if ( ! el ) return;
		lastFocus = document.activeElement;
		el.hidden = false;
		document.body.classList.add( 'd-locked' );
		const close = $( '[data-d-close]', el );
		if ( close ) close.focus();
	}
	function closeOverlay( el ) {
		el.hidden = true;
		document.body.classList.remove( 'd-locked' );
		if ( lastFocus ) lastFocus.focus();
	}
	$$( '.d-overlay' ).forEach( ( el ) => {
		el.addEventListener( 'click', ( e ) => {
			if ( e.target === el || e.target.closest( 'button[data-d-close]' ) ) closeOverlay( el );
			// "Nastavi kupovinu" on the home page just closes the drawer.
			const link = e.target.closest( '[data-d-close-link]' );
			if ( link && document.getElementById( 'proizvodi' ) ) {
				e.preventDefault();
				closeOverlay( el );
				document.getElementById( 'proizvodi' ).scrollIntoView( { behavior: 'smooth' } );
			}
		} );
	} );
	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key !== 'Escape' ) return;
		$$( '.d-overlay' ).filter( ( el ) => ! el.hidden ).forEach( closeOverlay );
		closeMenu();
	} );

	/* ---------- Mobile menu ---------- */
	const menu = $( '[data-d-menu]' );
	function closeMenu() {
		if ( ! menu ) return;
		menu.hidden = true;
		$$( '[data-d-menu-toggle]' ).forEach( ( b ) => b.setAttribute( 'aria-expanded', 'false' ) );
	}
	$$( '[data-d-menu-toggle]' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', ( e ) => {
			e.stopPropagation();
			if ( ! menu ) return;
			menu.hidden = ! menu.hidden;
			btn.setAttribute( 'aria-expanded', String( ! menu.hidden ) );
		} );
	} );
	document.addEventListener( 'click', ( e ) => {
		if ( menu && ! menu.hidden && ! menu.contains( e.target ) ) closeMenu();
	} );
	if ( menu ) menu.addEventListener( 'click', ( e ) => e.target.closest( 'a' ) && closeMenu() );

	/* ---------- Shared: AJAX add + drawer ---------- */
	function formatPrice( n ) {
		return Math.round( n ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, '.' ) + ' RSD';
	}

	function applyFragments( fragments ) {
		Object.keys( fragments ).forEach( ( sel ) => {
			let nodes;
			try {
				nodes = $$( sel );
			} catch ( err ) {
				return;
			}
			nodes.forEach( ( node ) => {
				const tpl = document.createElement( 'template' );
				tpl.innerHTML = fragments[ sel ].trim();
				if ( tpl.content.firstElementChild ) node.replaceWith( tpl.content.firstElementChild );
			} );
		} );
	}

	/** Resolves true when added; false when the caller should fall back to a normal request. */
	function ajaxAdd( productId, qty, button ) {
		if ( ! window.DANCI || ! DANCI.addToCartUrl || ! window.fetch ) return Promise.resolve( false );
		return fetch( DANCI.addToCartUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams( { product_id: productId, quantity: qty } ),
		} )
			.then( ( r ) => r.json() )
			.then( ( data ) => {
				if ( ! data || data.error ) return false;
				applyFragments( data.fragments || {} );
				if ( window.jQuery ) {
					window.jQuery( document.body ).trigger( 'added_to_cart', [ data.fragments, data.cart_hash, window.jQuery( button ) ] );
				}
				return true;
			} )
			.catch( () => false );
	}

	function showDrawer( { img, name, variant, total } ) {
		const drawer = $( '[data-d-drawer]' );
		if ( ! drawer ) return;
		$( '[data-d-drawer-img]', drawer ).src = img;
		$( '[data-d-drawer-name]', drawer ).textContent = name;
		$( '[data-d-drawer-variant]', drawer ).textContent = variant;
		$( '[data-d-drawer-price]', drawer ).textContent = total;
		openOverlay( drawer );
	}

	/* ---------- Home / shop cards ---------- */
	$$( '[data-d-card]' ).forEach( ( card ) => {
		const variations = JSON.parse( card.dataset.variations || '[]' );
		const img = $( '[data-d-card-img]', card );
		const colorLabel = $( '[data-d-card-color]', card );
		const priceEl = $( '[data-d-card-price]', card );
		const sizeBtns = $$( '[data-d-card-size]', card );
		const sizesBox = $( '[data-d-card-sizes]', card );
		const error = $( '[data-d-card-error]', card );
		const addBtn = $( '[data-d-card-add]', card );
		const state = { color: card.dataset.color, colorLabel: card.dataset.colorLabel, size: '', sizeLabel: '' };
		const hasColor = variations.some( ( v ) => v.color );

		const find = () => variations.find( ( v ) => ( ! hasColor || v.color === state.color ) && v.size === state.size );

		function render() {
			sizeBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.size;
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
				b.disabled = ! variations.some( ( v ) => v.size === b.dataset.value && ( ! hasColor || v.color === state.color ) && v.inStock );
			} );
			const v = find();
			priceEl.textContent = v ? v.priceS : card.dataset.price;
		}

		$$( '[data-d-swatch]', card ).forEach( ( sw ) => {
			sw.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				$$( '[data-d-swatch]', card ).forEach( ( s ) => {
					s.classList.toggle( 'is-selected', s === sw );
					s.setAttribute( 'aria-pressed', String( s === sw ) );
				} );
				state.color = sw.dataset.value;
				state.colorLabel = sw.dataset.label;
				if ( sw.dataset.image ) img.src = sw.dataset.image;
				if ( colorLabel ) colorLabel.textContent = sw.dataset.label;
				$$( '[data-d-card-link]', card ).forEach( ( a ) => ( a.href = sw.dataset.href ) );
				if ( addBtn ) addBtn.href = sw.dataset.href;
				render();
			} );
		} );

		sizeBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.size = b.dataset.value;
				state.sizeLabel = b.dataset.label;
				if ( error ) error.hidden = true;
				if ( sizesBox ) sizesBox.classList.remove( 'is-error' );
				render();
			} )
		);

		if ( addBtn ) {
			addBtn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const v = find();
				if ( ! v || ! v.inStock ) {
					if ( error ) error.hidden = false;
					if ( sizesBox ) sizesBox.classList.add( 'is-error' );
					return;
				}
				addBtn.classList.add( 'is-busy' );
				ajaxAdd( v.id, 1, addBtn ).then( ( ok ) => {
					addBtn.classList.remove( 'is-busy' );
					if ( ! ok ) {
						window.location.href = addBtn.href;
						return;
					}
					showDrawer( {
						img: v.thumb || img.src,
						name: card.dataset.name,
						variant: [ hasColor ? state.colorLabel : '', 'Veličina ' + state.sizeLabel, '1 kom' ].filter( Boolean ).join( ' · ' ),
						total: v.priceS,
					} );
					state.size = '';
					state.sizeLabel = '';
					render();
				} );
			} );
		}

		render();
	} );

	/* ---------- Product page ---------- */
	const form = $( '[data-d-buy]' );
	if ( form ) initProduct( form );

	function initProduct( form ) {
		const variations = JSON.parse( form.dataset.variations || '[]' );
		const mainImg = $( '[data-d-main-img]' );
		const thumbs = $$( '[data-d-thumb]' );
		const colorBtns = $$( '[data-d-color]', form );
		const sizeBtns = $$( '[data-d-size]', form );
		const colorInput = $( '[data-d-color-input]', form );
		const sizeInput = $( '[data-d-size-input]', form );
		const variationInput = $( '[data-d-variation]', form );
		const qtyInput = $( '[data-d-qty-input]', form );
		const error = $( '[data-d-error]', form );
		const initialColorBtn = colorBtns.find( ( b ) => b.dataset.value === ( colorInput ? colorInput.value : '' ) );
		const state = {
			color: colorInput ? colorInput.value : '', // slug/value posted to WooCommerce
			colorLabel: initialColorBtn ? initialColorBtn.dataset.label : '',
			size: '',
			sizeLabel: '',
			qty: 1,
		};

		function setMain( src, id ) {
			if ( src ) mainImg.src = src;
			thumbs.forEach( ( t ) => t.classList.toggle( 'is-active', String( t.dataset.id ) === String( id ) ) );
		}

		const findVariation = () =>
			variations.find( ( v ) => ( ! colorInput || v.color === state.color ) && ( ! sizeInput || v.size === state.size ) );

		function render() {
			$$( '[data-d-color-label]' ).forEach( ( el ) => ( el.textContent = state.colorLabel ) );
			$$( '[data-d-size-label]' ).forEach( ( el ) => ( el.textContent = state.sizeLabel || '—' ) );
			colorBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.color;
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
			} );
			sizeBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.size;
				b.disabled = ! variations.some(
					( v ) => v.size === b.dataset.value && ( ! colorInput || v.color === state.color ) && v.inStock
				);
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
			} );
			$( '[data-d-qty-value]', form ).textContent = state.qty;
			qtyInput.value = state.qty;
			if ( colorInput ) colorInput.value = state.color;
			if ( sizeInput ) sizeInput.value = state.size;
			const v = findVariation();
			variationInput.value = v && v.inStock ? v.id : '';
			$$( '[data-d-price]', form ).forEach( ( el ) => ( el.textContent = v && state.size ? v.priceS : form.dataset.price ) );
		}

		colorBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.color = b.dataset.value;
				state.colorLabel = b.dataset.label;
				// Keep the size only if it exists in stock for the new colour.
				if ( state.size && ! variations.some( ( v ) => v.color === state.color && v.size === state.size && v.inStock ) ) {
					state.size = '';
					state.sizeLabel = '';
				}
				setMain( b.dataset.image, b.dataset.imageId );
				render();
			} )
		);
		sizeBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.size = b.dataset.value;
				state.sizeLabel = b.dataset.label;
				if ( error ) error.hidden = true;
				$( '[data-d-sizes]', form ).classList.remove( 'is-error' );
				render();
			} )
		);
		thumbs.forEach( ( t ) => t.addEventListener( 'click', () => setMain( t.dataset.large, t.dataset.id ) ) );
		$$( '[data-d-qty]', form ).forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.qty = Math.max( 1, state.qty + Number( b.dataset.dQty ) );
				render();
			} )
		);

		const guide = $( '[data-d-guide]' );
		$$( '[data-d-guide-open]' ).forEach( ( b ) => b.addEventListener( 'click', () => openOverlay( guide ) ) );

		function needsSize() {
			if ( variationInput.value || ! sizeInput ) return false;
			if ( error ) error.hidden = false;
			const sizes = $( '[data-d-sizes]', form );
			if ( sizes ) {
				sizes.classList.add( 'is-error' );
				sizes.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
			return true;
		}

		form.addEventListener( 'submit', ( e ) => {
			const submitter = e.submitter;
			if ( needsSize() ) {
				e.preventDefault();
				return;
			}
			// "Kupi odmah" posts normally (server redirects to checkout).
			if ( submitter && submitter.name === 'danci_buy_now' ) return;
			if ( ! window.DANCI || ! DANCI.addToCartUrl || ! window.fetch ) return;
			e.preventDefault();
			const v = findVariation();
			const qty = state.qty;
			$$( '[data-d-add]', form ).forEach( ( b ) => ( b.disabled = true ) );
			ajaxAdd( v ? v.id : form.querySelector( '[name="add-to-cart"]' ).value, qty, submitter ).then( ( ok ) => {
				$$( '[data-d-add]', form ).forEach( ( b ) => ( b.disabled = false ) );
				if ( ! ok ) {
					// Let WooCommerce show its own error (e.g. out of stock) via a normal post.
					HTMLFormElement.prototype.submit.call( form );
					return;
				}
				const parts = [];
				if ( colorInput && state.colorLabel ) parts.push( state.colorLabel );
				if ( sizeInput ) parts.push( 'Veličina ' + state.sizeLabel );
				parts.push( qty + ' kom' );
				showDrawer( {
					img: ( v && v.thumb ) || mainImg.src,
					name: form.dataset.name,
					variant: parts.join( ' · ' ),
					total: formatPrice( ( v ? v.price : 0 ) * qty ),
				} );
				state.qty = 1;
				render();
			} );
		} );

		render();
	}

	/* ---------- Cart page: +/- trigger WooCommerce's AJAX cart update ---------- */
	document.addEventListener( 'click', ( e ) => {
		const btn = e.target.closest( '[data-d-cart-qty] button[data-step]' );
		if ( ! btn ) return;
		const input = $( 'input.qty', btn.parentElement );
		const next = Math.max( 1, ( parseInt( input.value, 10 ) || 1 ) + Number( btn.dataset.step ) );
		if ( String( next ) === input.value ) return;
		input.value = next;
		const update = $( '.woocommerce-cart-form [name="update_cart"]' );
		if ( update ) {
			update.disabled = false;
			update.click();
		}
	} );

	// Keep the header badge in sync after WooCommerce's AJAX cart update / removal.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_wc_div', () => {
			const cartForm = $( '.woocommerce-cart-form' );
			const count = cartForm ? cartForm.dataset.count : '0';
			$$( '.d-cart-count' ).forEach( ( el ) => ( el.textContent = count ) );
		} );
	}
} )();
