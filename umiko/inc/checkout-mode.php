<?php
/**
 * Satın alma davranışı tek yerden yönetilir.
 *
 *   native   → WooCommerce sepeti: "sepete at", sepet çekmecesi, ödeme sayfası. (varsayılan)
 *   external → Faz 1 vitrin: sepet kapalı, ürünlerde "shopier'den al" ve varsa "etsy'de gör".
 *
 * Ayar: Görünüm > Özelleştir > umiko > satış modu. Kodda: umiko_checkout_mode() / umiko_is_native().
 * Kartlar ve ürün sayfası yalnızca umiko_buy_actions() çağırır; mod değişince şablonlar değişmez.
 */

defined( 'ABSPATH' ) || exit;

function umiko_checkout_mode(): string {
	$mode = get_theme_mod( 'umiko_checkout_mode', 'native' );
	$mode = apply_filters( 'umiko_checkout_mode', $mode );
	return 'external' === $mode ? 'external' : 'native';
}

function umiko_is_native(): bool {
	return 'native' === umiko_checkout_mode();
}

/*
 * Vitrin modunda sepete ekleme tamamen kapanır, sepet ve ödeme sayfaları mağazaya yönlenir.
 */
add_action(
	'wp',
	function () {
		if ( umiko_is_native() ) {
			return;
		}
		add_filter( 'woocommerce_is_purchasable', '__return_false' );
		add_filter( 'woocommerce_variation_is_purchasable', '__return_false' );
	}
);

add_action(
	'template_redirect',
	function () {
		if ( umiko_is_native() || is_admin() ) {
			return;
		}
		if ( is_cart() || ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && ! is_wc_endpoint_url( 'order-pay' ) ) ) {
			wp_safe_redirect( umiko_shop_url() );
			exit;
		}
	}
);

/**
 * Ürünün satın alma alanı.
 *
 * @param string $context 'card' (ürün kartı) ya da 'single' (ürün sayfası)
 */
function umiko_buy_actions( WC_Product $product, string $context = 'card' ): void {
	if ( umiko_is_native() ) {
		umiko_native_form( $product, $context );
	} else {
		umiko_external_links( $product, $context );
	}
}

/**
 * Faz 2: sepete at. JS yoksa form normal POST ile WooCommerce'e gider; JS varsa Store API ile çekmeceye eklenir.
 * Renk seçimi, varyasyon ID'sini taşıyan radio butonlarıdır (WooCommerce varyasyon özniteliklerini kendisi doldurur).
 */
function umiko_native_form( WC_Product $product, string $context ): void {
	$colors   = umiko_product_colors( $product );
	$variable = $product->is_type( 'variable' );
	$is_card  = 'card' === $context;
	$uid      = $context . '-' . $product->get_id();

	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		printf( '<p class="buy__soldout"><span class="btn btn--disabled %s" aria-disabled="true">şu an yok</span></p>', $is_card ? 'btn--sm' : 'btn--lg' );
		return;
	}

	if ( ! in_array( $product->get_type(), array( 'simple', 'variable' ), true ) ) {
		printf( '<a class="btn btn--blue %s" href="%s">incele</a>', $is_card ? 'btn--sm' : 'btn--lg', esc_url( $product->get_permalink() ) );
		return;
	}

	$default_attrs = $variable ? $product->get_default_attributes() : array();
	$default_color = $default_attrs['pa_renk'] ?? '';
	$first_in      = '';
	foreach ( $colors as $c ) {
		if ( $c['in_stock'] && ( '' === $first_in || $c['slug'] === $default_color ) ) {
			$first_in = $c['slug'];
		}
	}
	$first_name = '';
	?>
	<form class="buy buy--<?php echo esc_attr( $context ); ?>" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" data-umiko-add>
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
		<?php if ( $variable && $colors ) : ?>
			<fieldset class="swatches swatches--<?php echo esc_attr( $context ); ?>">
				<?php
				foreach ( $colors as $c ) {
					if ( $c['slug'] === $first_in ) {
						$first_name = $c['name'];
					}
				}
				?>
				<legend class="<?php echo $is_card ? 'sr-only' : 'swatches__legend'; ?>">renk<?php echo $is_card ? '' : ': <output data-swatch-output>' . esc_html( $first_name ) . '</output>'; ?></legend>
				<?php foreach ( $colors as $c ) : ?>
					<label class="swatch" title="<?php echo esc_attr( $c['name'] ); ?>">
						<input type="radio" name="variation_id" value="<?php echo esc_attr( $c['variation_id'] ); ?>" data-name="<?php echo esc_attr( $c['name'] ); ?>" style="--sw: <?php echo esc_attr( umiko_color_value( $c['slug'] ) ); ?>" <?php checked( $c['slug'], $first_in ); ?> <?php disabled( ! $c['in_stock'] ); ?> required>
						<span class="sr-only"><?php echo esc_html( $c['name'] . ( $c['in_stock'] ? '' : ' (şu an yok)' ) ); ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>
		<?php endif; ?>

		<div class="buy__row">
			<?php if ( ! $is_card && ! $product->is_sold_individually() ) : ?>
				<div class="qty" data-qty>
					<label class="sr-only" for="qty-<?php echo esc_attr( $uid ); ?>">adet</label>
					<button type="button" class="qty__btn" data-qty-step="-1" aria-label="bir azalt"><?php echo umiko_icon( 'minus' ); // phpcs:ignore ?></button>
					<input id="qty-<?php echo esc_attr( $uid ); ?>" class="qty__input" type="number" name="quantity" value="1" min="1" max="<?php echo esc_attr( $product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : 99 ); ?>" inputmode="numeric">
					<button type="button" class="qty__btn" data-qty-step="1" aria-label="bir artır"><?php echo umiko_icon( 'plus' ); // phpcs:ignore ?></button>
				</div>
			<?php else : ?>
				<input type="hidden" name="quantity" value="1">
			<?php endif; ?>
			<button type="submit" class="btn btn--blue <?php echo $is_card ? 'btn--sm' : 'btn--lg btn--grow'; ?>" data-label="sepete at">
				<?php echo umiko_icon( 'bag' ); // phpcs:ignore ?>
				<span>sepete at</span>
				<?php if ( $is_card ) : ?><span class="sr-only">: <?php echo esc_html( $product->get_name() ); ?></span><?php endif; ?>
			</button>
		</div>
	</form>
	<?php
}

/**
 * Faz 1: Shopier (ana buton) + Etsy (varsa). Link henüz yoksa görünür [DOLDUR] durumunda pasif buton.
 */
function umiko_external_links( WC_Product $product, string $context ): void {
	$shopier = umiko_meta( $product, 'shopier_url' );
	$etsy    = umiko_meta( $product, 'etsy_url' );
	$size    = 'card' === $context ? 'btn--sm' : 'btn--lg';
	$name    = 'card' === $context ? '<span class="sr-only">: ' . esc_html( $product->get_name() ) . '</span>' : '';
	$new_tab = '<span class="sr-only"> (yeni sekmede açılır)</span>';

	echo '<div class="buy buy--external buy--' . esc_attr( $context ) . '">';
	if ( $shopier ) {
		printf(
			'<a class="btn btn--blue %1$s" href="%2$s" target="_blank" rel="noopener external">shopier\'den al%3$s%4$s %5$s</a>',
			esc_attr( $size ),
			esc_url( $shopier ),
			$name, // phpcs:ignore
			$new_tab, // phpcs:ignore
			umiko_icon( 'external' ) // phpcs:ignore
		);
	} else {
		printf(
			'<span class="btn btn--disabled %s" aria-disabled="true">shopier\'den al</span>%s',
			esc_attr( $size ),
			'single' === $context ? '<p class="buy__note">' . umiko_todo( 'shopier linki: ürün düzenle > umiko sekmesi' ) . '</p>' : '' // phpcs:ignore
		);
	}
	if ( $etsy ) {
		printf(
			'<a class="btn btn--ghost %1$s" href="%2$s" target="_blank" rel="noopener external">etsy\'de gör%3$s%4$s %5$s</a>',
			esc_attr( $size ),
			esc_url( $etsy ),
			$name, // phpcs:ignore
			$new_tab, // phpcs:ignore
			umiko_icon( 'external' ) // phpcs:ignore
		);
	}
	echo '</div>';
}
