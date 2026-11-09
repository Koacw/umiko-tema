<?php
/**
 * İletişim (slug: iletisim): e-posta, Instagram, satıcı bilgileri.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$email     = trim( (string) get_theme_mod( 'umiko_seller_email', '' ) );
$instagram = umiko_instagram_url();
?>
<div class="wrap page page--narrow">
	<header class="page__head">
		<?php echo umiko_flower( 'page__flower spin' ); // phpcs:ignore ?>
		<h1 class="page__title">bize yaz</h1>
		<p class="page__lead">sorun, fikrin ya da sadece bir merhaban varsa buradayız.</p>
	</header>

	<div class="contact">
		<div class="contact__card contact__card--blue">
			<?php echo umiko_icon( 'mail' ); // phpcs:ignore ?>
			<h2>e-posta</h2>
			<?php if ( $email ) : ?>
				<a class="contact__link" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
			<?php else : ?>
				<p><?php echo umiko_todo( 'e-posta' ); // phpcs:ignore ?></p>
			<?php endif; ?>
		</div>
		<div class="contact__card contact__card--pink">
			<?php echo umiko_icon( 'instagram' ); // phpcs:ignore ?>
			<h2>instagram</h2>
			<?php if ( $instagram ) : ?>
				<a class="contact__link" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener me">@<?php echo esc_html( ltrim( get_theme_mod( 'umiko_instagram' ), '@' ) ); ?><span class="sr-only"> (yeni sekmede açılır)</span></a>
			<?php else : ?>
				<p><?php echo umiko_todo( 'instagram kullanıcı adı' ); // phpcs:ignore ?></p>
			<?php endif; ?>
		</div>
	</div>

	<section class="legal-box" aria-labelledby="seller-title">
		<h2 id="seller-title">satıcı bilgileri</h2>
		<?php get_template_part( 'template-parts/seller-info' ); ?>
	</section>

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
