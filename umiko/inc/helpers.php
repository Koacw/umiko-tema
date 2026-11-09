<?php
/**
 * Küçük yardımcılar: çiçek, ikonlar, [DOLDUR] işaretleri, ürün görselleri, renkler.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Henüz belli olmayan bilgi için görünür yer tutucu.
 * Sitede pembe zeminli küçük bir etiket olarak görünür; yayından önce hepsi doldurulmalı.
 */
function umiko_todo( string $label ): string {
	return '<mark class="todo">[DOLDUR: ' . esc_html( $label ) . ']</mark>';
}

/**
 * Özelleştir'deki bir ayarı döndürür; boşsa görünür [DOLDUR] işareti.
 */
function umiko_mod_or_todo( string $key, string $label ): string {
	$value = trim( (string) get_theme_mod( $key, '' ) );
	return '' === $value ? umiko_todo( $label ) : esc_html( $value );
}

/**
 * Türkçe doğru küçük harf (İ → i, I → ı). mb_strtolower bunu tek başına doğru yapmaz.
 */
function umiko_lower( string $text ): string {
	return mb_strtolower( str_replace( array( 'I', 'İ' ), array( 'ı', 'i' ), $text ), 'UTF-8' );
}

/**
 * Logodaki dört yapraklı çiçek. Yaprak rengi currentColor, göbek rengi --center.
 * Comic dış hat: arkadaki .flower__line grubu (renk --line, varsayılan ink; kapatmak için --line: transparent).
 */
function umiko_flower( string $class = '', array $attrs = array() ): string {
	$extra = '';
	foreach ( $attrs as $name => $value ) {
		$extra .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
	}
	return sprintf(
		'<svg class="flower %1$s" viewBox="-5 -5 110 110" aria-hidden="true" focusable="false"%2$s><g class="flower__line"><circle cx="29" cy="29" r="29"/><circle cx="71" cy="29" r="29"/><circle cx="29" cy="71" r="29"/><circle cx="71" cy="71" r="29"/></g><g fill="currentColor"><circle cx="29" cy="29" r="29"/><circle cx="71" cy="29" r="29"/><circle cx="29" cy="71" r="29"/><circle cx="71" cy="71" r="29"/><circle cx="50" cy="50" r="30"/></g><circle class="flower__center" cx="50" cy="50" r="15.3"/></svg>',
		esc_attr( $class ),
		$extra
	);
}

/**
 * İnce çizgili, yuvarlak uçlu ikonlar.
 */
function umiko_icon( string $name, string $class = '' ): string {
	$paths = array(
		'bag'       => '<path d="M5 8h14l-1.2 11.1a2 2 0 0 1-2 1.9H8.2a2 2 0 0 1-2-1.9L5 8Z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h10"/>',
		'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'minus'     => '<path d="M5 12h14"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'external'  => '<path d="M14 5h5v5M19 5l-8 8"/><path d="M18 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4"/>',
		'instagram' => '<rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.6"/><path d="M16.8 7.2h.01"/>',
		'mail'      => '<rect x="3.5" y="5.5" width="17" height="13" rx="3"/><path d="m4.5 7.5 7.5 5.5 7.5-5.5"/>',
		'trash'     => '<path d="M5 7h14M10 11v6M14 11v6M6.5 7l.8 11.2A2 2 0 0 0 9.3 20h5.4a2 2 0 0 0 2-1.8L17.5 7M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/>',
		'clock'     => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'drop'      => '<path d="M12 3.5s6 6.4 6 10.5a6 6 0 0 1-12 0c0-4.1 6-10.5 6-10.5Z"/>',
		'ruler'     => '<rect x="3" y="8" width="18" height="8" rx="2"/><path d="M7 8v3M11 8v4M15 8v3M19 8v2"/>',
		'layers'    => '<path d="m12 4 8 4-8 4-8-4 8-4Z"/><path d="m4 12 8 4 8-4"/><path d="m4 16 8 4 8-4"/>',
		'heart'     => '<path d="M12 19.5s-7.5-4.4-7.5-9.7A4.3 4.3 0 0 1 12 7.3a4.3 4.3 0 0 1 7.5 2.5c0 5.3-7.5 9.7-7.5 9.7Z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon %s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $class ),
		$paths[ $name ]
	);
}

/**
 * Renk adından (terim slug'ı) CSS renk değeri. Yeni renk eklemek için `umiko_color_swatches` filtresi.
 */
function umiko_color_value( string $slug ): string {
	$map = apply_filters(
		'umiko_color_swatches',
		array(
			'pembe'    => 'var(--u-pink)',
			'mavi'     => 'var(--u-blue)',
			'krem'     => 'var(--u-cream)',
			'lacivert' => 'var(--u-ink)',
		)
	);
	return $map[ $slug ] ?? 'var(--u-pink-soft)';
}

/**
 * Kart ve yer tutucu zemini. Ürün sırasına (menu_order) göre dönüşümlü; yan yana kartlar farklı renk alır.
 */
function umiko_tint( int $id ): string {
	$tints = array( 'blue', 'cream', 'pink' );
	$order = (int) get_post_field( 'menu_order', $id );
	return $tints[ ( $order ?: $id ) % count( $tints ) ];
}

/**
 * Fotoğraf gelmeyen ürünler için marka renklerinde, açıkça "fotoğraf gelecek" diyen yer tutucu.
 */
function umiko_placeholder( string $label, string $tint = 'pink-soft', string $note = 'fotoğraf gelecek' ): string {
	return sprintf(
		'<div class="ph ph--%1$s" role="img" aria-label="%2$s">%3$s<span class="ph__note">%4$s</span></div>',
		esc_attr( $tint ),
		esc_attr( $label . ' (' . $note . ')' ),
		umiko_flower( 'ph__flower' ),
		esc_html( $note )
	);
}

/**
 * Comic çizimler: assets/illustrations/{ad}.svg. Satır içi basılır (renkler CSS token'larından gelir).
 * Her sahnenin tasarlandığı zemin rengi burada.
 */
function umiko_scenes(): array {
	return apply_filters(
		'umiko_scenes',
		array(
			'mama'     => array( 'label' => 'kedi maması sahnesi', 'bg' => 'pink-soft' ),
			'cop'      => array( 'label' => 'mutfak sahnesi', 'bg' => 'blue' ),
			'kumanda'  => array( 'label' => 'kanepe sahnesi', 'bg' => 'pink' ),
			'masa'     => array( 'label' => 'çalışma masası sahnesi', 'bg' => 'cream' ),
			'ev'       => array( 'label' => 'ev girişi sahnesi', 'bg' => 'blue' ),
			'kedi'     => array( 'label' => 'kitap okuyan kedi', 'bg' => 'pink-soft' ),
			'atolye'   => array( 'label' => 'atölyedeki 3D yazıcı', 'bg' => 'cream' ),
			'kedi-yuz' => array( 'label' => 'kalp gözlü kedi', 'bg' => 'blue' ),
		)
	);
}

/**
 * Bir çizimi satır içi SVG olarak döndürür.
 *
 * @param string $crop "x y genişlik yükseklik": sahnenin bir bölümüne yakınlaşmak için viewBox.
 * @param string $label Boşsa çizim dekoratif sayılır (aria-hidden).
 */
function umiko_illustration( string $name, string $label = '', string $crop = '', string $class = '' ): string {
	static $cache = array();
	if ( ! isset( umiko_scenes()[ $name ] ) ) {
		return '';
	}
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = UMIKO_DIR . '/assets/illustrations/' . $name . '.svg';
		$cache[ $name ] = is_readable( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	$svg = $cache[ $name ];
	if ( '' === $svg ) {
		return '';
	}

	$attrs = $label
		? sprintf( ' role="img" aria-label="%s"', esc_attr( $label ) )
		: ' aria-hidden="true" focusable="false"';
	$attrs .= ' preserveAspectRatio="xMidYMid slice"';
	$svg    = preg_replace( '/<svg\b([^>]*)class="ill"/', '<svg$1class="ill ' . esc_attr( $class ) . '"' . $attrs, $svg, 1 );

	if ( preg_match( '/^-?\d+(\.\d+)?( -?\d+(\.\d+)?){3}$/', trim( $crop ) ) ) {
		$svg = preg_replace( '/viewBox="[^"]*"/', 'viewBox="' . esc_attr( trim( $crop ) ) . '"', $svg, 1 );
	}
	return $svg;
}

/**
 * Ürünün çizimi: ürün düzenle > umiko > çizim. Boşsa kategoriye göre sahne.
 *
 * @return array{name:string, crop:string}|null
 */
function umiko_product_scene( WC_Product $product ): ?array {
	$name = umiko_meta( $product, 'scene' );
	if ( 'yok' === $name ) {
		return null;
	}
	if ( '' === $name ) {
		$by_cat = array( 'kedi' => 'kedi', 'mutfak' => 'cop', 'masa' => 'masa', 'ev' => 'ev' );
		foreach ( $product->get_category_ids() as $cat_id ) {
			$term = get_term( $cat_id, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) && isset( $by_cat[ $term->slug ] ) ) {
				$name = $by_cat[ $term->slug ];
				break;
			}
		}
	}
	if ( '' === $name || ! isset( umiko_scenes()[ $name ] ) ) {
		return null;
	}
	return array( 'name' => $name, 'crop' => umiko_meta( $product, 'scene_crop' ) );
}

/**
 * Fotoğraf yokken: ürünün comic çizimi + "fotoğraf gelecek" etiketi. Çizim de yoksa çiçekli yer tutucu.
 */
function umiko_scene_placeholder( WC_Product $product, bool $full = false ): string {
	$scene = umiko_product_scene( $product );
	if ( ! $scene ) {
		return umiko_placeholder( $product->get_name(), umiko_tint( $product->get_id() ) );
	}
	$bg = umiko_scenes()[ $scene['name'] ]['bg'];
	return sprintf(
		'<div class="ph ph--scene scene scene--%1$s" role="img" aria-label="%2$s">%3$s<span class="ph__note">%4$s</span></div>',
		esc_attr( $bg ),
		esc_attr( $product->get_name() . ': kullanım çizimi (ürün fotoğrafı gelecek)' ),
		umiko_illustration( $scene['name'], '', $full ? '' : $scene['crop'] ),
		'çizim · fotoğraf gelecek'
	);
}

/**
 * Ürün görseli varsa görsel, yoksa çizim / yer tutucu.
 */
function umiko_product_media( WC_Product $product, string $size = 'woocommerce_thumbnail', string $loading = 'lazy' ): string {
	$image_id = $product->get_image_id();
	if ( $image_id ) {
		return wp_get_attachment_image(
			$image_id,
			$size,
			false,
			array(
				'alt'     => trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ?: $product->get_name(),
				'loading' => $loading,
			)
		);
	}
	return umiko_scene_placeholder( $product );
}

/**
 * Ürünün renk seçenekleri: [ [ 'name' => 'pembe', 'slug' => 'pembe', 'variation_id' => 12, 'in_stock' => true ], … ]
 * Varyasyonlu ürünlerde her renk bir varyasyondur; basit ürünlerde yalnızca gösterim içindir.
 */
function umiko_product_colors( WC_Product $product ): array {
	$colors = array();

	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( $child_id );
			if ( ! $variation || 'publish' !== $variation->get_status() ) {
				continue;
			}
			foreach ( $variation->get_attributes() as $taxonomy => $slug ) {
				if ( ! in_array( $taxonomy, array( 'pa_renk', 'renk' ), true ) || '' === $slug ) {
					continue;
				}
				$term     = taxonomy_exists( $taxonomy ) ? get_term_by( 'slug', $slug, $taxonomy ) : null;
				$colors[] = array(
					'name'         => $term ? $term->name : $slug,
					'slug'         => $slug,
					'variation_id' => $variation->get_id(),
					'in_stock'     => $variation->is_in_stock(),
				);
			}
		}
		return $colors;
	}

	$terms = wc_get_product_terms( $product->get_id(), 'pa_renk' );
	foreach ( $terms as $term ) {
		$colors[] = array(
			'name'         => $term->name,
			'slug'         => $term->slug,
			'variation_id' => 0,
			'in_stock'     => $product->is_in_stock(),
		);
	}
	return $colors;
}

/**
 * Ürüne ait umiko alanı (bkz. inc/product-meta.php).
 */
function umiko_meta( WC_Product $product, string $key ): string {
	return trim( (string) $product->get_meta( '_umiko_' . $key ) );
}

/**
 * Rozet metni. $long true ise ürün sayfasındaki uzun hali.
 */
function umiko_badge( WC_Product $product, bool $long = false ): string {
	$badge = umiko_meta( $product, 'badge' );
	if ( 'yeni' === $badge ) {
		return 'yeni';
	}
	if ( 'siparise-ozel' === $badge ) {
		return $long ? 'siparişe özel, 3–5 günde kargoda' : 'siparişe özel';
	}
	return '';
}

/**
 * Slug'a göre sayfa linki; sayfa yoksa ana sayfa.
 */
function umiko_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function umiko_shop_url(): string {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

function umiko_instagram_url(): string {
	$user = ltrim( trim( (string) get_theme_mod( 'umiko_instagram', '' ) ), '@' );
	return '' === $user ? '' : 'https://www.instagram.com/' . rawurlencode( $user ) . '/';
}

/**
 * Yasal sayfalar: slug => başlık. Footer ve sayfa oluşturucu bu listeyi kullanır.
 */
function umiko_legal_pages(): array {
	return array(
		'mesafeli-satis-sozlesmesi' => 'Mesafeli Satış Sözleşmesi',
		'on-bilgilendirme-formu'    => 'Ön Bilgilendirme Formu',
		'iade-ve-cayma-hakki'       => 'İade ve Cayma Hakkı',
		'kvkk-aydinlatma-metni'     => 'KVKK Aydınlatma Metni',
		'cerez-politikasi'          => 'Çerez Politikası',
		'kargo-ve-teslimat'         => 'Kargo ve Teslimat',
	);
}
