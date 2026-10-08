<?php
/**
 * Theme setup and WooCommerce integration.
 *
 * @package Sur_Estudio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sur_estudio_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
		'product_grid'          => array(
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	register_nav_menus( array( 'primary' => __( 'Menú principal', 'sur-estudio' ) ) );
}
add_action( 'after_setup_theme', 'sur_estudio_setup' );

function sur_estudio_assets() {
	wp_enqueue_style( 'sur-estudio-style', get_stylesheet_uri(), array(), '2.2.0' );
	wp_enqueue_style( 'sur-estudio-redesign', get_template_directory_uri() . '/assets/css/redesign.css', array( 'sur-estudio-style' ), '2.2.0' );
	wp_enqueue_script( 'sur-estudio-script', get_template_directory_uri() . '/assets/js/theme.js', array(), '2.2.0', true );
}
add_action( 'wp_enqueue_scripts', 'sur_estudio_assets' );

function sur_estudio_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/#coleccion' );
}

function sur_estudio_product_category_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term && ! is_wp_error( $term ) ) {
		$url = get_term_link( $term );
		if ( ! is_wp_error( $url ) ) {
			return $url;
		}
	}
	return sur_estudio_shop_url();
}

function sur_estudio_cart_url() {
	return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/#coleccion' );
}

function sur_estudio_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
}

function sur_estudio_cart_count_fragment( $fragments ) {
	$fragments['span.bag-count'] = '<span class="bag-count">' . esc_html( sur_estudio_cart_count() ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'sur_estudio_cart_count_fragment' );

function sur_estudio_wrapper_start() {
	echo '<main class="content-page container">';
}
function sur_estudio_wrapper_end() {
	echo '</main>';
}
function sur_estudio_woocommerce_wrappers() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'sur_estudio_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'sur_estudio_wrapper_end', 10 );
}
add_action( 'wp', 'sur_estudio_woocommerce_wrappers' );
