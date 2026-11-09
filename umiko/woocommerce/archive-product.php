<?php
/**
 * Mağaza ve kategori sayfaları: başlık, kategori çipleri, sıralama, ürün ızgarası.
 *
 * @package umiko
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$current = is_product_category() ? get_queried_object() : null;
$title   = is_shop() ? 'ürünler' : umiko_lower( woocommerce_page_title( false ) );
$cats    = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	)
);
?>
<div class="wrap shop">
	<header class="shop__head">
		<?php echo umiko_flower( 'shop__flower spin' ); // phpcs:ignore ?>
		<h1 class="shop__title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( is_shop() ) : ?>
			<p class="shop__lead">hepsi gereksiz. hepsi gerekli. hepsi istanbul'da, tek tek basıldı.</p>
		<?php elseif ( $current && $current->description ) : ?>
			<div class="shop__lead"><?php echo wp_kses_post( wpautop( $current->description ) ); ?></div>
		<?php endif; ?>
	</header>

	<div class="shop__bar">
		<?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
			<nav class="chips" aria-label="kategoriler">
				<a class="chip" href="<?php echo esc_url( umiko_shop_url() ); ?>" <?php echo is_shop() ? 'aria-current="page"' : ''; ?>>hepsi</a>
				<?php foreach ( $cats as $cat ) : ?>
					<a class="chip" href="<?php echo esc_url( get_term_link( $cat ) ); ?>" <?php echo $current && $current->term_id === $cat->term_id ? 'aria-current="page"' : ''; ?>><?php echo esc_html( umiko_lower( $cat->name ) ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<?php if ( woocommerce_product_loop() ) : ?>
			<div class="shop__sort"><?php woocommerce_catalog_ordering(); ?></div>
		<?php endif; ?>
	</div>

	<?php woocommerce_output_all_notices(); ?>

	<?php if ( woocommerce_product_loop() ) : ?>
		<ul class="product-grid">
			<?php
			$i = 0;
			while ( have_posts() ) {
				the_post();
				global $product;
				if ( $product && $product->is_visible() ) {
					get_template_part( 'template-parts/product-card', null, array( 'product' => $product, 'eager' => $i++ < 2 ) );
				}
			}
			?>
		</ul>
		<?php woocommerce_pagination(); ?>
	<?php else : ?>
		<div class="empty">
			<?php echo umiko_flower( 'empty__flower spin' ); // phpcs:ignore ?>
			<p>burada henüz bir şey yok. ama yakında gereksiz ama gerekli bir şey olacak.</p>
			<a class="btn btn--blue" href="<?php echo esc_url( umiko_shop_url() ); ?>">bütün ürünler</a>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
