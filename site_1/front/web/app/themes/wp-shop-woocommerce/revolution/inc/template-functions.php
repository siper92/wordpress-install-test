<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package WP Shop Woocommerce
 */

function wp_shop_woocommerce_body_classes( $wp_shop_woocommerce_classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$wp_shop_woocommerce_classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$wp_shop_woocommerce_classes[] = 'no-sidebar'; 
	}

	return $wp_shop_woocommerce_classes;
}
add_filter( 'body_class', 'wp_shop_woocommerce_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function wp_shop_woocommerce_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'wp_shop_woocommerce_pingback_header' );
