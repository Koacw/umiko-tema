<?php
/**
 * Ürün düzenleme ekranında "umiko" sekmesi.
 * Yeni ürün eklerken kod gerekmez: Ürünler > Yeni ekle > Ürün verileri > umiko.
 *
 * Fiyat (KDV dahil), renkler (Renk özniteliği → varyasyonlar), ölçüler (Gönderim sekmesi),
 * görseller ve açıklamalar WooCommerce'in kendi alanlarında kalır.
 */

defined( 'ABSPATH' ) || exit;

function umiko_product_fields(): array {
	return array(
		'shopier_url' => array( 'label' => 'Shopier linki', 'type' => 'url', 'desc' => 'Vitrin modunda "shopier\'den al" butonu buraya gider.' ),
		'etsy_url'    => array( 'label' => 'Etsy linki', 'type' => 'url', 'desc' => 'İsteğe bağlı. Doluysa ikinci buton olarak görünür.' ),
		'material'    => array( 'label' => 'Malzeme', 'type' => 'text', 'desc' => 'ör. PLA, istanbul\'da 3D baskı' ),
		'lead_time'   => array( 'label' => 'Hazırlık süresi', 'type' => 'text', 'desc' => 'ör. stoktan, 1–2 iş günü' ),
		'care'        => array( 'label' => 'Bakım / kullanım notu', 'type' => 'textarea', 'desc' => '' ),
		'confession'  => array( 'label' => 'gereksiz: itiraf', 'type' => 'textarea', 'desc' => 'Ürünün gereksiz görünen tarafı. ör. "kaşıkla da sıyırabilirsin, biliyoruz."' ),
		'reason'      => array( 'label' => 'ama gerekli: çünkü', 'type' => 'textarea', 'desc' => 'Neden yine de gerekli.' ),
		'scene'       => array(
			'label'   => 'Comic çizim',
			'type'    => 'select',
			'desc'    => 'Fotoğraf yokken kartta görünen kullanım çizimi. Boşsa kategoriye göre seçilir.',
			'options' => array(
				''         => 'kategoriye göre',
				'mama'     => 'kedi maması (mutfak + kedi)',
				'cop'      => 'mutfak tezgâhı',
				'kumanda'  => 'kanepe',
				'masa'     => 'çalışma masası',
				'ev'       => 'ev girişi',
				'kedi'     => 'kitap okuyan kedi',
				'atolye'   => '3D yazıcı',
				'yok'      => 'çizim yok (çiçek)',
			),
		),
		'scene_crop'  => array( 'label' => 'Çizim kadrajı', 'type' => 'text', 'desc' => 'İsteğe bağlı, kare: "x y genişlik yükseklik" (sahne 400×400). ör. 236 226 170 170 → masa sahnesinde kulaklık köşesi.' ),
		'badge'       => array(
			'label'   => 'Rozet',
			'type'    => 'select',
			'desc'    => '',
			'options' => array(
				''              => 'yok',
				'yeni'          => 'yeni',
				'siparise-ozel' => 'siparişe özel, 3–5 günde kargoda',
			),
		),
	);
}

add_filter(
	'woocommerce_product_data_tabs',
	function ( array $tabs ): array {
		$tabs['umiko'] = array(
			'label'    => 'umiko',
			'target'   => 'umiko_product_data',
			'priority' => 15,
		);
		return $tabs;
	}
);

add_action(
	'woocommerce_product_data_panels',
	function () {
		global $post;
		echo '<div id="umiko_product_data" class="panel woocommerce_options_panel hidden"><div class="options_group">';
		foreach ( umiko_product_fields() as $key => $field ) {
			$args = array(
				'id'          => '_umiko_' . $key,
				'label'       => $field['label'],
				'description' => $field['desc'],
				'desc_tip'    => false,
				'value'       => get_post_meta( $post->ID, '_umiko_' . $key, true ),
			);
			switch ( $field['type'] ) {
				case 'textarea':
					woocommerce_wp_textarea_input( $args );
					break;
				case 'select':
					woocommerce_wp_select( $args + array( 'options' => $field['options'] ) );
					break;
				default:
					woocommerce_wp_text_input( $args + array( 'type' => $field['type'] ) );
			}
		}
		echo '</div></div>';
	}
);

add_action(
	'woocommerce_admin_process_product_object',
	function ( WC_Product $product ) {
		foreach ( umiko_product_fields() as $key => $field ) {
			$name = '_umiko_' . $key;
			if ( ! isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- WooCommerce nonce'u doğruladı.
				continue;
			}
			$raw   = wp_unslash( $_POST[ $name ] ); // phpcs:ignore
			$value = match ( $field['type'] ) {
				'url'      => esc_url_raw( trim( $raw ) ),
				'textarea' => sanitize_textarea_field( $raw ),
				'select'   => array_key_exists( $raw, $field['options'] ) ? $raw : '',
				default    => sanitize_text_field( $raw ),
			};
			$product->update_meta_data( $name, $value );
		}
	}
);
