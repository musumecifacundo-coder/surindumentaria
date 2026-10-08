<?php
/** Run inside WordPress: wp eval-file /absolute/path/to/seed-demo-products.php */
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WC_Product' ) ) {
	throw new RuntimeException( 'WordPress y WooCommerce deben estar activos.' );
}
require_once ABSPATH . 'wp-admin/includes/image.php';

$catalog_path = get_template_directory() . '/assets/catalog/products.json';
$image_path   = get_template_directory() . '/assets/images/';
$items        = json_decode( file_get_contents( $catalog_path ), true );
if ( ! is_array( $items ) || count( $items ) !== 24 ) {
	throw new RuntimeException( 'No se encontró el catálogo completo de 24 productos.' );
}

$categories = array();
foreach ( array( 'coleccion-01' => 'Colección 01', 'sastreria' => 'Sastrería', 'esenciales' => 'Esenciales', 'accesorios' => 'Accesorios' ) as $slug => $name ) {
	$term = term_exists( $slug, 'product_cat' );
	if ( ! $term ) {
		$term = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
	}
	if ( is_wp_error( $term ) ) {
		throw new RuntimeException( $term->get_error_message() );
	}
	$categories[ $slug ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
}

function sur_demo_attach_image( $filename, $product_id, $title, $image_path ) {
	$source = $image_path . $filename;
	if ( ! is_readable( $source ) ) {
		throw new RuntimeException( 'Falta la imagen: ' . $filename );
	}
	$upload = wp_upload_bits( $filename, null, file_get_contents( $source ) );
	if ( ! empty( $upload['error'] ) ) {
		throw new RuntimeException( $upload['error'] );
	}
	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'image/jpeg',
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $upload['file'], $product_id );
	if ( is_wp_error( $attachment_id ) ) {
		throw new RuntimeException( $attachment_id->get_error_message() );
	}
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	return $attachment_id;
}

$legacy_skus = array( 'blazer' => 'SUR-DEMO-BLAZER', 'shirt' => 'SUR-DEMO-SHIRT', 'bag' => 'SUR-DEMO-BAG', 'trousers' => 'SUR-DEMO-TROUSERS' );
foreach ( $items as $position => $item ) {
	$existing_id = wc_get_product_id_by_sku( $item['sku'] );
	if ( ! $existing_id && isset( $legacy_skus[ $item['id'] ] ) ) {
		$existing_id = wc_get_product_id_by_sku( $legacy_skus[ $item['id'] ] );
	}
	if ( $existing_id && get_post_meta( $existing_id, '_sur_demo_catalog_v2', true ) === '1' ) {
		echo 'Ya existe: ' . $item['name'] . PHP_EOL;
		continue;
	}

	$sizes   = $item['sizes'];
	if ( $existing_id ) {
		wp_set_object_terms( $existing_id, $sizes ? 'variable' : 'simple', 'product_type' );
		$product = $sizes ? new WC_Product_Variable( $existing_id ) : new WC_Product_Simple( $existing_id );
	} else {
		$product = $sizes ? new WC_Product_Variable() : new WC_Product_Simple();
		$product->set_sku( $item['sku'] );
	}
	$product->set_name( $item['name'] );
	$product->set_short_description( $item['detail'] );
	$product->set_description( $item['description'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_menu_order( $position );
	if ( $sizes ) {
		$attribute = new WC_Product_Attribute();
		$attribute->set_name( 'Talle' );
		$attribute->set_options( $sizes );
		$attribute->set_visible( true );
		$attribute->set_variation( true );
		$product->set_attributes( array( $attribute ) );
	} else {
		$product->set_regular_price( (string) $item['price'] );
		$product->set_stock_status( 'instock' );
	}
	$product_id = $product->save();
	wp_set_object_terms( $product_id, array( $categories['coleccion-01'], $categories[ $item['category'] ] ), 'product_cat' );
	$product->set_image_id( sur_demo_attach_image( $item['image'], $product_id, $item['name'], $image_path ) );
	$gallery_ids = array();
	foreach ( isset( $item['gallery'] ) ? $item['gallery'] : array() as $gallery_image ) {
		$gallery_ids[] = sur_demo_attach_image( $gallery_image, $product_id, $item['name'] . ' · detalle', $image_path );
	}
	$product->set_gallery_image_ids( $gallery_ids );
	$product->save();

	foreach ( $sizes as $size ) {
		$variation = new WC_Product_Variation();
		$variation->set_parent_id( $product_id );
		$variation->set_attributes( array( 'talle' => $size ) );
		$variation->set_regular_price( (string) $item['price'] );
		$variation->set_stock_status( 'instock' );
		$variation->save();
	}
	if ( $sizes ) {
		WC_Product_Variable::sync( $product_id );
	}
	update_post_meta( $product_id, '_sur_demo_catalog_v2', '1' );
	echo ( $existing_id ? 'Actualizado: ' : 'Creado: ' ) . $item['name'] . PHP_EOL;
}
