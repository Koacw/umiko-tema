<?php
/**
 * Yedek şablon. (Blog kapsam dışı; yazılar varsa sade bir liste.)
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="wrap page page--narrow">
	<header class="page__head">
		<?php echo umiko_flower( 'page__flower spin' ); // phpcs:ignore ?>
		<h1 class="page__title"><?php echo esc_html( umiko_lower( wp_strip_all_tags( get_the_archive_title() ?: get_bloginfo( 'name' ) ) ) ); ?></h1>
	</header>
	<?php if ( have_posts() ) : ?>
		<ul class="post-list">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
		</ul>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p>burada henüz bir şey yok.</p>
	<?php endif; ?>
</div>
<?php
get_footer();
