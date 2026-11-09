<?php
/**
 * Genel sayfa (sepet, ödeme, hesabım ve editörle yazılan diğer sayfalar).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'wrap page ' . ( $is_wc_page ? 'page--wide' : 'page--narrow' ) ); ?>>
		<header class="page__head">
			<?php echo umiko_flower( 'page__flower spin' ); // phpcs:ignore ?>
			<h1 class="page__title"><?php echo esc_html( umiko_lower( get_the_title() ) ); ?></h1>
		</header>
		<div class="page__content prose">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
