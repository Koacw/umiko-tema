<?php
/**
 * Tema etkinleşince eksik sayfaları oluşturur: hikâye, sss, iletişim ve 6 yasal sayfa.
 * Var olan sayfalara dokunmaz. Görünüm > umiko sayfaları ekranından tekrar çalıştırılabilir.
 */

defined( 'ABSPATH' ) || exit;

function umiko_create_pages(): int {
	$pages = array(
		'hikaye'   => array( 'hikâye', '' ),
		'sss'      => array( 'sss', '' ),
		'iletisim' => array( 'iletişim', '' ),
	);
	foreach ( umiko_legal_pages() as $slug => $title ) {
		$pages[ $slug ] = array( $title, 'page-templates/legal.php' );
	}

	$created = 0;
	foreach ( $pages as $slug => [ $title, $template ] ) {
		if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => '',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			if ( $template ) {
				update_post_meta( $id, '_wp_page_template', $template );
			}
			++$created;
		}
	}
	return $created;
}

add_action( 'after_switch_theme', 'umiko_create_pages' );

add_action(
	'admin_menu',
	function () {
		add_theme_page(
			'umiko sayfaları',
			'umiko sayfaları',
			'edit_pages',
			'umiko-pages',
			function () {
				$created = null;
				if ( isset( $_POST['umiko_create_pages'] ) && check_admin_referer( 'umiko_create_pages' ) ) {
					$created = umiko_create_pages();
				}
				?>
				<div class="wrap">
					<h1>umiko sayfaları</h1>
					<?php if ( null !== $created ) : ?>
						<div class="notice notice-success"><p><?php echo esc_html( $created ); ?> sayfa oluşturuldu.</p></div>
					<?php endif; ?>
					<p>Hikâye, SSS, iletişim ve yasal sayfaların (Mesafeli Satış Sözleşmesi, Ön Bilgilendirme Formu, İade ve Cayma Hakkı, KVKK, Çerez Politikası, Kargo ve Teslimat) eksik olanlarını oluşturur. Var olan sayfalara dokunmaz.</p>
					<form method="post">
						<?php wp_nonce_field( 'umiko_create_pages' ); ?>
						<p><button class="button button-primary" name="umiko_create_pages" value="1">eksik sayfaları oluştur</button></p>
					</form>
				</div>
				<?php
			}
		);
	}
);
