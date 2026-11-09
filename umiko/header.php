<?php
/**
 * Üst kısım: logo, menü, sepet. Mobilde menü <dialog> içinde açılır.
 */

defined( 'ABSPATH' ) || exit;

$native     = function_exists( 'umiko_is_native' ) && umiko_is_native();
$cart_count = $native && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">içeriğe geç</a>

<header class="site-header">
	<div class="site-header__inner wrap">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<img src="<?php echo esc_url( UMIKO_URI . '/assets/brand/umiko-logo.png' ); ?>" width="1314" height="390" alt="umiko ana sayfa" fetchpriority="high">
		</a>

		<nav class="site-nav" aria-label="ana menü">
			<?php umiko_nav( 'primary', 'site-nav__list' ); ?>
		</nav>

		<div class="site-header__actions">
			<?php if ( $native ) : ?>
				<button type="button" class="icon-btn cart-btn" data-open-dialog="cart-drawer" aria-haspopup="dialog">
					<?php echo umiko_icon( 'bag' ); // phpcs:ignore ?>
					<span class="sr-only">sepet</span>
					<span class="cart-btn__count" data-cart-count <?php echo $cart_count ? '' : 'hidden'; ?>><?php echo esc_html( $cart_count ); ?></span>
					<span class="sr-only">ürün</span>
				</button>
			<?php endif; ?>
			<button type="button" class="icon-btn menu-btn" data-open-dialog="mobile-menu" aria-haspopup="dialog">
				<?php echo umiko_icon( 'menu' ); // phpcs:ignore ?>
				<span class="sr-only">menüyü aç</span>
			</button>
		</div>
	</div>
</header>

<dialog id="mobile-menu" class="sheet sheet--menu" aria-label="menü" closedby="any">
	<div class="sheet__head">
		<img class="sheet__logo" src="<?php echo esc_url( UMIKO_URI . '/assets/brand/umiko-logo-pembe-zemin.png' ); ?>" width="1314" height="390" alt="umiko">
		<button type="button" class="icon-btn" data-close-dialog>
			<?php echo umiko_icon( 'close' ); // phpcs:ignore ?><span class="sr-only">menüyü kapat</span>
		</button>
	</div>
	<nav aria-label="mobil menü">
		<?php umiko_nav( 'primary', 'sheet__nav' ); ?>
	</nav>
	<?php echo umiko_flower( 'sheet__flower spin' ); // phpcs:ignore ?>
</dialog>

<main id="main" tabindex="-1">
