<?php
/**
 * Custom typography options for this theme
 *
 * @package WP Shop Woocommerce
 */

function wp_shop_woocommerce_output_custom_font_css() {
    $wp_shop_woocommerce_font_choice = get_theme_mod( 'wp_shop_woocommerce_font_family', 'default' );

    if ( $wp_shop_woocommerce_font_choice === 'default' ) {
        return;
    }

    $wp_shop_woocommerce_font_map = array(
        'bad_script'       => '"Bad Script", cursive',
        'roboto'           => '"Roboto", sans-serif',
        'playfair_display' => '"Playfair Display", serif',
        'open_sans'        => '"Open Sans", sans-serif',
        'lobster'          => '"Lobster", cursive',
        'merriweather'     => '"Merriweather", serif',
        'oswald'           => '"Oswald", sans-serif',
        'raleway'          => '"Raleway", sans-serif',
    );

    $wp_shop_woocommerce_font_family = isset( $wp_shop_woocommerce_font_map[ $wp_shop_woocommerce_font_choice ] ) ? $wp_shop_woocommerce_font_map[ $wp_shop_woocommerce_font_choice ] : $wp_shop_woocommerce_font_map['pt_sans'];

    $wp_shop_woocommerce_custom_css = "
        body,
        h1, h2, h3, h4, h5, h6,
        p, a, span, div,
        .site, .entry-content, .main-navigation, .widget,
        input, textarea, button, .menu, .site-title, .site-description {
            font-family: {$wp_shop_woocommerce_font_family} !important;
        }
    ";

    wp_add_inline_style( 'wp-shop-woocommerce-google-fonts', $wp_shop_woocommerce_custom_css );
}
add_action( 'wp_enqueue_scripts', 'wp_shop_woocommerce_output_custom_font_css', 20 );


function wp_shop_woocommerce_sanitize_font_family( $wp_shop_woocommerce_input ) {
    $wp_shop_woocommerce_valid = array(
        'default', 'bad_script', 'roboto',
        'playfair_display', 'open_sans', 'lobster', 'merriweather', 'oswald', 'raleway'
    );
    return in_array( $wp_shop_woocommerce_input, $wp_shop_woocommerce_valid ) ? $wp_shop_woocommerce_input : 'default';
}

function wp_shop_woocommerce_enqueue_selected_google_font() {
    $wp_shop_woocommerce_font_choice = get_theme_mod( 'wp_shop_woocommerce_font_family', 'default' );

    $wp_shop_woocommerce_font_links = array(
        'bad_script'       => 'https://fonts.googleapis.com/css2?family=Bad+Script&display=swap',
        'roboto'           => 'https://fonts.googleapis.com/css2?family=Roboto&display=swap',
        'playfair_display' => 'https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap',
        'open_sans'        => 'https://fonts.googleapis.com/css2?family=Open+Sans&display=swap',
        'lobster'          => 'https://fonts.googleapis.com/css2?family=Lobster&display=swap',
        'merriweather'     => 'https://fonts.googleapis.com/css2?family=Merriweather&display=swap',
        'oswald'           => 'https://fonts.googleapis.com/css2?family=Oswald&display=swap',
        'raleway'          => 'https://fonts.googleapis.com/css2?family=Raleway&display=swap',
    );

    if ( isset( $wp_shop_woocommerce_font_links[ $wp_shop_woocommerce_font_choice ] ) ) {
        wp_enqueue_style( 'wp-shop-woocommerce-dynamic-font', $wp_shop_woocommerce_font_links[ $wp_shop_woocommerce_font_choice ], array(), null );
    }
}
add_action( 'wp_enqueue_scripts', 'wp_shop_woocommerce_enqueue_selected_google_font' );