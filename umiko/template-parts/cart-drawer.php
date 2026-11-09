<?php
/**
 * Sepet çekmecesi. İçeriği assets/js/umiko.js WooCommerce Store API'den doldurur.
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog id="cart-drawer" class="sheet sheet--cart" aria-labelledby="cart-drawer-title" closedby="any">
	<div class="sheet__head">
		<h2 id="cart-drawer-title" class="sheet__title">sepetin</h2>
		<button type="button" class="icon-btn" data-close-dialog>
			<?php echo umiko_icon( 'close' ); // phpcs:ignore ?><span class="sr-only">sepeti kapat</span>
		</button>
	</div>

	<p class="cart__error" data-cart-error hidden></p>

	<div class="cart__empty" data-cart-empty>
		<?php echo umiko_flower( 'cart__empty-flower spin' ); // phpcs:ignore ?>
		<p>sepetin boş. ama tamamen gereksiz değil, bir şeyler ekleyebilirsin.</p>
		<a class="btn btn--blue" href="<?php echo esc_url( umiko_shop_url() ); ?>">ürünlere bak</a>
	</div>

	<ul class="cart__items" data-cart-items hidden></ul>

	<div class="cart__foot" data-cart-foot hidden>
		<p class="cart__total"><span>ara toplam</span> <strong data-cart-subtotal></strong></p>
		<p class="cart__note">KDV dahil. kargo ödeme adımında hesaplanır.</p>
		<a class="btn btn--blue btn--lg btn--block" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">ödemeye geç</a>
		<a class="cart__link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">sepet sayfasına git</a>
	</div>

	<template data-cart-item-template>
		<li class="cart-item">
			<a class="cart-item__media" data-f="link" tabindex="-1" aria-hidden="true"></a>
			<div class="cart-item__body">
				<a class="cart-item__name" data-f="link"><span data-f="name"></span></a>
				<p class="cart-item__meta" data-f="meta"></p>
				<div class="cart-item__row">
					<div class="qty qty--sm">
						<button type="button" class="qty__btn" data-action="dec"><?php echo umiko_icon( 'minus' ); // phpcs:ignore ?><span class="sr-only">bir azalt</span></button>
						<span class="qty__value" data-f="qty"></span>
						<button type="button" class="qty__btn" data-action="inc"><?php echo umiko_icon( 'plus' ); // phpcs:ignore ?><span class="sr-only">bir artır</span></button>
					</div>
					<strong class="cart-item__price" data-f="price"></strong>
				</div>
			</div>
			<button type="button" class="icon-btn icon-btn--sm cart-item__remove" data-action="remove"><?php echo umiko_icon( 'trash' ); // phpcs:ignore ?><span class="sr-only">sepetten çıkar</span></button>
		</li>
	</template>
</dialog>
