<?php
/**
 * WooCommerce döngüsündeki ürün (kısa kodlar, bloklar, eklentiler) için umiko kartı.
 *
 * @package umiko
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
