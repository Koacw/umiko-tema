<?php
/**
 * Ürün detay: galeri, ad, fiyat (KDV dahil), renkler, satın alma, ayrıntılar,
 * "gereksiz ama gerekli" itirafı, "bunlar da gereksiz ama gerekli".
 *
 * @package umiko
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	global $product;
	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}

	$image_ids = array_values( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) );
	$badge     = umiko_badge( $product, true );
	$lead      = umiko_meta( $product, 'lead_time' );
	$care      = umiko_meta( $product, 'care' );
	$material  = umiko_meta( $product, 'material' );
	$confess   = umiko_meta( $product, 'confession' );
	$reason    = umiko_meta( $product, 'reason' );
	$cat_ids   = $product->get_category_ids();
	$cat       = $cat_ids ? get_term( $cat_ids[0], 'product_cat' ) : null;

	do_action( 'woocommerce_before_single_product' ); // WooCommerce bildirimleri (ör. "sepete eklendi") buraya düşer.
	?>
	<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'wrap pdp', $product ); ?>>
		<nav class="crumbs" aria-label="konum">
			<a href="<?php echo esc_url( umiko_shop_url() ); ?>">ürünler</a>
			<?php if ( $cat && ! is_wp_error( $cat ) ) : ?>
				<span aria-hidden="true">/</span> <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( umiko_lower( $cat->name ) ); ?></a>
			<?php endif; ?>
		</nav>

		<div class="pdp__grid">
			<div class="pdp__gallery gallery" data-gallery>
				<div class="gallery__track" data-gallery-track tabindex="0" aria-label="ürün görselleri">
					<?php if ( $image_ids ) : ?>
						<?php foreach ( $image_ids as $i => $image_id ) : ?>
							<figure class="gallery__slide" <?php echo 0 === $i ? 'style="view-transition-name: product-' . esc_attr( $product->get_id() ) . '"' : ''; ?>>
								<?php
								echo wp_get_attachment_image( // phpcs:ignore
									$image_id,
									'woocommerce_single',
									false,
									array(
										'alt'           => trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) ?: $product->get_name() . ' ' . ( $i + 1 ),
										'loading'       => 0 === $i ? 'eager' : 'lazy',
										'fetchpriority' => 0 === $i ? 'high' : 'auto',
									)
								);
								?>
							</figure>
						<?php endforeach; ?>
					<?php else : ?>
						<figure class="gallery__slide" style="view-transition-name: product-<?php echo esc_attr( $product->get_id() ); ?>">
							<?php echo umiko_scene_placeholder( $product, true ); // phpcs:ignore ?>
						</figure>
					<?php endif; ?>
				</div>
				<?php if ( count( $image_ids ) > 1 ) : ?>
					<div class="gallery__thumbs" role="group" aria-label="görsel seç">
						<?php foreach ( $image_ids as $i => $image_id ) : ?>
							<button type="button" class="gallery__thumb" data-gallery-go="<?php echo esc_attr( $i ); ?>" <?php echo 0 === $i ? 'aria-current="true"' : ''; ?>>
								<?php echo wp_get_attachment_image( $image_id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '' ) ); // phpcs:ignore ?>
								<span class="sr-only"><?php echo esc_html( ( $i + 1 ) . '. görsel' ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="pdp__info">
				<?php if ( $badge ) : ?>
					<span class="badge <?php echo str_starts_with( $badge, 'yeni' ) ? 'badge--blue' : 'badge--pink'; ?>"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
				<h1 class="pdp__title"><?php echo esc_html( $product->get_name() ); ?></h1>
				<p class="pdp__price">
					<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
					<span class="pdp__vat">KDV dahil</span>
				</p>
				<?php if ( $product->get_short_description() ) : ?>
					<div class="pdp__short"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
				<?php endif; ?>

				<?php umiko_buy_actions( $product, 'single' ); ?>

				<ul class="facts">
					<li><?php echo umiko_icon( 'clock' ); // phpcs:ignore ?><span><strong>hazırlık süresi</strong> <?php echo $lead ? esc_html( $lead ) : umiko_todo( 'hazırlık süresi' ); // phpcs:ignore ?></span></li>
					<?php if ( $material ) : ?>
						<li><?php echo umiko_icon( 'layers' ); // phpcs:ignore ?><span><strong>malzeme</strong> <?php echo esc_html( $material ); ?></span></li>
					<?php endif; ?>
				</ul>

				<div class="details">
					<details name="pdp-details" open>
						<summary>açıklama</summary>
						<div class="details__body prose"><?php the_content(); ?></div>
					</details>
					<details name="pdp-details">
						<summary>ölçüler</summary>
						<div class="details__body">
							<p>
								<?php
								echo $product->has_dimensions() // phpcs:ignore
									? esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) ) . ' (uzunluk × genişlik × yükseklik)'
									: umiko_todo( 'ölçüler: ürün düzenle > gönderim sekmesi' );
								?>
							</p>
						</div>
					</details>
					<details name="pdp-details">
						<summary>bakım ve kullanım</summary>
						<div class="details__body"><p><?php echo $care ? esc_html( $care ) : umiko_todo( 'bakım notu' ); // phpcs:ignore ?></p></div>
					</details>
					<details name="pdp-details">
						<summary>kargo ve iade</summary>
						<div class="details__body">
							<p>ayrıntılar <a href="<?php echo esc_url( umiko_page_url( 'kargo-ve-teslimat' ) ); ?>">kargo ve teslimat</a> ile <a href="<?php echo esc_url( umiko_page_url( 'iade-ve-cayma-hakki' ) ); ?>">iade ve cayma hakkı</a> sayfalarında.</p>
						</div>
					</details>
				</div>
			</div>
		</div>

		<?php if ( $confess && $reason ) : ?>
			<section class="verdict reveal" aria-label="gereksiz ama gerekli">
				<?php echo umiko_flower( 'verdict__flower spin' ); // phpcs:ignore ?>
				<div class="verdict__col">
					<p class="verdict__tag">gereksiz</p>
					<p class="verdict__text"><?php echo esc_html( $confess ); ?></p>
				</div>
				<div class="verdict__col verdict__col--b">
					<p class="verdict__tag">gerekli</p>
					<p class="verdict__text"><?php echo esc_html( $reason ); ?></p>
				</div>
			</section>
		<?php endif; ?>
	</article>

	<?php
	// Yapılandırılmış veri: WooCommerce Product şeması (fiyat mağaza para birimiyle, TRY). wp_footer'da basılır.
	if ( isset( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data( $product );
	}

	$related = wc_get_products(
		array(
			'limit'      => 4,
			'status'     => 'publish',
			'visibility' => 'catalog',
			'exclude'    => array( $product->get_id() ),
			'orderby'    => 'rand',
		)
	);
	if ( $related ) :
		?>
		<section class="section wrap" aria-labelledby="related-title">
			<header class="section__head reveal">
				<h2 id="related-title" class="section__title">bunlar da gereksiz ama gerekli</h2>
				<a class="link-arrow" href="<?php echo esc_url( umiko_shop_url() ); ?>">hepsini gör <?php echo umiko_icon( 'arrow' ); // phpcs:ignore ?></a>
			</header>
			<ul class="product-grid product-grid--4">
				<?php
				foreach ( $related as $item ) {
					get_template_part( 'template-parts/product-card', null, array( 'product' => $item ) );
				}
				?>
			</ul>
		</section>
		<?php
	endif;

	do_action( 'woocommerce_after_single_product' );
endwhile;

get_footer();
