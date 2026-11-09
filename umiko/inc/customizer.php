<?php
/**
 * Görünüm > Özelleştir > umiko
 * Boş bırakılan alanlar sitede görünür [DOLDUR] işareti olarak çıkar.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_panel( 'umiko', array( 'title' => 'umiko', 'priority' => 30 ) );

		$sections = array(
			'umiko_sales'   => 'satış modu',
			'umiko_brand'   => 'hikâye ve sosyal',
			'umiko_club'    => 'umiko kulübü (e-posta)',
			'umiko_seller'  => 'satıcı bilgileri (yasal)',
		);
		foreach ( $sections as $id => $title ) {
			$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'umiko' ) );
		}

		$fields = array(
			array( 'umiko_checkout_mode', 'umiko_sales', 'satın alma', 'radio', 'native', array(
				'native'   => 'WooCommerce sepeti ("sepete at", ödeme sitede)',
				'external' => 'Vitrin: Shopier / Etsy butonları (sepet kapalı)',
			), 'Ürün kartları ve sayfaları iki modda da aynı; sadece butonlar değişir.' ),

			array( 'umiko_instagram', 'umiko_brand', 'Instagram kullanıcı adı', 'text', '', null, '@ olmadan, ör. umiko.shop' ),
			array( 'umiko_founder_story', 'umiko_brand', 'Kurucunun kısa hikâyesi', 'textarea', '', null, 'Ana sayfadaki atölye bölümünde ve hikâye sayfasında görünür. Paragraflar için boş satır bırak.' ),

			array( 'umiko_newsletter_url', 'umiko_club', 'Form gönderim adresi (Brevo, Mailchimp vb.)', 'url', '', null, 'Boşken e-posta formu hiç görünmez; sadece Instagram butonu kalır.' ),
			array( 'umiko_newsletter_field', 'umiko_club', 'E-posta alanının adı', 'text', 'EMAIL', null, 'Mailchimp: EMAIL, Brevo: EMAIL. Sağlayıcının formundaki input name değeri.' ),

			array( 'umiko_seller_name', 'umiko_seller', 'Ad soyad / unvan', 'text', '', null, '' ),
			array( 'umiko_seller_address', 'umiko_seller', 'Adres', 'textarea', '', null, '' ),
			array( 'umiko_seller_email', 'umiko_seller', 'E-posta', 'email', '', null, '' ),
			array( 'umiko_seller_phone', 'umiko_seller', 'Telefon', 'text', '', null, '' ),
			array( 'umiko_seller_tax', 'umiko_seller', 'Vergi no / MERSİS no', 'text', '', null, '' ),
			array( 'umiko_etbis', 'umiko_seller', 'ETBİS doğrulama kodu (HTML)', 'textarea', '', null, 'ETBİS kaydından sonra verilen kodu buraya yapıştır; footer\'da görünür.' ),
		);

		foreach ( $fields as [ $id, $section, $label, $type, $default, $choices, $desc ] ) {
			$sanitize = match ( $type ) {
				'url'      => 'esc_url_raw',
				'email'    => 'sanitize_email',
				'textarea' => 'umiko_etbis' === $id ? 'wp_kses_post' : 'sanitize_textarea_field',
				'radio'    => fn( $v ) => array_key_exists( $v, $choices ) ? $v : $default,
				default    => 'sanitize_text_field',
			};
			$wp_customize->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => $sanitize ) );
			$wp_customize->add_control(
				$id,
				array_filter(
					array(
						'label'       => $label,
						'section'     => $section,
						'type'        => $type,
						'choices'     => $choices,
						'description' => $desc,
					)
				)
			);
		}
	}
);
