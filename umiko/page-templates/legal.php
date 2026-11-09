<?php
/**
 * Template Name: Yasal sayfa
 *
 * Mesafeli Satış Sözleşmesi, Ön Bilgilendirme Formu, İade ve Cayma Hakkı, KVKK, Çerez Politikası, Kargo ve Teslimat.
 * Metinler tema tarafından yazılmaz; bir hukukçu ya da mali müşavir hazırlayıp sayfa editörüne yapıştırır.
 * Yasal metinler normal büyük–küçük harf kuralına uyar.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$has_content = '' !== trim( wp_strip_all_tags( get_the_content() ) );
	?>
	<article <?php post_class( 'wrap page page--narrow legal' ); ?>>
		<header class="page__head">
			<h1 class="page__title page__title--legal"><?php the_title(); ?></h1>
			<p class="legal__updated">Son güncelleme: <?php echo $has_content ? esc_html( get_the_modified_date( 'j F Y' ) ) : umiko_todo( 'tarih' ); // phpcs:ignore ?></p>
		</header>

		<?php if ( ! $has_content ) : ?>
			<div class="legal-todo" role="note">
				<?php echo umiko_flower( 'legal-todo__flower' ); // phpcs:ignore ?>
				<p class="legal-todo__title">[DOLDUR] Bu metin henüz hazırlanmadı.</p>
				<p>Bu sayfanın metni bir hukukçu ya da mali müşavir tarafından hazırlanacak. Metin hazır olduğunda WordPress panelinde bu sayfayı düzenleyip içeriğe yapıştırman yeterli; bu kutu kendiliğinden kaybolur.</p>
			</div>
		<?php endif; ?>

		<div class="page__content prose prose--legal">
			<?php the_content(); ?>
		</div>

		<section class="legal-box" aria-labelledby="legal-seller-title">
			<h2 id="legal-seller-title">Satıcı bilgileri</h2>
			<?php get_template_part( 'template-parts/seller-info' ); ?>
		</section>
	</article>
	<?php
endwhile;

get_footer();
