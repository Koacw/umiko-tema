<?php
/**
 * Tema kurulumu, varlıkların yüklenmesi, WooCommerce ince ayarları.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 640,
				'single_image_width'    => 1100,
				'product_grid'          => array(
					'default_columns' => 3,
					'min_columns'     => 2,
					'max_columns'     => 4,
				),
			)
		);
		// Galeriyi tema kendisi çiziyor (scroll-snap). WooCommerce'in flexslider / zoom / photoswipe'ı yüklenmez.

		register_nav_menus(
			array(
				'primary' => 'üst menü',
				'legal'   => 'footer: yasal sayfalar',
			)
		);
	}
);

// Klasik tema olsa da yalnızca sayfada kullanılan blokların CSS'i yüklensin.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

// WooCommerce'in klasik CSS'i yerine temanın kendi stilleri. (Sepet/ödeme bloklarının stilleri yerinde kalır.)
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// Başlık ayracı: "ürünler · umiko"
add_filter( 'document_title_separator', fn() => '·' );

add_filter( 'loop_shop_per_page', fn() => 24 );

// Sıralama seçenekleri marka diliyle. (Yorum özelliği kapsam dışı, "puana göre" çıkarıldı.)
add_filter(
	'woocommerce_catalog_orderby',
	fn() => array(
		'menu_order' => 'bizim sıramız',
		'date'       => 'en yeniler',
		'popularity' => 'en sevilenler',
		'price'      => 'fiyat: önce uygun',
		'price-desc' => 'fiyat: önce yüksek',
	)
);

// Mağaza sayfasının başlığı her dilde "ürünler".
add_filter(
	'document_title_parts',
	function ( array $parts ): array {
		if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
			$parts['title'] = 'ürünler';
		}
		if ( is_404() ) {
			$parts['title'] = 'bu sayfa gereksizmiş';
		}
		return $parts;
	}
);

/**
 * WooCommerce "sipariş kaynağı" takibi sbjs_* çerezleri bırakır. Bunlar zorunlu çerez değil;
 * ileride çerez onayı eklenince `umiko_tracking_allowed` filtresiyle açılabilir.
 */
add_filter( 'wc_order_attribution_allow_tracking', fn() => (bool) apply_filters( 'umiko_tracking_allowed', false ) );

add_action(
	'wp_enqueue_scripts',
	function () {
		// Sürüm = dosya değişiklik zamanı: CSS/JS güncellenince tarayıcı önbelleği kendiliğinden yenilenir.
		$ver = fn( $file ) => UMIKO_VERSION . '.' . filemtime( UMIKO_DIR . $file );
		wp_enqueue_style( 'umiko', UMIKO_URI . '/assets/css/umiko.css', array(), $ver( '/assets/css/umiko.css' ) );

		wp_enqueue_script_module( 'umiko', UMIKO_URI . '/assets/js/umiko.js', array(), $ver( '/assets/js/umiko.js' ) );

		// Tema jQuery'ye ihtiyaç duymuyor. Hesap, sepet ve ödeme sayfaları dışında WooCommerce'in eski scriptlerini çıkar.
		$needs_wc_js = function_exists( 'is_account_page' ) && ( is_account_page() || is_cart() || is_checkout() );
		if ( ! $needs_wc_js ) {
			foreach ( array( 'woocommerce', 'wc-add-to-cart', 'wc-add-to-cart-variation', 'wc-single-product', 'wc-cart-fragments', 'sourcebuster-js', 'wc-order-attribution' ) as $handle ) {
				wp_dequeue_script( $handle );
			}
		}
	},
	100
);

/**
 * JS modülüne veri: WooCommerce Store API adresi, nonce, satış modu, sayfa linkleri.
 * <script type="application/json" id="wp-script-module-data-umiko"> olarak basılır (WP 6.7+).
 */
add_filter(
	'script_module_data_umiko',
	function ( array $data ): array {
		$native = function_exists( 'umiko_is_native' ) && umiko_is_native();
		return array_merge(
			$data,
			array(
				'native'      => $native,
				'storeApi'    => esc_url_raw( rest_url( 'wc/store/v1/' ) ),
				'nonce'       => $native ? wp_create_nonce( 'wc_store_api' ) : '',
				'cartUrl'     => $native ? wc_get_cart_url() : '',
				'checkoutUrl' => $native ? wc_get_checkout_url() : '',
				'shopUrl'     => umiko_shop_url(),
			)
		);
	}
);

add_action(
	'wp_head',
	function () {
		// Türkçe sayfalarda iki font dosyası da hemen lazım (ğ, ş, ı latin-ext içinde).
		foreach ( array( 'latin', 'latin-ext' ) as $subset ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( UMIKO_URI . '/assets/fonts/baloo-2-' . $subset . '-wght-normal.woff2' )
			);
		}
		echo '<meta name="theme-color" content="#FFF8F4">' . "\n";
		// JS varsa .js sınıfı: "gereksiz / gerekli" anahtarı gibi öğeler ilk boyamada doğru görünsün.
		echo '<script>document.documentElement.classList.add("js")</script>' . "\n";

		// Site simgesi (Özelleştir > Site kimliği) seçilmediyse çiçek favicon.
		if ( ! has_site_icon() ) {
			$img = UMIKO_URI . '/assets/img/';
			printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( $img . 'favicon.svg' ) );
			printf( '<link rel="icon" href="%s" sizes="32x32" type="image/png">' . "\n", esc_url( $img . 'favicon-32.png' ) );
			printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $img . 'favicon-180.png' ) );
		}
	},
	1
);

add_filter(
	'body_class',
	function ( array $classes ): array {
		if ( function_exists( 'umiko_is_native' ) ) {
			$classes[] = umiko_is_native() ? 'umiko-native' : 'umiko-external';
		}
		return $classes;
	}
);

// Ürün yokken WooCommerce'in gri yer tutucusu yerine markalı yer tutucu (eklentiler ve bloklar için).
add_filter(
	'woocommerce_placeholder_img',
	function ( $html, $size, $dimensions ) {
		return umiko_placeholder( 'ürün', 'pink-soft' );
	},
	10,
	3
);

/**
 * Menü atanmamışsa kullanılan varsayılan üst menü.
 */
function umiko_default_menu(): array {
	return array(
		array( 'ürünler', umiko_shop_url() ),
		array( 'hikâye', umiko_page_url( 'hikaye' ) ),
		array( 'sss', umiko_page_url( 'sss' ) ),
		array( 'iletişim', umiko_page_url( 'iletisim' ) ),
	);
}

function umiko_nav( string $location, string $class ): void {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 1,
			)
		);
		return;
	}

	$items = 'legal' === $location
		? array_map( fn( $slug, $title ) => array( umiko_lower( $title ), umiko_page_url( $slug ) ), array_keys( umiko_legal_pages() ), umiko_legal_pages() )
		: umiko_default_menu();

	$current = home_url( add_query_arg( array() ) );
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as [ $label, $url ] ) {
		$is_current = untrailingslashit( strtok( $current, '?' ) ) === untrailingslashit( $url );
		printf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( $url ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	echo '</ul>';
}
