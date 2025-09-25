<?php
/**
 * WP Shop Woocommerce functions and definitions
 *
 * @package WP Shop Woocommerce
 */

if ( ! defined( 'WP_SHOP_WOOCOMMERCE_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'WP_SHOP_WOOCOMMERCE_VERSION', '1.0.0' );
}

function wp_shop_woocommerce_setup() {

	load_theme_textdomain( 'wp-shop-woocommerce', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'wp-shop-woocommerce' ),
			'social-menu' => esc_html__('Social Menu', 'wp-shop-woocommerce'),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-background',
		apply_filters(
			'wp_shop_woocommerce_custom_background_args',
			array(
				'default-color' => '#fafafa',
				'default-image' => '',
			)
		)
	);

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_theme_support( 'post-formats', array(
        'image',
        'video',
        'gallery',
        'audio', 
    ));

	if ( ! defined( 'WP_SHOP_WOOCOMMERCE_IMPORT_URL' ) ) {
		define( 'WP_SHOP_WOOCOMMERCE_IMPORT_URL', esc_url( admin_url( 'themes.php?page=wpshopwoocommerce-demoimport' ) ) );
	}
	if ( ! defined( 'WP_SHOP_WOOCOMMERCE_GETSTART_URL' ) ) {
		define( 'WP_SHOP_WOOCOMMERCE_GETSTART_URL', esc_url( admin_url( 'themes.php?page=wp-shop-woocommerce-getstart-page' ) ) );
	}
	if ( ! defined( 'WP_SHOP_WOOCOMMERCE_WELCOME_MESSAGE' ) ) {
		define( 'WP_SHOP_WOOCOMMERCE_WELCOME_MESSAGE', __( 'Welcome to WP Shop Woocommerce', 'wp-shop-woocommerce' ) );
	}
	
}
add_action( 'after_setup_theme', 'wp_shop_woocommerce_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $wp_shop_woocommerce_content_width
 */
function wp_shop_woocommerce_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'wp_shop_woocommerce_content_width', 640 );
}
add_action( 'after_setup_theme', 'wp_shop_woocommerce_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function wp_shop_woocommerce_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'wp-shop-woocommerce' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'wp-shop-woocommerce' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1', 'wp-shop-woocommerce' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Add widgets here.', 'wp-shop-woocommerce' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 2', 'wp-shop-woocommerce' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here.', 'wp-shop-woocommerce' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 3', 'wp-shop-woocommerce' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here.', 'wp-shop-woocommerce' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wp_shop_woocommerce_widgets_init' );


function wp_shop_woocommerce_social_menu()
    {
        if (has_nav_menu('social-menu')) :
            wp_nav_menu(array(
                'theme_location' => 'social-menu',
                'container' => 'ul',
                'menu_class' => 'social-menu menu',
                'menu_id'  => 'menu-social',
            ));
        endif;
    }

/**
 * Enqueue scripts and styles.
 */
function wp_shop_woocommerce_scripts() {

	// Load fonts locally
	require_once get_theme_file_path('revolution/inc/wptt-webfont-loader.php');

	$wp_shop_woocommerce_font_families = array(
		'Caveat Brush',
		'Montserrat:ital,wght@0,100..900;1,100..900',
	);
	
	$wp_shop_woocommerce_fonts_url = add_query_arg( array(
		'family' => implode( '&family=', $wp_shop_woocommerce_font_families ),
		'display' => 'swap',
	), 'https://fonts.googleapis.com/css2' );

	wp_enqueue_style('wp-shop-woocommerce-google-fonts', wptt_get_webfont_url(esc_url_raw($wp_shop_woocommerce_fonts_url)), array(), '1.0.0');
	
	// Font Awesome CSS
    wp_enqueue_style('font-awesome-6', get_template_directory_uri() . '/revolution/assets/vendors/font-awesome-6/css/all.min.css', array(), '6.7.2');

	wp_enqueue_style('owl.carousel.style', get_template_directory_uri() . '/revolution/assets/css/owl.carousel.css', array());
	
	wp_enqueue_style( 'wp-shop-woocommerce-style', get_stylesheet_uri(), array(), WP_SHOP_WOOCOMMERCE_VERSION );

	require get_parent_theme_file_path( '/custom-style.php' );
	wp_add_inline_style( 'wp-shop-woocommerce-style',$wp_shop_woocommerce_custom_css );

	wp_style_add_data('wp-shop-woocommerce-style', 'rtl', 'replace');

	wp_enqueue_script( 'wp-shop-woocommerce-navigation', get_template_directory_uri() . '/js/navigation.js', array(), WP_SHOP_WOOCOMMERCE_VERSION, true );

	wp_enqueue_script( 'owl.carousel.jquery', get_template_directory_uri() . '/revolution/assets/js/owl.carousel.js', array(), WP_SHOP_WOOCOMMERCE_VERSION, true );

	wp_enqueue_script( 'wp-shop-woocommerce-custom-js', get_template_directory_uri() . '/revolution/assets/js/custom.js', array('jquery'), WP_SHOP_WOOCOMMERCE_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_shop_woocommerce_scripts' );

if (!function_exists('wp_shop_woocommerce_related_post')) :
    /**
     * Display related posts from same category
     *
     */

    function wp_shop_woocommerce_related_post($post_id){        
        $wp_shop_woocommerce_categories = get_the_category($post_id);
        if ($wp_shop_woocommerce_categories) {
            $wp_shop_woocommerce_category_ids = array();
            $wp_shop_woocommerce_category = get_category($wp_shop_woocommerce_category_ids);
            $wp_shop_woocommerce_categories = get_the_category($post_id);
            foreach ($wp_shop_woocommerce_categories as $wp_shop_woocommerce_category) {
                $wp_shop_woocommerce_category_ids[] = $wp_shop_woocommerce_category->term_id;
            }
            $wp_shop_woocommerce_count = $wp_shop_woocommerce_category->category_count;
            if ($wp_shop_woocommerce_count > 1) { ?>

         	<?php
		$wp_shop_woocommerce_related_post_wrap = absint(get_theme_mod('wp_shop_woocommerce_enable_related_post', 1));
		if($wp_shop_woocommerce_related_post_wrap == 1){ ?>
                <div class="related-post">
                    
                    <h2 class="post-title"><?php esc_html_e(get_theme_mod('wp_shop_woocommerce_related_post_text', __('Related Post', 'wp-shop-woocommerce'))); ?></h2>
                    <?php
                    $wp_shop_woocommerce_cat_post_args = array(
                        'category__in' => $wp_shop_woocommerce_category_ids,
                        'post__not_in' => array($post_id),
                        'post_type' => 'post',
                        'posts_per_page' =>  get_theme_mod( 'wp_shop_woocommerce_related_post_count', '3' ),
                        'post_status' => 'publish',
						'orderby'           => 'rand',
                        'ignore_sticky_posts' => true
                    );
                    $wp_shop_woocommerce_featured_query = new WP_Query($wp_shop_woocommerce_cat_post_args);
                    ?>
                    <div class="rel-post-wrap">
                        <?php
                        if ($wp_shop_woocommerce_featured_query->have_posts()) :

                        while ($wp_shop_woocommerce_featured_query->have_posts()) : $wp_shop_woocommerce_featured_query->the_post();
                            ?>
							<div class="card-item rel-card-item">
								<div class="card-content">
									<?php if ( has_post_thumbnail() ) { ?>
										<div class="card-media">
											<?php wp_shop_woocommerce_post_thumbnail(); ?>
										</div>
									<?php } else {
										// Fallback default image
										$wp_shop_woocommerce_default_post_thumbnail = get_template_directory_uri() . '/revolution/assets/images/slider1.png';
										echo '<img class="default-post-img" src="' . esc_url( $wp_shop_woocommerce_default_post_thumbnail ) . '" alt="' . esc_attr( get_the_title() ) . '">';
									} ?>
									<div class="entry-title">
										<h3>
											<a href="<?php the_permalink() ?>">
												<?php the_title(); ?>
											</a>
										</h3>
									</div>
									<div class="entry-meta">
										<?php
										wp_shop_woocommerce_posted_on();
										wp_shop_woocommerce_posted_by();
										?>
									</div>
								</div>
							</div>
                        <?php
                        endwhile;
                        ?>
                <?php
                endif;
                wp_reset_postdata();
                ?>
                </div>
                <?php } ?>
                <?php
            }
        }
    }
endif;
add_action('wp_shop_woocommerce_related_posts', 'wp_shop_woocommerce_related_post', 10, 1);

//Excerpt 
function wp_shop_woocommerce_excerpt_function($wp_shop_woocommerce_excerpt_count = 35) {
    $wp_shop_woocommerce_excerpt = get_the_excerpt();
    $wp_shop_woocommerce_text_excerpt = wp_strip_all_tags($wp_shop_woocommerce_excerpt);
    $wp_shop_woocommerce_excerpt_limit = (int) get_theme_mod('wp_shop_woocommerce_excerpt_limit', $wp_shop_woocommerce_excerpt_count);
    $wp_shop_woocommerce_words = preg_split('/\s+/', $wp_shop_woocommerce_text_excerpt); 
    $wp_shop_woocommerce_trimmed_words = array_slice($wp_shop_woocommerce_words, 0, $wp_shop_woocommerce_excerpt_limit);
    $wp_shop_woocommerce_theme_excerpt = implode(' ', $wp_shop_woocommerce_trimmed_words);

    return $wp_shop_woocommerce_theme_excerpt;
}


/**
 * Checkbox sanitization callback example.
 *
 * Sanitization callback for 'checkbox' type controls. This callback sanitizes `$wp_shop_woocommerce_checked`
 * as a boolean value, either TRUE or FALSE.
 */
function wp_shop_woocommerce_sanitize_checkbox($wp_shop_woocommerce_checked)
{
    // Boolean check.
    return ((isset($wp_shop_woocommerce_checked) && true == $wp_shop_woocommerce_checked) ? true : false);
}

function wp_shop_woocommerce_sanitize_choices( $wp_shop_woocommerce_input, $swp_shop_woocommerce_etting ) {
    global $wp_customize; 
    $wp_shop_woocommerce_control = $wp_customize->get_control( $swp_shop_woocommerce_etting->id ); 
    if ( array_key_exists( $wp_shop_woocommerce_input, $wp_shop_woocommerce_control->choices ) ) {
        return $wp_shop_woocommerce_input;
    } else {
        return $swp_shop_woocommerce_etting->default;
    }
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/revolution/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/revolution/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/revolution/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/revolution/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/revolution/inc/jetpack.php';

}

/**
 * Breadcrumb File.
 */
require get_template_directory() . '/revolution/inc/breadcrumbs.php';

/**
 * Custom typography options for this theme.
 */
require get_template_directory() . '/revolution/inc/typography-options.php';

//////////////////////////////////////////////   Function for Translation Error   //////////////////////////////////////////////////////
function wp_shop_woocommerce_enqueue_function() {

	/**
	* GET START.
	*/
	require get_template_directory() . '/getstarted/wp_shop_woocommerce_about_page.php';

	/**
	* DEMO IMPORT.
	*/
	require get_template_directory() . '/demo-import/wp_shop_woocommerce_config_file.php';

	define('WP_SHOP_WOOCOMMERCE_FREE_SUPPORT',__('https://wordpress.org/support/theme/wp-shop-woocommerce/','wp-shop-woocommerce'));
	define('WP_SHOP_WOOCOMMERCE_PRO_SUPPORT',__('https://www.revolutionwp.com/pages/community/','wp-shop-woocommerce'));
	define('WP_SHOP_WOOCOMMERCE_REVIEW',__('https://wordpress.org/support/theme/wp-shop-woocommerce/reviews/#new-post','wp-shop-woocommerce'));
	define('WP_SHOP_WOOCOMMERCE_BUY_NOW',__('https://www.revolutionwp.com/products/woocommerce-wordpress-theme','wp-shop-woocommerce'));
	define('WP_SHOP_WOOCOMMERCE_LIVE_DEMO',__('https://demo.revolutionwp.com/shop-cart-woocommerce-pro/','wp-shop-woocommerce'));
	define('WP_SHOP_WOOCOMMERCE_PRO_DOC',__('https://demo.revolutionwp.com/wpdocs/shop-cart-woocommerce-pro/','wp-shop-woocommerce'));
	define('WP_SHOP_WOOCOMMERCE_LITE_DOC',__('https://demo.revolutionwp.com/wpdocs/shop-cart-woocommerce-free/','wp-shop-woocommerce'));
	
}
add_action( 'after_setup_theme', 'wp_shop_woocommerce_enqueue_function' );

function wp_shop_woocommerce_remove_customize_register() {
    global $wp_customize;

    $wp_customize->remove_setting( 'display_header_text' );
    $wp_customize->remove_control( 'display_header_text' );

}

add_action( 'customize_register', 'wp_shop_woocommerce_remove_customize_register', 11 );

// Add admin notice
function wp_shop_woocommerce_admin_notice() { 
    global $pagenow;
    $wp_shop_woocommerce_theme_args      = wp_get_theme();
    $wp_shop_woocommerce_meta            = get_option( 'wp_shop_woocommerce_admin_notice' );
    $name            = $wp_shop_woocommerce_theme_args->__get( 'Name' );
    $wp_shop_woocommerce_current_screen  = get_current_screen();

    if( !$wp_shop_woocommerce_meta ){
	    if( is_network_admin() ){
	        return;
	    }

	    if( ! current_user_can( 'manage_options' ) ){
	        return;
	    } 
		
		if( $wp_shop_woocommerce_current_screen->base !== 'appearance_page_wp_shop_woocommerce_guide' && 
            $wp_shop_woocommerce_current_screen->base !== 'toplevel_page_wpshopwoocommerce-demoimport' && 
            $wp_shop_woocommerce_current_screen->base !== 'toplevel_page_thestorefrontwoocommerce-demoimport' ) { ?>

            <div class="notice notice-success wp-shop-woocommerce-welcome-notice">
                <p class="wp-shop-woocommerce-dismiss-link">
                    <strong>
                        <a href="<?php echo esc_url( add_query_arg( 'wp_shop_woocommerce_admin_notice', '1' ) ); ?>">
                            <?php esc_html_e( 'Dismiss', 'wp-shop-woocommerce' ); ?>
                        </a>
                    </strong>
                </p>

                <div class="wp-shop-woocommerce-welcome-notice-wrap">
                    <h2 class="wp-shop-woocommerce-notice-title">
                        <span class="dashicons dashicons-admin-home"></span> 
                        <?php 
                            $wp_shop_woocommerce_theme_name = wp_get_theme()->get( 'Name' );
                            /* translators: %s!: Theme Name. */
                            echo esc_html( sprintf( __( 'Welcome to the free theme: %s!', 'wp-shop-woocommerce' ), $wp_shop_woocommerce_theme_name ) );
                        ?>
                    </h2>
                    <p class="wp-shop-woocommerce-notice-desc">
                        <?php esc_html_e( 'Get started by exploring the features of your new theme. Customize your design, add your content, and create a site that fits your vision.', 'wp-shop-woocommerce' ); ?>
                    </p>

                    <div class="wp-shop-woocommerce-welcome-info">
                        <div class="wp-shop-woocommerce-welcome-thumb">
                            <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/screenshot.png' ); ?>" alt="<?php esc_attr_e( 'Theme Screenshot', 'wp-shop-woocommerce' ); ?>">
                        </div>

                        <div class="wp-shop-woocommerce-welcome-import">
                            <h3><span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Quick Start: Import Demo', 'wp-shop-woocommerce' ); ?></h3>
                            <p><?php esc_html_e( 'Use the Demo Importer to quickly set up your site with a pre-made layout. Get a complete site in minutes.', 'wp-shop-woocommerce' ); ?></p>
                            <p><a class="button info-link button-primary" href="<?php echo esc_url( WP_SHOP_WOOCOMMERCE_IMPORT_URL ); ?>"><?php esc_html_e( 'Go to Demo Importer', 'wp-shop-woocommerce' ); ?></a></p>
                        </div>

                        <div class="wp-shop-woocommerce-welcome-getting-started">
                            <h3><span class="dashicons dashicons-art"></span> <?php esc_html_e( 'Customize Your Theme', 'wp-shop-woocommerce' ); ?></h3>
                            <p><?php esc_html_e( 'Want to make it truly yours? Explore the Getting Started Guide to personalize your site to suit your needs.', 'wp-shop-woocommerce' ); ?></p>
                            <p><a class="info-link button" href="<?php echo esc_url( WP_SHOP_WOOCOMMERCE_GETSTART_URL ); ?>"><?php esc_html_e( 'View Getting Started Guide', 'wp-shop-woocommerce' ); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }

	}
}

add_action( 'admin_notices', 'wp_shop_woocommerce_admin_notice' );

if( ! function_exists( 'wp_shop_woocommerce_update_admin_notice' ) ) :
/**
 * Updating admin notice on dismiss
*/
function wp_shop_woocommerce_update_admin_notice(){
    if ( isset( $_GET['wp_shop_woocommerce_admin_notice'] ) && $_GET['wp_shop_woocommerce_admin_notice'] = '1' ) {
        update_option( 'wp_shop_woocommerce_admin_notice', true );
    }
}
endif;
add_action( 'admin_init', 'wp_shop_woocommerce_update_admin_notice' );


add_action('after_switch_theme', 'wp_shop_woocommerce_setup_options');
function wp_shop_woocommerce_setup_options () {
    update_option('wp_shop_woocommerce_admin_notice', FALSE );
}

/**
 * WooCommerce custom filters
 */
add_filter('loop_shop_columns', 'wp_shop_woocommerce_loop_columns');

if (!function_exists('wp_shop_woocommerce_loop_columns')) {

	function wp_shop_woocommerce_loop_columns() {

		$wp_shop_woocommerce_columns = get_theme_mod( 'wp_shop_woocommerce_per_columns', 3 );

		return $wp_shop_woocommerce_columns;
	}
}

/************************************************************************************/

add_filter( 'loop_shop_per_page', 'wp_shop_woocommerce_per_page', 20 );

function wp_shop_woocommerce_per_page( $wp_shop_woocommerce_cols ) {

  	$wp_shop_woocommerce_cols = get_theme_mod( 'wp_shop_woocommerce_product_per_page', 9 );

	return $wp_shop_woocommerce_cols;
}

/************************************************************************************/

add_filter( 'woocommerce_output_related_products_args', 'wp_shop_woocommerce_products_args' );

function wp_shop_woocommerce_products_args( $wp_shop_woocommerce_args ) {

    $wp_shop_woocommerce_args['posts_per_page'] = get_theme_mod( 'custom_related_products_number', 6 );

    $wp_shop_woocommerce_args['columns'] = get_theme_mod( 'custom_related_products_number_per_row', 3 );

    return $wp_shop_woocommerce_args;
}

/************************************************************************************/


/**
 * Custom logo
 */

function wp_shop_woocommerce_custom_css() {
?>
	<style type="text/css" id="custom-theme-colors" >
        :root {
           
            --wp_shop_woocommerce_logo_width: <?php echo absint(get_theme_mod('wp_shop_woocommerce_logo_width')); ?> ;   
        }
        .main-header .site-branding {
            max-width:<?php echo esc_html(get_theme_mod('wp_shop_woocommerce_logo_width')); ?>px ;    
        }         
	</style>
<?php
}
add_action( 'wp_head', 'wp_shop_woocommerce_custom_css' );

function get_changelog_from_readme() {
	$wp_shop_woocommerce_file_path = get_template_directory() . '/readme.txt'; // Adjust path if necessary

	if (file_exists($wp_shop_woocommerce_file_path)) {
		$wp_shop_woocommerce_content = file_get_contents($wp_shop_woocommerce_file_path);

		// Extract changelog section
		$wp_shop_woocommerce_changelog_start = strpos($wp_shop_woocommerce_content, "== Changelog ==");
		$wp_shop_woocommerce_changelog = substr($wp_shop_woocommerce_content, $wp_shop_woocommerce_changelog_start);

		// Split changelog into versions
		preg_match_all('/\*\s([\d\.]+)\s-\s(.+?)\n((?:\t-\s.+?\n)+)/', $wp_shop_woocommerce_changelog, $wp_shop_woocommerce_matches, PREG_SET_ORDER);
		
		return $wp_shop_woocommerce_matches;
	}
	return [];
}

add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );