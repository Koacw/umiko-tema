<?php
/**
 * 404: bu sayfa gereksizmiş, sildik. Çiçeklere tıklayınca dönüp zıplıyorlar.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="wrap lost">
	<div class="lost__garden" data-garden>
		<?php
		$petals = array( 'blue', 'pink', 'blue', 'cream', 'pink', 'blue', 'pink' );
		foreach ( $petals as $i => $tint ) {
			printf(
				'<button type="button" class="lost__flower lost__flower--%1$s" style="--i:%2$d" data-garden-flower aria-label="çiçeğe dokun">%3$s</button>',
				esc_attr( $tint ),
				(int) $i,
				umiko_flower() // phpcs:ignore
			);
		}
		?>
	</div>
	<p class="lost__code" aria-hidden="true">4<?php echo umiko_flower( 'lost__zero spin' ); // phpcs:ignore ?>4</p>
	<h1 class="lost__title">bu sayfa gereksizmiş, sildik.</h1>
	<p class="lost__lead">ana sayfaya dönelim mi?</p>
	<div class="lost__actions">
		<a class="btn btn--blue btn--lg bounce" href="<?php echo esc_url( home_url( '/' ) ); ?>">ana sayfaya dön</a>
		<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( umiko_shop_url() ); ?>">ürünlere bak</a>
	</div>
	<p class="lost__count" data-garden-count aria-live="polite" hidden></p>
</div>
<?php
get_footer();
