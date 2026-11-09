<?php
/**
 * Ürün kartı. Ana sayfa, mağaza, kategoriler ve "bunlar da gereksiz ama gerekli" listesi bunu kullanır.
 *
 * @var array $args { product: WC_Product, eager?: bool }
 */

defined( 'ABSPATH' ) || exit;

/** @var WC_Product $product */
$product = $args['product'];
$url     = $product->get_permalink();
$badge   = umiko_badge( $product );
$colors  = umiko_product_colors( $product );
$native  = umiko_is_native();
?>
<li <?php wc_product_class( 'card card--' . umiko_tint( $product->get_id() ), $product ); ?>>
	<a class="card__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true" style="view-transition-name: product-<?php echo esc_attr( $product->get_id() ); ?>">
		<?php echo umiko_product_media( $product, 'woocommerce_thumbnail', empty( $args['eager'] ) ? 'lazy' : 'eager' ); // phpcs:ignore ?>
	</a>
	<?php if ( $badge ) : ?>
		<span class="badge <?php echo 'yeni' === $badge ? 'badge--blue' : 'badge--pink'; ?> card__badge"><?php echo esc_html( $badge ); ?></span>
	<?php endif; ?>

	<div class="card__body">
		<h3 class="card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<div class="card__meta">
			<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<?php if ( $colors && ! $native ) : ?>
				<span class="dots" role="img" aria-label="renkler: <?php echo esc_attr( implode( ', ', array_column( $colors, 'name' ) ) ); ?>">
					<?php foreach ( $colors as $c ) : ?>
						<span class="dot" style="--sw: <?php echo esc_attr( umiko_color_value( $c['slug'] ) ); ?>"></span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
		</div>
		<?php umiko_buy_actions( $product, 'card' ); ?>
	</div>
</li>
