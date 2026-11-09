<?php
/**
 * Ürünler > umiko örnek ürünler
 * demo/products.php içindeki kataloğu WooCommerce ürünlerine çevirir (renkler → varyasyonlar).
 * Örnek ürünleri tek tıkla siler. Örnek ürün yayındayken yöneticiye uyarı gösterir.
 */

defined( 'ABSPATH' ) || exit;

const UMIKO_COLOR_TAX = 'pa_renk';

/**
 * Global "renk" özniteliğini ve terimlerini hazırlar.
 */
function umiko_ensure_color_attribute( array $colors ): void {
	if ( ! wc_attribute_taxonomy_id_by_name( 'renk' ) ) {
		wc_create_attribute(
			array(
				'name'         => 'renk',
				'slug'         => 'renk',
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);
		delete_transient( 'wc_attribute_taxonomies' );
	}
	// Öznitelik bu istekte oluşturulduysa taksonomi henüz kayıtlı değildir.
	if ( ! taxonomy_exists( UMIKO_COLOR_TAX ) ) {
		register_taxonomy( UMIKO_COLOR_TAX, array( 'product', 'product_variation' ), array( 'hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false ) );
	}
	foreach ( $colors as $color ) {
		if ( ! term_exists( $color, UMIKO_COLOR_TAX ) ) {
			wp_insert_term( $color, UMIKO_COLOR_TAX, array( 'slug' => $color ) );
		}
	}
}

function umiko_ensure_term( string $slug, string $taxonomy ): int {
	$names = array(
		'kedi'   => 'kedi',
		'mutfak' => 'mutfak',
		'masa'   => 'masa',
		'ev'     => 'ev',
	);
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$created = wp_insert_term( $names[ $slug ] ?? $slug, $taxonomy, array( 'slug' => $slug ) );
	return is_wp_error( $created ) ? 0 : (int) $created['term_id'];
}

/**
 * @return array{created:int, skipped:int}
 */
function umiko_import_demo_products( ?string $only_seed = null ): array {
	$catalog = require UMIKO_DIR . '/demo/products.php';
	$colors  = array_unique( array_merge( ...array_column( $catalog, 'colors' ) ) );
	umiko_ensure_color_attribute( $colors );

	$result = array( 'created' => 0, 'skipped' => 0 );
	$order  = 0;

	foreach ( $catalog as $item ) {
		++$order;
		if ( $only_seed && $only_seed !== $item['seed'] ) {
			continue;
		}
		$existing = get_page_by_path( $item['slug'], OBJECT, 'product' );
		if ( $existing ) {
			// Var olan ürüne dokunma; yalnızca çizim alanı hiç ayarlanmamışsa doldur.
			if ( ! metadata_exists( 'post', $existing->ID, '_umiko_scene' ) ) {
				update_post_meta( $existing->ID, '_umiko_scene', $item['scene'] ?? '' );
				update_post_meta( $existing->ID, '_umiko_scene_crop', $item['scene_crop'] ?? '' );
			}
			++$result['skipped'];
			continue;
		}

		$price = wc_format_decimal( $item['price'] / 100, 2 );

		$product = new WC_Product_Variable();
		$product->set_name( $item['name'] );
		$product->set_slug( $item['slug'] );
		$product->set_status( 'publish' );
		$product->set_menu_order( $order );
		$product->set_short_description( $item['short'] );
		$product->set_description( $item['description'] );
		$product->set_category_ids( array_filter( array( umiko_ensure_term( $item['category'], 'product_cat' ) ) ) );
		$product->set_tag_ids( array_filter( array( umiko_ensure_term( 'ornek' === $item['seed'] ? 'ornek-urun' : 'lansman', 'product_tag' ) ) ) );

		if ( $item['dimensions'] ) {
			[ $l, $w, $h ] = $item['dimensions'];
			$product->set_length( $l );
			$product->set_width( $w );
			$product->set_height( $h );
		}

		$term_ids = array();
		foreach ( $item['colors'] as $color ) {
			$term = get_term_by( 'slug', $color, UMIKO_COLOR_TAX );
			if ( $term ) {
				$term_ids[] = $term->term_id;
			}
		}
		$attribute = new WC_Product_Attribute();
		$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'renk' ) );
		$attribute->set_name( UMIKO_COLOR_TAX );
		$attribute->set_options( $term_ids );
		$attribute->set_visible( true );
		$attribute->set_variation( true );
		$product->set_attributes( array( $attribute ) );
		$product->set_default_attributes( array( UMIKO_COLOR_TAX => $item['colors'][0] ) );

		foreach ( array( 'confession', 'reason', 'care', 'lead_time', 'badge' ) as $key ) {
			$product->update_meta_data( '_umiko_' . $key, $item[ $key ] );
		}
		$product->update_meta_data( '_umiko_material', $item['material'] ?? 'PLA, istanbul\'da 3D baskı' );
		$product->update_meta_data( '_umiko_scene', $item['scene'] ?? '' );
		$product->update_meta_data( '_umiko_scene_crop', $item['scene_crop'] ?? '' );
		$product->update_meta_data( '_umiko_shopier_url', '' );
		$product->update_meta_data( '_umiko_etsy_url', '' );
		$product->update_meta_data( '_umiko_seed', $item['seed'] );

		$parent_id = $product->save();

		foreach ( $item['colors'] as $i => $color ) {
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $parent_id );
			$variation->set_attributes( array( UMIKO_COLOR_TAX => $color ) );
			$variation->set_regular_price( $price );
			$variation->set_stock_status( 'instock' );
			$variation->set_menu_order( $i );
			$variation->save();
		}
		wp_set_object_terms( $parent_id, $term_ids, UMIKO_COLOR_TAX );
		WC_Product_Variable::sync( $parent_id );
		wc_delete_product_transients( $parent_id );

		++$result['created'];
	}
	return $result;
}

function umiko_seed_product_ids( string $seed ): array {
	return wc_get_products(
		array(
			'limit'      => -1,
			'status'     => array( 'publish', 'draft', 'private' ),
			'return'     => 'ids',
			'meta_key'   => '_umiko_seed', // phpcs:ignore
			'meta_value' => $seed, // phpcs:ignore
		)
	);
}

function umiko_delete_seed_products( string $seed ): int {
	$count = 0;
	foreach ( umiko_seed_product_ids( $seed ) as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product ) {
			continue;
		}
		foreach ( $product->get_children() as $child_id ) {
			wp_delete_post( $child_id, true );
		}
		$product->delete( true );
		++$count;
	}
	return $count;
}

add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'edit.php?post_type=product',
			'umiko örnek ürünler',
			'umiko örnek ürünler',
			'manage_woocommerce',
			'umiko-demo',
			'umiko_demo_screen'
		);
	}
);

function umiko_demo_screen(): void {
	$message = '';
	if ( isset( $_POST['umiko_demo_action'] ) && check_admin_referer( 'umiko_demo' ) ) {
		$action = sanitize_key( $_POST['umiko_demo_action'] );
		if ( 'import_all' === $action || 'import_lansman' === $action ) {
			$r       = umiko_import_demo_products( 'import_lansman' === $action ? 'lansman' : null );
			$message = sprintf( '%d ürün oluşturuldu, %d ürün zaten vardı.', $r['created'], $r['skipped'] );
		} elseif ( 'delete_ornek' === $action ) {
			$message = sprintf( '%d örnek ürün silindi.', umiko_delete_seed_products( 'ornek' ) );
		}
	}
	$demo_count = count( umiko_seed_product_ids( 'ornek' ) );
	?>
	<div class="wrap">
		<h1>umiko örnek ürünler</h1>
		<?php if ( $message ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
		<?php endif; ?>
		<p>Katalog: <code>wp-content/themes/umiko/demo/products.php</code>. 3 lansman ürünü (fiyatları örnek) + 9 uydurma örnek ürün. Renkler varyasyon olarak oluşturulur; fotoğraflar yok, temada "fotoğraf gelecek" yer tutucusu görünür.</p>
		<form method="post">
			<?php wp_nonce_field( 'umiko_demo' ); ?>
			<p>
				<button class="button button-primary" name="umiko_demo_action" value="import_all">tüm kataloğu yükle (12 ürün)</button>
				<button class="button" name="umiko_demo_action" value="import_lansman">sadece 3 lansman ürününü yükle</button>
			</p>
			<p>
				<button class="button button-link-delete" name="umiko_demo_action" value="delete_ornek" onclick="return confirm('Örnek ürünler kalıcı olarak silinsin mi? Lansman ürünlerine dokunulmaz.');" <?php disabled( 0 === $demo_count ); ?>>örnek ürünleri sil (<?php echo esc_html( $demo_count ); ?>)</button>
			</p>
		</form>
	</div>
	<?php
}

add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'product_page_umiko-demo' === $screen->id ) {
			return;
		}
		$count = count( umiko_seed_product_ids( 'ornek' ) );
		if ( ! $count ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p><strong>umiko:</strong> mağazada %d uydurma örnek ürün var. Yayına almadan önce <a href="%s">örnek ürünleri sil</a>.</p></div>',
			(int) $count,
			esc_url( admin_url( 'edit.php?post_type=product&page=umiko-demo' ) )
		);
	}
);
