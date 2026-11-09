<?php
/**
 * umiko kulübü: Instagram + (adres girildiyse) e-posta formu.
 * Form adresi boşken form hiç görünmez. Ayar: Özelleştir > umiko > umiko kulübü.
 */

defined( 'ABSPATH' ) || exit;

$instagram = umiko_instagram_url();
$form_url  = trim( (string) get_theme_mod( 'umiko_newsletter_url', '' ) );
$field     = trim( (string) get_theme_mod( 'umiko_newsletter_field', 'EMAIL' ) ) ?: 'EMAIL';
?>
<section class="club reveal" aria-labelledby="club-title">
	<?php echo umiko_flower( 'club__flower club__flower--a spin' ); // phpcs:ignore ?>
	<?php echo umiko_flower( 'club__flower club__flower--b' ); // phpcs:ignore ?>
	<div class="club__inner">
		<h2 id="club-title" class="club__title">umiko kulübüne katıl: yeni ürünleri herkesten önce gör.</h2>

		<?php if ( $form_url ) : ?>
			<form class="club__form" action="<?php echo esc_url( $form_url ); ?>" method="post" target="_blank">
				<label class="sr-only" for="club-email">e-posta adresin</label>
				<input id="club-email" type="email" name="<?php echo esc_attr( $field ); ?>" placeholder="e-posta adresin" autocomplete="email" required>
				<button class="btn btn--blue" type="submit">katıl</button>
			</form>
			<p class="club__fine">istediğin an çıkabilirsin. ayrıntılar <a href="<?php echo esc_url( umiko_page_url( 'kvkk-aydinlatma-metni' ) ); ?>">KVKK aydınlatma metni</a>nde.</p>
		<?php endif; ?>

		<?php if ( $instagram ) : ?>
			<a class="btn <?php echo $form_url ? 'btn--ghost' : 'btn--blue btn--lg'; ?>" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener me">
				<?php echo umiko_icon( 'instagram' ); // phpcs:ignore ?> instagram'da takip et<span class="sr-only"> (yeni sekmede açılır)</span>
			</a>
		<?php else : ?>
			<p><?php echo umiko_todo( 'instagram kullanıcı adı: Özelleştir > umiko' ); // phpcs:ignore ?></p>
		<?php endif; ?>
	</div>
</section>
