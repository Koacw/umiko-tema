<?php
/**
 * SSS (slug: sss). Cevaplar henüz belli değil: [DOLDUR].
 * Cevaplar netleşince aşağıdaki $faq dizisini güncelle ya da sayfa editörüne yaz (editör içeriği altta görünür).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$faq = array(
	array( 'kargo ne kadar sürüyor?', '' ),             // [DOLDUR: kargo firması ve süresi]
	array( 'hazırlık süresi ne demek?', '' ),           // [DOLDUR]
	array( 'iade edebilir miyim?', '' ),                // [DOLDUR: 14 gün cayma hakkı ayrıntıları]
	array( 'ürünlere nasıl bakmalıyım?', '' ),          // [DOLDUR]
	array( 'siparişe özel üretim nasıl işliyor?', '' ), // [DOLDUR]
);
?>
<div class="wrap page page--narrow">
	<header class="page__head">
		<?php echo umiko_flower( 'page__flower spin' ); // phpcs:ignore ?>
		<h1 class="page__title">sık sorulanlar</h1>
		<p class="page__lead">aklına takılan bir şey varsa büyük ihtimalle burada. yoksa <a href="<?php echo esc_url( umiko_page_url( 'iletisim' ) ); ?>">bize yaz</a>.</p>
	</header>

	<div class="faq">
		<?php foreach ( $faq as $i => [ $q, $a ] ) : ?>
			<details class="faq__item reveal" name="faq" <?php echo 0 === $i ? 'open' : ''; ?>>
				<summary><?php echo esc_html( $q ); ?><span class="faq__icon" aria-hidden="true"><?php echo umiko_icon( 'plus' ); // phpcs:ignore ?></span></summary>
				<div class="faq__answer"><p><?php echo $a ? esc_html( $a ) : umiko_todo( 'cevap' ); // phpcs:ignore ?></p></div>
			</details>
		<?php endforeach; ?>
	</div>

	<div class="page__content prose">
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
</div>
<?php
get_footer();
