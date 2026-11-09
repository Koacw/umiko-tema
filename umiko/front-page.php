<?php
/**
 * Ana sayfa: hero, kayan bant, ürünler, kategoriler, "gereksiz ama gerekli", atölye, kulüp.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$has_wc   = function_exists( 'wc_get_products' );
$products = $has_wc ? wc_get_products( array( 'limit' => 6, 'status' => 'publish', 'visibility' => 'catalog', 'orderby' => 'menu_order', 'order' => 'ASC' ) ) : array();
$confess  = $has_wc ? array_filter(
	wc_get_products( array( 'limit' => 12, 'status' => 'publish', 'visibility' => 'catalog', 'orderby' => 'menu_order', 'order' => 'ASC' ) ),
	fn( $p ) => umiko_meta( $p, 'confession' ) && umiko_meta( $p, 'reason' )
) : array();
$confess  = array_slice( $confess, 0, 3 );
$story    = trim( (string) get_theme_mod( 'umiko_founder_story', '' ) );
?>

<section class="hero wrap" aria-labelledby="hero-title">
	<div class="hero__text">
		<p class="bubble hero__hello">merhaba, biz umiko!</p>
		<h1 id="hero-title" class="hero__title">gereksiz ama gerekli tatlı şeyler.</h1>
		<p class="hero__lead">masana, mutfağına, kedine küçük tatlılıklar. istanbul'da, katman katman basıyoruz.</p>
		<div class="hero__actions">
			<a class="btn btn--blue btn--lg" href="<?php echo esc_url( umiko_shop_url() ); ?>">ürünlere bak <?php echo umiko_icon( 'arrow' ); // phpcs:ignore ?></a>
			<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( umiko_page_url( 'hikaye' ) ); ?>">hikâyemiz</a>
		</div>
	</div>

	<?php // Kolaj: ürünlerin nasıl kullanıldığını anlatan eğik comic kareler. ?>
	<div class="hero__collage" role="img" aria-label="çizimler: kedisine mama veren bir kız, mutfakta patates soyup kabuğu tezgâha takılı poşete atan bir kız, kanepede kumandayı duvardaki kumandalıktan alan bir kız">
		<figure class="polaroid polaroid--1">
			<div class="scene scene--pink-soft"><?php echo umiko_illustration( 'mama' ); // phpcs:ignore ?></div>
			<figcaption>kedine</figcaption>
		</figure>
		<figure class="polaroid polaroid--2">
			<div class="scene scene--blue"><?php echo umiko_illustration( 'cop' ); // phpcs:ignore ?></div>
			<figcaption>mutfağına</figcaption>
		</figure>
		<figure class="polaroid polaroid--3">
			<div class="scene scene--pink"><?php echo umiko_illustration( 'kumanda' ); // phpcs:ignore ?></div>
			<figcaption>kanepene</figcaption>
		</figure>
		<div class="hero__face scene scene--blue"><?php echo umiko_illustration( 'kedi-yuz' ); // phpcs:ignore ?></div>
		<?php echo umiko_flower( 'hero__flower spin' ); // phpcs:ignore ?>
		<span class="sfx hero__sfx" aria-hidden="true">tatlı!</span>
	</div>
</section>

<div class="marquee-clip"><div class="marquee" aria-hidden="true">
	<div class="marquee__track">
		<?php
		$words = array( 'gereksiz ama gerekli', 'istanbul\'da basıldı', 'katman katman', 'tek tek', 'kedine', 'mutfağına', 'masana' );
		for ( $i = 0; $i < 2; $i++ ) {
			foreach ( $words as $w ) {
				echo '<span>' . esc_html( $w ) . '</span>' . umiko_flower( 'marquee__flower' ); // phpcs:ignore
			}
		}
		?>
	</div>
</div></div>

<?php if ( $products ) : ?>
<section class="section wrap" aria-labelledby="products-title">
	<header class="section__head reveal">
		<h2 id="products-title" class="section__title">tatlı şeyler</h2>
		<a class="link-arrow" href="<?php echo esc_url( umiko_shop_url() ); ?>">hepsini gör <?php echo umiko_icon( 'arrow' ); // phpcs:ignore ?></a>
	</header>
	<ul class="product-grid">
		<?php
		foreach ( $products as $i => $product ) {
			get_template_part( 'template-parts/product-card', null, array( 'product' => $product, 'eager' => $i < 2 ) );
		}
		?>
	</ul>
</section>
<?php endif; ?>

<?php
$cats = array(
	'kedi'   => array( 'kedine', 'blue' ),
	'mutfak' => array( 'mutfağına', 'pink' ),
	'masa'   => array( 'masana', 'pink-soft' ),
	'ev'     => array( 'evine', 'cream' ),
);
$tiles = array();
if ( $has_wc ) {
	foreach ( $cats as $slug => [ $label, $tint ] ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && $term->count ) {
			$tiles[] = array( $label, $tint, get_term_link( $term ), $term->count );
		}
	}
}
if ( $tiles ) :
	?>
<section class="section wrap" aria-labelledby="cats-title">
	<header class="section__head reveal">
		<h2 id="cats-title" class="section__title">nereye lazım?</h2>
	</header>
	<ul class="tiles">
		<?php foreach ( $tiles as [ $label, $tint, $link, $count ] ) : ?>
			<li class="tile tile--<?php echo esc_attr( $tint ); ?> reveal">
				<a href="<?php echo esc_url( $link ); ?>">
					<span class="tile__label"><?php echo esc_html( $label ); ?></span>
					<span class="tile__count"><?php echo esc_html( $count ); ?> şey</span>
					<?php echo umiko_flower( 'tile__flower' ); // phpcs:ignore ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
<?php endif; ?>

<?php if ( $confess ) : ?>
<section class="section wrap confess-section" aria-labelledby="confess-title">
	<header class="section__head section__head--center reveal">
		<?php echo umiko_flower( 'divider-flower' ); // phpcs:ignore ?>
		<h2 id="confess-title" class="section__title">gereksiz ama gerekli.</h2>
		<p class="section__lead">her ürünün bir itirafı var. anahtarı çevir, hikâyenin sonunu gör.</p>
	</header>
	<ul class="confess-list">
		<?php
		foreach ( $confess as $i => $product ) :
			$scene = umiko_product_scene( $product );
			?>
			<li class="strip confess reveal" data-confess>
				<?php if ( $scene ) : ?>
					<div class="strip__panel strip__art scene scene--<?php echo esc_attr( umiko_scenes()[ $scene['name'] ]['bg'] ); ?>">
						<?php echo umiko_illustration( $scene['name'], $product->get_name() . ': kullanım çizimi', $scene['crop'] ); // phpcs:ignore ?>
					</div>
				<?php endif; ?>
				<div class="strip__panel strip__talk">
					<p class="caption"><span><?php echo esc_html( ( $i + 1 ) . '. bölüm' ); ?></span> <a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></p>
					<div class="bubble bubble--big confess__text">
						<p class="confess__a"><span class="confess__tag">gereksiz:</span> <?php echo esc_html( umiko_meta( $product, 'confession' ) ); ?></p>
						<p class="confess__b"><span class="confess__tag">gerekli:</span> <?php echo esc_html( umiko_meta( $product, 'reason' ) ); ?></p>
					</div>
					<button type="button" class="switch" aria-pressed="false" data-confess-toggle>
						<span class="sr-only"><?php echo esc_html( $product->get_name() ); ?>: </span>
						<span class="switch__label switch__label--a" aria-hidden="true">gereksiz</span>
						<span class="switch__track" aria-hidden="true"><span class="switch__thumb"><?php echo umiko_flower(); // phpcs:ignore ?></span></span>
						<span class="switch__label switch__label--b" aria-hidden="true">gerekli</span>
						<span class="sr-only">neden gerekli olduğunu göster</span>
					</button>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
<?php endif; ?>

<section class="section wrap workshop" aria-labelledby="workshop-title">
	<div class="workshop__text reveal">
		<p class="eyebrow"><?php echo umiko_icon( 'layers' ); // phpcs:ignore ?> atölyeden</p>
		<h2 id="workshop-title" class="section__title">istanbul'da küçük bir atölye.</h2>
		<p>ürünlerimizin çoğunu istanbul'daki atölyemizde, bir Bambu Lab P1S ile PLA'dan, tek tek basıyoruz. yakında el yapımı şeyler de gelecek.</p>
		<div class="workshop__story">
			<?php echo $story ? wp_kses_post( wpautop( esc_html( $story ) ) ) : '<p>' . umiko_todo( 'kurucunun kısa hikâyesi: Özelleştir > umiko > hikâye ve sosyal' ) . '</p>'; // phpcs:ignore ?>
		</div>
		<a class="btn btn--blue" href="<?php echo esc_url( umiko_page_url( 'hikaye' ) ); ?>">hikâyenin devamı</a>
	</div>
	<div class="workshop__media">
		<div class="workshop__ph workshop__ph--1 scene scene--cream reveal"><?php echo umiko_illustration( 'atolye', 'çizim: atölyedeki 3D yazıcı katman katman bir çiçek basıyor' ); // phpcs:ignore ?></div>
		<div class="workshop__ph workshop__ph--2 reveal"><?php echo umiko_placeholder( 'ürünü hazırlayan eller', 'pink' ); // phpcs:ignore ?></div>
		<div class="workshop__print reveal" aria-hidden="true">
			<div class="print">
				<?php echo umiko_flower( 'print__flower' ); // phpcs:ignore ?>
				<span class="print__nozzle"></span>
			</div>
			<span class="print__label">katman katman</span>
		</div>
	</div>
</section>

<div class="wrap section">
	<?php get_template_part( 'template-parts/club' ); ?>
</div>

<?php
get_footer();
