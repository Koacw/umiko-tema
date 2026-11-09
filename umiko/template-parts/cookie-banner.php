<?php
/**
 * Çerez bildirimi. Faz 1'de sitede yalnızca zorunlu çerezler (sepet / oturum) var, bu yüzden bant bilgilendirme amaçlı.
 * İleride analitik eklenirse: "kabul et / reddet" butonları eklenip takip kodu
 * window.umikoConsent.onGrant(() => { ... }) içinde yüklenmeli (bkz. assets/js/umiko.js).
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cookie" data-cookie hidden role="region" aria-label="çerez bildirimi">
	<?php echo umiko_flower( 'cookie__flower' ); // phpcs:ignore ?>
	<p>bu sitede sadece sitenin çalışması için gereken çerezler var. ayrıntılar <a href="<?php echo esc_url( umiko_page_url( 'cerez-politikasi' ) ); ?>">çerez politikası</a>nda.</p>
	<button type="button" class="btn btn--blue btn--sm" data-cookie-ok>tamam</button>
</div>
