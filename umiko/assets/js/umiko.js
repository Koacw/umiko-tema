/**
 * umiko · ön yüz (ES module, jQuery yok)
 *
 * - <dialog> ile mobil menü ve sepet çekmecesi
 * - WooCommerce Store API ile sepete ekleme / adet / silme (sayfa yenilenmeden)
 * - galeri, adet seçici, renk seçici, "gereksiz / gerekli" anahtarı
 * - çerez bandı + ileride analitik için onay kancası (window.umikoConsent)
 * - 404 çiçek bahçesi
 *
 * JS yoksa her şey yine çalışır: formlar WooCommerce'e normal POST eder.
 */

const data = JSON.parse( document.getElementById( 'wp-script-module-data-umiko' )?.textContent || '{}' );
const $ = ( sel, root = document ) => root.querySelector( sel );
const $$ = ( sel, root = document ) => [ ...root.querySelectorAll( sel ) ];

document.documentElement.classList.add( 'js' );

const announcer = $( '[data-announcer]' );
const announce = ( text ) => {
	if ( ! announcer ) return;
	announcer.textContent = '';
	requestAnimationFrame( () => ( announcer.textContent = text ) );
};

/* ---------------- dialoglar ---------------- */

const openDialog = ( id, opener ) => {
	const dialog = document.getElementById( id );
	if ( ! dialog || dialog.open ) return dialog;
	dialog.showModal();
	dialog._opener = opener || document.activeElement;
	return dialog;
};

document.addEventListener( 'click', ( e ) => {
	const opener = e.target.closest( '[data-open-dialog]' );
	if ( opener ) {
		const dialog = openDialog( opener.dataset.openDialog, opener );
		if ( opener.dataset.openDialog === 'cart-drawer' ) cart.refresh();
		return dialog;
	}
	const closer = e.target.closest( '[data-close-dialog]' );
	if ( closer ) closer.closest( 'dialog' )?.close();
} );

$$( 'dialog.sheet' ).forEach( ( dialog ) => {
	// closedby="any" desteklemeyen tarayıcılar için: arka plana tıklayınca kapan.
	dialog.addEventListener( 'click', ( e ) => {
		if ( e.target === dialog ) dialog.close();
	} );
	dialog.addEventListener( 'close', () => dialog._opener?.focus?.() );
	// menüde bir linke basınca kapan (aynı sayfadaki çapalar için)
	$$( 'a', dialog ).forEach( ( a ) => a.addEventListener( 'click', () => dialog.id === 'mobile-menu' && dialog.close() ) );
} );

/* ---------------- para biçimi (kuruş → ₺1.234,50) ---------------- */

const money = ( minor, c ) => {
	const unit = c?.currency_minor_unit ?? 2;
	const raw = String( minor ?? '0' );
	const negative = raw.startsWith( '-' );
	const digits = raw.replace( /\D/g, '' ).padStart( unit + 1, '0' );
	const int = digits.slice( 0, digits.length - unit ).replace( /\B(?=(\d{3})+(?!\d))/g, c?.currency_thousand_separator ?? '.' );
	const frac = unit ? ( c?.currency_decimal_separator ?? ',' ) + digits.slice( -unit ) : '';
	return `${ negative ? '-' : '' }${ c?.currency_prefix ?? '' }${ int }${ frac }${ c?.currency_suffix ?? '' }`;
};

const decode = ( html ) => new DOMParser().parseFromString( String( html ?? '' ), 'text/html' ).documentElement.textContent;

/* ---------------- Store API ---------------- */

const store = {
	nonce: data.nonce || '',
	async request( path, { method = 'GET', body, retry = true } = {} ) {
		const res = await fetch( data.storeApi + path, {
			method,
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json', Nonce: this.nonce },
			body: body ? JSON.stringify( body ) : undefined,
		} );
		const fresh = res.headers.get( 'Nonce' );
		if ( fresh ) this.nonce = fresh;
		const json = await res.json().catch( () => ( {} ) );
		// Sayfa önbellekteyse nonce eskimiş olabilir: sepeti bir kez okuyup taze nonce ile tekrar dene.
		if ( res.status === 403 && retry && /nonce/i.test( json.code || '' ) ) {
			await this.request( 'cart', { retry: false } );
			return this.request( path, { method, body, retry: false } );
		}
		if ( ! res.ok ) throw new Error( decode( json.message ) || 'bir şeyler ters gitti, tekrar dener misin?' );
		return json;
	},
};

/* ---------------- sepet çekmecesi ---------------- */

const drawer = $( '#cart-drawer' );

const cart = {
	data: null,

	async refresh() {
		if ( ! data.native ) return;
		try {
			this.render( await store.request( 'cart' ) );
		} catch ( err ) {
			this.error( err.message );
		}
	},

	setCount( count, bump = false ) {
		$$( '[data-cart-count]' ).forEach( ( el ) => {
			el.textContent = count;
			el.hidden = ! count;
			if ( bump ) {
				el.classList.remove( 'is-bumped' );
				void el.offsetWidth;
				el.classList.add( 'is-bumped' );
			}
		} );
	},

	error( message ) {
		const box = drawer && $( '[data-cart-error]', drawer );
		if ( ! box ) return;
		box.textContent = message || '';
		box.hidden = ! message;
	},

	render( c ) {
		this.data = c;
		this.setCount( c.items_count || 0 );
		if ( ! drawer ) return;
		this.error( '' );

		const list = $( '[data-cart-items]', drawer );
		const tpl = $( '[data-cart-item-template]', drawer );
		const empty = ! c.items?.length;
		$( '[data-cart-empty]', drawer ).hidden = ! empty;
		$( '[data-cart-foot]', drawer ).hidden = empty;
		list.hidden = empty;

		const totals = c.totals || {};
		const subtotal = Number( totals.total_items || 0 ) + Number( totals.total_items_tax || 0 );
		$( '[data-cart-subtotal]', drawer ).textContent = money( subtotal, totals );

		list.replaceChildren(
			...( c.items || [] ).map( ( item ) => {
				const li = tpl.content.firstElementChild.cloneNode( true );
				li.dataset.key = item.key;
				$$( '[data-f="link"]', li ).forEach( ( a ) => ( a.href = item.permalink ) );
				$( '[data-f="name"]', li ).textContent = decode( item.name );
				$( '[data-f="meta"]', li ).textContent = ( item.variation || [] ).map( ( v ) => `${ decode( v.attribute ) }: ${ decode( v.value ) }` ).join( ' · ' );
				$( '[data-f="qty"]', li ).textContent = item.quantity;
				$( '[data-f="price"]', li ).textContent = money( Number( item.prices?.price || 0 ) * item.quantity, item.prices );

				const media = $( '.cart-item__media', li );
				const img = item.images?.[ 0 ];
				if ( img ) {
					const el = new Image();
					el.src = img.thumbnail || img.src;
					el.alt = '';
					el.loading = 'lazy';
					media.append( el );
				} else {
					media.innerHTML = `<div class="ph ph--pink" aria-hidden="true"><svg class="flower ph__flower" viewBox="0 0 100 100"><g fill="currentColor"><circle cx="29" cy="29" r="29"/><circle cx="71" cy="29" r="29"/><circle cx="29" cy="71" r="29"/><circle cx="71" cy="71" r="29"/><circle cx="50" cy="50" r="30"/></g><circle class="flower__center" cx="50" cy="50" r="15.3"/></svg></div>`;
				}

				const max = item.quantity_limits?.maximum ?? 99;
				const min = item.quantity_limits?.minimum ?? 1;
				$( '[data-action="inc"]', li ).disabled = item.quantity >= max;
				$( '[data-action="dec"]', li ).disabled = item.quantity <= min;
				const name = decode( item.name );
				$( '[data-action="inc"] .sr-only', li ).textContent = `${ name }: bir artır`;
				$( '[data-action="dec"] .sr-only', li ).textContent = `${ name }: bir azalt`;
				$( '[data-action="remove"] .sr-only', li ).textContent = `${ name }: sepetten çıkar`;
				return li;
			} )
		);
	},

	async change( key, action ) {
		const li = drawer && $( `[data-key="${ CSS.escape( key ) }"]`, drawer );
		const item = this.data?.items?.find( ( i ) => i.key === key );
		if ( ! item ) return;
		li?.classList.add( 'is-busy' );
		try {
			const next =
				action === 'remove'
					? await store.request( 'cart/remove-item', { method: 'POST', body: { key } } )
					: await store.request( 'cart/update-item', { method: 'POST', body: { key, quantity: item.quantity + ( action === 'inc' ? 1 : -1 ) } } );
			this.render( next );
			announce( action === 'remove' ? `${ decode( item.name ) } sepetten çıkarıldı.` : 'sepet güncellendi.' );
			// çıkarılan satırın yerine odak kaybolmasın
			if ( action === 'remove' ) ( $( '[data-cart-items] .qty__btn', drawer ) || $( '[data-close-dialog]', drawer ) )?.focus();
		} catch ( err ) {
			li?.classList.remove( 'is-busy' );
			this.error( err.message );
		}
	},
};

drawer?.addEventListener( 'click', ( e ) => {
	const btn = e.target.closest( '[data-action]' );
	const li = btn?.closest( '[data-key]' );
	if ( btn && li ) cart.change( li.dataset.key, btn.dataset.action );
} );

// Sepette bir şey varsa (WooCommerce çerezi) sayacı tazele: sayfa önbellekten gelse de sayı doğru olsun.
if ( data.native && /woocommerce_items_in_cart=1/.test( document.cookie ) ) cart.refresh();

/* ---------------- sepete at ---------------- */

document.addEventListener( 'submit', async ( e ) => {
	const form = e.target.closest( '[data-umiko-add]' );
	if ( ! form || ! data.native ) return;
	e.preventDefault();

	const fd = new FormData( form );
	const id = Number( fd.get( 'variation_id' ) || fd.get( 'add-to-cart' ) );
	const quantity = Math.max( 1, Number( fd.get( 'quantity' ) || 1 ) );
	const button = $( '[type="submit"]', form );
	const label = $( 'span:not(.sr-only)', button );

	button.setAttribute( 'aria-busy', 'true' );
	button.disabled = true;
	try {
		const c = await store.request( 'cart/add-item', { method: 'POST', body: { id, quantity } } );
		cart.render( c );
		cart.setCount( c.items_count, true );
		if ( label ) label.textContent = 'sepette!';
		announce( 'sepete eklendi.' );
		openDialog( 'cart-drawer', button );
	} catch ( err ) {
		cart.render( cart.data || { items: [], totals: {} } );
		cart.error( err.message );
		openDialog( 'cart-drawer', button );
	} finally {
		button.removeAttribute( 'aria-busy' );
		button.disabled = false;
		setTimeout( () => label && ( label.textContent = button.dataset.label || 'sepete at' ), 1800 );
	}
} );

/* ---------------- adet ve renk seçici ---------------- */

document.addEventListener( 'click', ( e ) => {
	const step = e.target.closest( '[data-qty-step]' );
	if ( ! step ) return;
	const input = $( 'input', step.closest( '[data-qty]' ) );
	const next = Number( input.value || 1 ) + Number( step.dataset.qtyStep );
	input.value = Math.min( Number( input.max || 99 ), Math.max( Number( input.min || 1 ), next ) );
	input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
} );

document.addEventListener( 'change', ( e ) => {
	if ( e.target.matches( '.swatch input' ) ) {
		const out = $( '[data-swatch-output]', e.target.closest( 'fieldset' ) );
		if ( out ) out.textContent = e.target.dataset.name;
	}
	// WooCommerce sıralama menüsü (jQuery'siz)
	if ( e.target.matches( 'select.orderby' ) ) e.target.form?.submit();
} );

/* ---------------- galeri ---------------- */

$$( '[data-gallery]' ).forEach( ( gallery ) => {
	const track = $( '[data-gallery-track]', gallery );
	const thumbs = $$( '[data-gallery-go]', gallery );
	if ( ! thumbs.length ) return;
	thumbs.forEach( ( t ) =>
		t.addEventListener( 'click', () => {
			track.scrollTo( { left: track.clientWidth * Number( t.dataset.galleryGo ), behavior: 'smooth' } );
		} )
	);
	track.addEventListener(
		'scroll',
		() => {
			const i = Math.round( track.scrollLeft / track.clientWidth );
			thumbs.forEach( ( t, n ) => t.setAttribute( 'aria-current', String( n === i ) ) );
		},
		{ passive: true }
	);
} );

/* ---------------- gereksiz / gerekli anahtarı ---------------- */

document.addEventListener( 'click', ( e ) => {
	const sw = e.target.closest( '[data-confess-toggle]' );
	if ( ! sw ) return;
	const on = sw.getAttribute( 'aria-pressed' ) !== 'true';
	sw.setAttribute( 'aria-pressed', String( on ) );
	sw.closest( '[data-confess]' )?.classList.toggle( 'is-on', on );
} );

/* ---------------- çerez bandı + onay kancası ---------------- */

const consentKey = 'umiko-cookie-notice';
const safeGet = ( k ) => {
	try {
		return localStorage.getItem( k );
	} catch {
		return null;
	}
};
const safeSet = ( k, v ) => {
	try {
		localStorage.setItem( k, v );
	} catch {}
};

/**
 * İleride analitik eklenirse takip kodu yalnızca onaydan sonra yüklenmeli:
 *   window.umikoConsent.onGrant(() => { ...takip kodu... });
 * Faz 1'de zorunlu olmayan çerez yok; grant() hiçbir yerde çağrılmıyor.
 */
const consentQueue = [];
window.umikoConsent = {
	granted: safeGet( 'umiko-consent' ) === 'granted',
	onGrant( fn ) {
		this.granted ? fn() : consentQueue.push( fn );
	},
	grant() {
		this.granted = true;
		safeSet( 'umiko-consent', 'granted' );
		consentQueue.splice( 0 ).forEach( ( fn ) => fn() );
	},
};

const banner = $( '[data-cookie]' );
if ( banner && ! safeGet( consentKey ) ) {
	banner.hidden = false;
	$( '[data-cookie-ok]', banner )?.addEventListener( 'click', () => {
		safeSet( consentKey, 'seen' );
		banner.hidden = true;
	} );
}

/* ---------------- 404 bahçesi ---------------- */

const garden = $( '[data-garden]' );
if ( garden ) {
	let taps = 0;
	const counter = $( '[data-garden-count]' );
	const lines = [ 'bir çiçek döndü.', 'iki kere döndü. gereksiz ama tatlı.', 'tamamen gereksiz bir eğlence.', 'hâlâ buradasın. ürünlere bakalım mı?' ];
	garden.addEventListener( 'click', ( e ) => {
		const f = e.target.closest( '[data-garden-flower]' );
		if ( ! f ) return;
		f.classList.remove( 'is-pop' );
		void f.offsetWidth;
		f.classList.add( 'is-pop' );
		setTimeout( () => f.classList.remove( 'is-pop' ), 900 );
		taps += 1;
		counter.hidden = false;
		counter.textContent = lines[ Math.min( taps, lines.length ) - 1 ] + ( taps > lines.length ? ` (${ taps })` : '' );
	} );
}
