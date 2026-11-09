<?php
/**
 * Sayfa açıklamaları, Open Graph ve yapılandırılmış veri.
 * Yoast / Rank Math / SEOPress kuruluysa açıklama ve OG etiketlerini onlara bırakır.
 *
 * Ürün şeması (Product, fiyat TRY) WooCommerce'in kendi yapılandırılmış verisiyle basılır;
 * tema bunu woocommerce/single-product.php içinde tetikler.
 * sitemap.xml ve robots.txt WordPress çekirdeğinden gelir (/wp-sitemap.xml).
 */

defined( 'ABSPATH' ) || exit;

function umiko_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

function umiko_meta_description(): string {
	$tagline = 'gereksiz ama gerekli tatlı şeyler. masana, mutfağına, kedine küçük tatlılıklar; istanbul\'da, katman katman basıyoruz.';

	if ( is_front_page() ) {
		return $tagline;
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$text = wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() );
			return $text ?: $product->get_name() . ' · umiko';
		}
	}
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return 'umiko\'nun bütün ürünleri: istanbul\'daki atölyede tek tek basılan, gereksiz ama gerekli ev ve masa objeleri.';
	}
	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		return wp_strip_all_tags( term_description( $term ) ) ?: $term->name . ' için gereksiz ama gerekli şeyler · umiko';
	}
	if ( is_404() ) {
		return 'bu sayfa gereksizmiş, sildik.';
	}
	if ( is_page() ) {
		$by_slug = array(
			'hikaye'   => 'umiko\'nun hikâyesi: istanbul\'da küçük bir atölye, bir 3D yazıcı ve gereksiz ama gerekli fikirler.',
			'sss'      => 'kargo, hazırlık süresi, iade, bakım ve siparişe özel üretim hakkında sık sorulanlar.',
			'iletisim' => 'umiko\'ya yaz: e-posta, Instagram ve satıcı bilgileri.',
		);
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( $by_slug[ $slug ] ) ) {
			return $by_slug[ $slug ];
		}
		$excerpt = wp_strip_all_tags( get_the_excerpt( get_queried_object_id() ) );
		return $excerpt ?: get_the_title( get_queried_object_id() ) . ' · umiko';
	}
	return $tagline;
}

add_action(
	'wp_head',
	function () {
		if ( umiko_seo_plugin_active() ) {
			return;
		}
		$desc  = wp_html_excerpt( umiko_meta_description(), 160, '…' );
		$title = wp_get_document_title();
		$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
		$image = UMIKO_URI . '/assets/img/og-default.jpg';
		$type  = 'website';

		if ( function_exists( 'is_product' ) && is_product() ) {
			$product = wc_get_product( get_queried_object_id() );
			$type    = 'product';
			if ( $product && $product->get_image_id() ) {
				$image = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
			}
		}

		$tags = array(
			'description'         => $desc,
			'og:site_name'        => 'umiko',
			'og:locale'           => 'tr_TR',
			'og:type'             => $type,
			'og:title'            => $title,
			'og:description'      => $desc,
			'og:url'              => $url,
			'og:image'            => $image,
			'twitter:card'        => 'summary_large_image',
		);
		foreach ( $tags as $key => $value ) {
			$attr = str_starts_with( $key, 'og:' ) ? 'property' : 'name';
			printf( '<meta %s="%s" content="%s">' . "\n", $attr, esc_attr( $key ), esc_attr( $value ) );
		}
		if ( 'website' === $type && str_ends_with( $image, 'og-default.jpg' ) ) {
			echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
		}
	},
	5
);

/*
 * Ana sayfada kurum + site şeması.
 */
add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() || umiko_seo_plugin_active() ) {
			return;
		}
		$same_as = array_filter( array( umiko_instagram_url() ) );
		$data    = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type'  => 'Organization',
					'@id'    => home_url( '/#org' ),
					'name'   => 'umiko',
					'url'    => home_url( '/' ),
					'logo'   => UMIKO_URI . '/assets/brand/umiko-logo.png',
					'slogan' => 'gereksiz ama gerekli.',
					'sameAs' => array_values( $same_as ),
				),
				array(
					'@type'      => 'WebSite',
					'name'       => 'umiko',
					'url'        => home_url( '/' ),
					'inLanguage' => 'tr-TR',
					'publisher'  => array( '@id' => home_url( '/#org' ) ),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
);
