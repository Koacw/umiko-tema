<?php
/**
 * Hikâye sayfası (slug: hikaye).
 * Kurucunun hikâyesi: Özelleştir > umiko > hikâye ve sosyal. Sayfa editörüne yazılan içerik de altta görünür.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$story = trim( (string) get_theme_mod( 'umiko_founder_story', '' ) );
?>
<article class="wrap story">
	<header class="story__hero">
		<div class="story__intro">
			<p class="eyebrow"><?php echo umiko_icon( 'heart' ); // phpcs:ignore ?> hikâye</p>
			<h1 class="story__title">istanbul'da küçük bir atölye, kocaman bir çiçek.</h1>
			<p class="story__lead">umiko, "gereksiz ama gerekli" şeyler yapan küçük bir kadın girişimi. evini, masanı ve kedini biraz daha tatlı yapmak için uğraşıyoruz.</p>
		</div>
		<div class="story__portrait">
			<?php echo umiko_placeholder( 'umiko\'nun kurucusu atölyede', 'pink' ); // phpcs:ignore ?>
			<?php echo umiko_flower( 'story__sticker spin' ); // phpcs:ignore ?>
		</div>
	</header>

	<section class="story__body prose" aria-label="kurucunun hikâyesi">
		<?php echo $story ? wp_kses_post( wpautop( esc_html( $story ) ) ) : '<p>' . umiko_todo( 'kurucunun hikâyesi: Özelleştir > umiko > hikâye ve sosyal' ) . '</p>'; // phpcs:ignore ?>
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</section>

	<section class="steps" aria-labelledby="steps-title">
		<h2 id="steps-title" class="section__title">bir şey nasıl umiko olur?</h2>
		<ol class="steps__list">
			<li class="step reveal">
				<span class="step__num">1</span>
				<h3>gereksiz bir fikir</h3>
				<p>"keşke şunun için küçük bir şey olsa" dediğimiz her an bir umiko fikri.</p>
			</li>
			<li class="step reveal">
				<span class="step__num">2</span>
				<h3>katman katman</h3>
				<p>atölyemizdeki Bambu Lab P1S ile PLA'dan, tek tek basıyoruz.</p>
			</li>
			<li class="step reveal">
				<span class="step__num">3</span>
				<h3>senin evinde</h3>
				<p>paketleyip yola çıkarıyoruz. gerisi sende: masanda, mutfağında, kedinin yanında.</p>
			</li>
		</ol>
	</section>

	<section class="story__gallery" aria-label="atölyeden kareler">
		<?php echo umiko_placeholder( 'atölyedeki 3D yazıcı', 'blue' ); // phpcs:ignore ?>
		<?php echo umiko_placeholder( 'ürünü hazırlayan eller', 'pink-soft' ); // phpcs:ignore ?>
		<?php echo umiko_placeholder( 'yakından katmanlar', 'pink' ); // phpcs:ignore ?>
	</section>

	<p class="story__cta">
		<a class="btn btn--blue btn--lg bounce" href="<?php echo esc_url( umiko_shop_url() ); ?>">ürünlere bak <?php echo umiko_icon( 'arrow' ); // phpcs:ignore ?></a>
	</p>
</article>
<?php
get_footer();
