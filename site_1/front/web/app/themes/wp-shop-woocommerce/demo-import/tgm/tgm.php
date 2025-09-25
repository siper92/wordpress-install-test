<?php
require get_template_directory() . '/demo-import/tgm/class-tgm-plugin-activation.php';
/**
 * Recommended plugins.
 */
function wp_shop_woocommerce_register_recommended_plugins_set() {
	$plugins = array(
		array(
			'name'             => __( 'Woocommerce', 'wp-shop-woocommerce' ),
			'slug'             => 'woocommerce',
			'source'           => '',
			'required'         => true,
			'force_activation' => false,
		),
	);
	$wp_shop_woocommerce_config = array();
	tgmpa( $plugins, $wp_shop_woocommerce_config );
}
add_action( 'tgmpa_register', 'wp_shop_woocommerce_register_recommended_plugins_set' );
