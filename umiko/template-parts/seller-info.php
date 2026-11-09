<?php
/**
 * Satıcı bilgileri (6563 sayılı Kanun ve Elektronik Ticarette Hizmet Sağlayıcı ve Aracı Hizmet Sağlayıcılar Hakkında Yönetmelik).
 * Footer'da ve iletişim sayfasında görünür. Değerler: Özelleştir > umiko > satıcı bilgileri.
 */

defined( 'ABSPATH' ) || exit;

$email = trim( (string) get_theme_mod( 'umiko_seller_email', '' ) );
?>
<dl class="seller">
	<div><dt>unvan</dt><dd><?php echo umiko_mod_or_todo( 'umiko_seller_name', 'ad soyad / unvan' ); // phpcs:ignore ?></dd></div>
	<div><dt>adres</dt><dd><?php echo nl2br( umiko_mod_or_todo( 'umiko_seller_address', 'adres' ) ); // phpcs:ignore ?></dd></div>
	<div><dt>e-posta</dt><dd><?php echo $email ? '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>' : umiko_todo( 'e-posta' ); // phpcs:ignore ?></dd></div>
	<div><dt>telefon</dt><dd><?php echo umiko_mod_or_todo( 'umiko_seller_phone', 'telefon' ); // phpcs:ignore ?></dd></div>
	<div><dt>vergi / mersis no</dt><dd><?php echo umiko_mod_or_todo( 'umiko_seller_tax', 'vergi no / MERSİS no' ); // phpcs:ignore ?></dd></div>
</dl>
