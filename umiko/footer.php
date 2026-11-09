<?php
/**
 * Footer: yasal linkler, satıcı bilgileri, Instagram, imza, ETBİS alanı.
 */

defined( 'ABSPATH' ) || exit;

$instagram = umiko_instagram_url();
$etbis     = trim( (string) get_theme_mod( 'umiko_etbis', '' ) );
?>
</main>

<footer class="site-footer">
	<?php echo umiko_flower( 'site-footer__flower' ); // phpcs:ignore ?>
	<div class="wrap site-footer__grid">
		<div class="site-footer__brand">
			<img src="<?php echo esc_url( UMIKO_URI . '/assets/brand/umiko-logo-mavi-zemin.png' ); ?>" width="1314" height="390" alt="umiko" loading="lazy">
			<p class="site-footer__slogan">gereksiz ama gerekli.</p>
			<?php if ( $instagram ) : ?>
				<a class="btn btn--cream btn--sm" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener me">
					<?php echo umiko_icon( 'instagram' ); // phpcs:ignore ?> instagram'da umiko<span class="sr-only"> (yeni sekmede açılır)</span>
				</a>
			<?php else : ?>
				<p><?php echo umiko_todo( 'instagram kullanıcı adı' ); // phpcs:ignore ?></p>
			<?php endif; ?>
		</div>

		<nav class="site-footer__col" aria-label="umiko">
			<h2 class="site-footer__title">umiko</h2>
			<?php umiko_nav( 'primary', 'site-footer__list' ); ?>
		</nav>

		<nav class="site-footer__col" aria-label="yasal">
			<h2 class="site-footer__title">yasal</h2>
			<?php umiko_nav( 'legal', 'site-footer__list' ); ?>
		</nav>

		<div class="site-footer__col">
			<h2 class="site-footer__title">satıcı bilgileri</h2>
			<?php get_template_part( 'template-parts/seller-info', null, array( 'compact' => true ) ); ?>
		</div>
	</div>

	<div class="wrap site-footer__bottom">
		<p>umiko · istanbul'da küçük bir atölye</p>
		<div class="site-footer__etbis" data-slot="etbis">
			<?php
			// ETBİS kaydından sonra Özelleştir > umiko > satıcı bilgileri > ETBİS kodu alanına yapıştırılır.
			echo $etbis ? wp_kses_post( $etbis ) : ''; // phpcs:ignore
			?>
		</div>
		<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> umiko</p>
	</div>
</footer>

<?php
if ( function_exists( 'umiko_is_native' ) && umiko_is_native() ) {
	get_template_part( 'template-parts/cart-drawer' );
}
get_template_part( 'template-parts/cookie-banner' );
?>
<div class="sr-only" aria-live="polite" data-announcer></div>
<?php wp_footer(); ?>
</body>
</html>
