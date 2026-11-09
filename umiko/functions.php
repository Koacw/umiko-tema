<?php
/**
 * umiko tema dosyaları.
 *
 * inc/checkout-mode.php  satın alma davranışını tek yerden yönetir (native | external)
 * inc/product-meta.php   ürün düzenleme ekranındaki "umiko" sekmesi (shopier, etsy, bakım…)
 * inc/customizer.php     Görünüm > Özelleştir > umiko ayarları
 * inc/demo-import.php    Ürünler > umiko örnek ürünler (tek tıkla demo katalog)
 */

defined( 'ABSPATH' ) || exit;

define( 'UMIKO_VERSION', '1.0.0' );
define( 'UMIKO_DIR', get_template_directory() );
define( 'UMIKO_URI', get_template_directory_uri() );

require UMIKO_DIR . '/inc/helpers.php';
require UMIKO_DIR . '/inc/setup.php';
require UMIKO_DIR . '/inc/customizer.php';
require UMIKO_DIR . '/inc/pages.php';
require UMIKO_DIR . '/inc/seo.php';

if ( class_exists( 'WooCommerce' ) ) {
	require UMIKO_DIR . '/inc/checkout-mode.php';
	require UMIKO_DIR . '/inc/product-meta.php';
	require UMIKO_DIR . '/inc/demo-import.php';
}
