<?php
/**
 * WP Shop Woocommerce Theme Customizer
 *
 * @package WP Shop Woocommerce
 */

function wp_shop_woocommerce_customize_register( $wp_customize ) {
	load_template( trailingslashit( get_template_directory() ) . 'revolution/inc/fontawesome-change.php' );

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'wp_shop_woocommerce_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'wp_shop_woocommerce_customize_partial_blogdescription',
			)
		);
	}

		/* WooCommerce custom settings */

	$wp_customize->add_section('woocommerce_custom_settings', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('WooCommerce Custom Settings', 'wp-shop-woocommerce'),
		'panel'       => 'woocommerce',
	));

	$wp_customize->add_setting(
		'wp_shop_woocommerce_per_columns',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '3',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_per_columns',
		array(
			'label'       => __('Product Per Single Row', 'wp-shop-woocommerce'),
			'section'     => 'woocommerce_custom_settings',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 1,
	             'max' => 4,
	             'step' => 1,
	         ),
		)
	);

	$wp_customize->add_setting(
		'wp_shop_woocommerce_product_per_page',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '6',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_product_per_page',
		array(
			'label'       => __('Product Per One Page', 'wp-shop-woocommerce'),
			'section'     => 'woocommerce_custom_settings',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 1,
	             'max' => 12,
	             'step' => 1,
	         ),
		)
	);

	/*Related Products Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_related_product',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_related_product',
		array(
			'label'       => __('Enable Related Product', 'wp-shop-woocommerce'),
			'description' => __('Checked to show Related Product', 'wp-shop-woocommerce'),
			'section'     => 'woocommerce_custom_settings',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'custom_related_products_number',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '3',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'custom_related_products_number',
		array(
			'label'       => __('Related Product Count', 'wp-shop-woocommerce'),
			'section'     => 'woocommerce_custom_settings',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 1,
	             'max' => 20,
	             'step' => 1,
	         ),
		)
	);

	$wp_customize->add_setting(
		'custom_related_products_number_per_row',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '3',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'custom_related_products_number_per_row',
		array(
			'label'       => __('Related Product Per Row', 'wp-shop-woocommerce'),
			'section'     => 'woocommerce_custom_settings',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 1,
	             'max' => 4,
	             'step' => 1,
	         ),
		)
	);

	/*Archive Product layout*/
	$wp_customize->add_setting('wp_shop_woocommerce_archive_product_layout',array(
        'default' => 'layout-1',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
	));
	$wp_customize->add_control('wp_shop_woocommerce_archive_product_layout',array(
        'type' => 'select',
        'label' => esc_html__('Archive Product Layout','wp-shop-woocommerce'),
        'section' => 'woocommerce_custom_settings',
        'choices' => array(
            'layout-1' => esc_html__('Sidebar On Right','wp-shop-woocommerce'),
            'layout-2' => esc_html__('Sidebar On Left','wp-shop-woocommerce'),
			'layout-3' => esc_html__('Full Width Layout','wp-shop-woocommerce')
        ),
	) );

	/*Single Product layout*/
	$wp_customize->add_setting('wp_shop_woocommerce_single_product_layout',array(
        'default' => 'layout-1',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
	));
	$wp_customize->add_control('wp_shop_woocommerce_single_product_layout',array(
        'type' => 'select',
        'label' => esc_html__('Single Product Layout','wp-shop-woocommerce'),
        'section' => 'woocommerce_custom_settings',
        'choices' => array(
            'layout-1' => esc_html__('Sidebar On Right','wp-shop-woocommerce'),
            'layout-2' => esc_html__('Sidebar On Left','wp-shop-woocommerce'),
			'layout-3' => esc_html__('Full Width Layout','wp-shop-woocommerce')
        ),
	) );

	$wp_customize->add_setting('wp_shop_woocommerce_woocommerce_product_sale',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
        'default'           => 'Right',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
    ));
    $wp_customize->add_control('wp_shop_woocommerce_woocommerce_product_sale',array(
        'label'       => esc_html__( 'Woocommerce Product Sale Positions','wp-shop-woocommerce' ),
        'type' => 'select',
        'section' => 'woocommerce_custom_settings',
        'choices' => array(
            'Right' => __('Right','wp-shop-woocommerce'),
            'Left' => __('Left','wp-shop-woocommerce'),
            'Center' => __('Center','wp-shop-woocommerce')
        ),
    ) );

	/*
    * Theme Options Panel
    */
	$wp_customize->add_panel('wp_shop_woocommerce_panel', array(
		'priority' => 25,
		'capability' => 'edit_theme_options',
		'title' => __('Shop Woocommerce Theme Options', 'wp-shop-woocommerce'),
	));

	/*Additional Options*/
	$wp_customize->add_section('wp_shop_woocommerce_additional_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Additional Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	/*Main Slider Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_sticky_header',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => false,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_sticky_header',
		array(
			'label'       => __('Enable Sticky Header', 'wp-shop-woocommerce'),
			'description' => __('Checked to enable sticky header', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_additional_section',
			'type'        => 'checkbox',
		)
	);

	/*Main Slider Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_preloader',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 0,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_preloader',
		array(
			'label'       => __('Enable Preloader', 'wp-shop-woocommerce'),
			'description' => __('Checked to show preloader', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_additional_section',
			'type'        => 'checkbox',
		)
	);

	/*Breadcrumbs Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_breadcrumbs',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_breadcrumbs',
		array(
			'label'       => __('Enable Breadcrumbs', 'wp-shop-woocommerce'),
			'description' => __('Checked to show Breadcrumbs', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_additional_section',
			'type'        => 'checkbox',
		)
	);

	/*Post layout*/
	$wp_customize->add_setting('wp_shop_woocommerce_archive_layout',array(
        'default' => 'layout-1',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
	));
	$wp_customize->add_control('wp_shop_woocommerce_archive_layout',array(
        'type' => 'select',
        'label' => esc_html__('Post Layout','wp-shop-woocommerce'),
        'section' => 'wp_shop_woocommerce_additional_section',
        'choices' => array(
            'layout-1' => esc_html__('Sidebar On Right','wp-shop-woocommerce'),
            'layout-2' => esc_html__('Sidebar On Left','wp-shop-woocommerce'),
			'layout-3' => esc_html__('Full Width Layout','wp-shop-woocommerce')
        ),
	) );

	/*single post layout*/
	$wp_customize->add_setting('wp_shop_woocommerce_post_layout',array(
        'default' => 'layout-1',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
	));
	$wp_customize->add_control('wp_shop_woocommerce_post_layout',array(
        'type' => 'select',
        'label' => esc_html__('Single Post Layout','wp-shop-woocommerce'),
        'section' => 'wp_shop_woocommerce_additional_section',
        'choices' => array(
            'layout-1' => esc_html__('Sidebar On Right','wp-shop-woocommerce'),
            'layout-2' => esc_html__('Sidebar On Left','wp-shop-woocommerce'),
			'layout-3' => esc_html__('Full Width Layout','wp-shop-woocommerce')
        ),
	) );

	/*single page layout*/
	$wp_customize->add_setting('wp_shop_woocommerce_page_layout',array(
        'default' => 'layout-1',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
	));
	$wp_customize->add_control('wp_shop_woocommerce_page_layout',array(
        'type' => 'select',
        'label' => esc_html__('Single Page Layout','wp-shop-woocommerce'),
        'section' => 'wp_shop_woocommerce_additional_section',
        'choices' => array(
            'layout-1' => esc_html__('Sidebar On Right','wp-shop-woocommerce'),
            'layout-2' => esc_html__('Sidebar On Left','wp-shop-woocommerce'),
			'layout-3' => esc_html__('Full Width Layout','wp-shop-woocommerce')
        ),
	) );

		/*Archive Post Options*/
	$wp_customize->add_section('wp_shop_woocommerce_blog_post_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Blog Page Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_blog_post_title',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_blog_post_title',array(
		'label'       => __('Enable Blog Post Title', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Blog Post Title', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_blog_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_blog_post_meta',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_blog_post_meta',array(
		'label'       => __('Enable Blog Post Meta', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Blog Post Meta Feilds', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_blog_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_blog_post_tags',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_blog_post_tags',array(
		'label'       => __('Enable Blog Post Tags', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Blog Post Tags', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_blog_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_blog_post_image',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_blog_post_image',array(
		'label'       => __('Enable Blog Post Image', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Blog Post Image', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_blog_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_blog_post_content',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_blog_post_content',array(
		'label'       => __('Enable Blog Post Content', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Blog Post Content', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_blog_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_blog_post_button',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_blog_post_button',array(
		'label'       => __('Enable Blog Post Read More Button', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Blog Post Read More Button', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_blog_post_section',
		'type'        => 'checkbox',
	));

	/*Blog post Content layout*/
	$wp_customize->add_setting('wp_shop_woocommerce_blog_Post_content_layout',array(
        'default' => 'Left',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
	));
	$wp_customize->add_control('wp_shop_woocommerce_blog_Post_content_layout',array(
        'type' => 'select',
        'label' => esc_html__('Blog Post Content Layout','wp-shop-woocommerce'),
        'section' => 'wp_shop_woocommerce_blog_post_section',
        'choices' => array(
            'Left' => esc_html__('Left','wp-shop-woocommerce'),
            'Center' => esc_html__('Center','wp-shop-woocommerce'),
            'Right' => esc_html__('Right','wp-shop-woocommerce')
        ),
	) );

	/*Excerpt*/
    $wp_customize->add_setting(
		'wp_shop_woocommerce_excerpt_limit',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '25',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_excerpt_limit',
		array(
			'label'       => __('Excerpt Limit', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_blog_post_section',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 2,
	             'max' => 50,
	             'step' => 2,
	         ),
		)
	);

	/*Archive Button Text*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_read_more_text',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 'Continue Reading....',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_read_more_text',
		array(
			'label'       => __('Edit Button Text ', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_blog_post_section',
			'type'        => 'text',
		)
	);

	/*Single Post Options*/
	$wp_customize->add_section('wp_shop_woocommerce_single_post_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Single Post Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_single_blog_post_title',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_single_blog_post_title',array(
		'label'       => __('Enable Single Post Title', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Single Blog Post Title', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_single_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_single_blog_post_meta',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_single_blog_post_meta',array(
		'label'       => __('Enable Single Post Meta', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Single Blog Post Meta Feilds', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_single_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_single_blog_post_tags',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_single_blog_post_tags',array(
		'label'       => __('Enable Single Post Tags', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Single Blog Post Tags', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_single_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_single_post_image',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_single_post_image',array(
		'label'       => __('Enable Single Post Image', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Single Post Image', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_single_post_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('wp_shop_woocommerce_enable_single_blog_post_content',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 1,
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
	));
	$wp_customize->add_control('wp_shop_woocommerce_enable_single_blog_post_content',array(
		'label'       => __('Enable Single Post Content', 'wp-shop-woocommerce'),
		'description' => __('Checked To Show Single Blog Post Content', 'wp-shop-woocommerce'),
		'section'     => 'wp_shop_woocommerce_single_post_section',
		'type'        => 'checkbox',
	));

	/*Related Post Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_related_post',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_related_post',
		array(
			'label'       => __('Enable Related Post', 'wp-shop-woocommerce'),
			'description' => __('Checked to show Related Post', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_single_post_section',
			'type'        => 'checkbox',
		)
	);

	/*Related post Edit Text*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_related_post_text',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 'Related Post',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_related_post_text',
		array(
			'label'       => __('Edit Related Post Text ', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_single_post_section',
			'type'        => 'text',
		)
	);	

	/*Related Post Per Page*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_related_post_count',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '3',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_related_post_count',
		array(
			'label'       => __('Related Post Count', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_single_post_section',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 1,
	             'max' => 9,
	             'step' => 1,
	         ),
		)
	);

	/*
	* Customizer Global COlor
	*/

	/*Global Color Options*/
	$wp_customize->add_section('wp_shop_woocommerce_global_color_section', array(
		'priority'       => 1,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Global Color Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	$wp_customize->add_setting( 'wp_shop_woocommerce_primary_color',
		array(
		'default'           => '#59A2FF',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
		$wp_customize, 
		'wp_shop_woocommerce_primary_color',
		array(
			'label'      => esc_html__( 'Primary Color', 'wp-shop-woocommerce' ),
			'section'    => 'wp_shop_woocommerce_global_color_section',
			'settings'   => 'wp_shop_woocommerce_primary_color',
		) ) 
	);

	$wp_customize->add_setting( 'wp_shop_woocommerce_secondary_color',
		array(
		'default'           => '#26242D',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control( 
		new WP_Customize_Color_Control( 
		$wp_customize, 
		'wp_shop_woocommerce_secondary_color',
		array(
			'label'      => esc_html__( 'Secondary Color', 'wp-shop-woocommerce' ),
			'section'    => 'wp_shop_woocommerce_global_color_section',
			'settings'   => 'wp_shop_woocommerce_secondary_color',
		) ) 
	);


	/*
	* Customizer top header section
	*/

	$wp_customize->add_setting(
		'wp_shop_woocommerce_site_title_text',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_site_title_text',
		array(
			'label'       => __('Enable Title', 'wp-shop-woocommerce'),
			'description' => __('Enable or Disable Title from the site', 'wp-shop-woocommerce'),
			'section'     => 'title_tagline',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'wp_shop_woocommerce_site_tagline_text',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 0,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_site_tagline_text',
		array(
			'label'       => __('Enable Tagline', 'wp-shop-woocommerce'),
			'description' => __('Enable or Disable Tagline from the site', 'wp-shop-woocommerce'),
			'section'     => 'title_tagline',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'wp_shop_woocommerce_logo_width',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '150',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_logo_width',
		array(
			'label'       => __('Logo Width in PX', 'wp-shop-woocommerce'),
			'section'     => 'title_tagline',
			'type'        => 'number',
			'input_attrs' => array(
	            'min' => 100,
	             'max' => 300,
	             'step' => 1,
	         ),
		)
	);

/*Typography Options*/
	$wp_customize->add_section( 'wp_shop_woocommerce_typography_section', array(
		'panel'       => 'wp_shop_woocommerce_panel',
        'title'    => __( 'Typography Options', 'wp-shop-woocommerce' ),
        'priority' => 2,
    ) );

    $wp_customize->add_setting( 'wp_shop_woocommerce_font_family', array(
		'default'           => 'default',
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_font_family',
	) );
	
	$wp_customize->add_control( 'wp_shop_woocommerce_font_family', array(
		'label'    => __( 'Global Font Family', 'wp-shop-woocommerce' ),
		'section'  => 'wp_shop_woocommerce_typography_section',
		'type'     => 'select',
		'choices'  => array(
			'default'          => __( 'Default (Theme Font)', 'wp-shop-woocommerce' ),
			'bad_script'       => 'Bad Script',
			'roboto'           => 'Roboto',
			'playfair_display' => 'Playfair Display',
			'open_sans'        => 'Open Sans',
			'lobster'          => 'Lobster',
			'merriweather'     => 'Merriweather',
			'oswald'           => 'Oswald',
			'raleway'          => 'Raleway',
		),
	) );
	/*Top Header Options*/
	$wp_customize->add_section('wp_shop_woocommerce_topbar_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Top Header Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));


	/*Top Header Phone Text*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_header_info_phone',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '+123 456 7890',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_header_info_phone',
		array(
			'label'       => __('Edit Phone No ', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_topbar_section',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting('wp_shop_woocommerce_header_phone_icon',array(
		'default'	=> 'fas fa-phone-alt',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new WP_Shop_Woocommerce_Icon_Changer(
        $wp_customize,'wp_shop_woocommerce_header_phone_icon',array(
		'label'	=> __('Phone Number Icon','wp-shop-woocommerce'),
		'transport' => 'refresh',
		'section'	=> 'wp_shop_woocommerce_topbar_section',
		'type'		=> 'icon'
	)));

	/*Top Header Phone Text*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_header_info_email',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 'support@example.com',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_header_info_email',
		array(
			'label'       => __('Edit Email Address ', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_topbar_section',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting('wp_shop_woocommerce_header_mail_icon',array(
		'default'	=> 'fas fa-envelope',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(new WP_Shop_Woocommerce_Icon_Changer(
        $wp_customize,'wp_shop_woocommerce_header_mail_icon',array(
		'label'	=> __('Mail Icon','wp-shop-woocommerce'),
		'transport' => 'refresh',
		'section'	=> 'wp_shop_woocommerce_topbar_section',
		'type'		=> 'icon'
	)));

	/*Top Header Text*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_header_topbar_text',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 'FREE SHIPPING on orders over $99. This offer is valid on all store items.',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_header_topbar_text',
		array(
			'label'       => __('Edit Header Text ', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_topbar_section',
			'type'        => 'text',
		)
	);

	/*
	* Customizer main header section
	*/

	/*
	* Customizer main slider section
	*/
	/*Main Slider Options*/
	$wp_customize->add_section('wp_shop_woocommerce_slider_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Main Slider Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	/*Main Slider Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_slider',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 0,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_slider',
		array(
			'label'       => __('Enable Main Slider', 'wp-shop-woocommerce'),
			'description' => __('Checked to show the main slider', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_slider_section',
			'type'        => 'checkbox',
		)
	);

	for ($wp_shop_woocommerce_i=1; $wp_shop_woocommerce_i <= 3; $wp_shop_woocommerce_i++) { 

		/*Main Slider Image*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_slider_image'.$wp_shop_woocommerce_i,
			array(
				'capability'    => 'edit_theme_options',
		        'default'       => '',
		        'transport'     => 'postMessage',
		        'sanitize_callback' => 'esc_url_raw',
	    	)
	    );

		$wp_customize->add_control( 
			new WP_Customize_Image_Control( $wp_customize, 
				'wp_shop_woocommerce_slider_image'.$wp_shop_woocommerce_i, 
				array(
			        'label' => __('Edit Slider Image ', 'wp-shop-woocommerce') .$wp_shop_woocommerce_i,
			        'description' => __('Edit the slider image.', 'wp-shop-woocommerce'),
			        'section' => 'wp_shop_woocommerce_slider_section',
				)
			)
		);

		/*Main extra Slider Heading*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_slider_xtra_heading'.$wp_shop_woocommerce_i,
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_slider_xtra_heading'.$wp_shop_woocommerce_i,
			array(
				'label'       => __('Edit Extra Heading Text ', 'wp-shop-woocommerce') .$wp_shop_woocommerce_i,
				'description' => __('Edit the slider Extra heading text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_slider_section',
				'type'        => 'text',
			)
		);

		/*Main Slider Heading*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_slider_heading'.$wp_shop_woocommerce_i,
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_slider_heading'.$wp_shop_woocommerce_i,
			array(
				'label'       => __('Edit Heading Text ', 'wp-shop-woocommerce') .$wp_shop_woocommerce_i,
				'description' => __('Edit the slider heading text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_slider_section',
				'type'        => 'text',
			)
		);

		/*Main Slider Content*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_slider_text'.$wp_shop_woocommerce_i,
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_slider_text'.$wp_shop_woocommerce_i,
			array(
				'label'       => __('Edit Content Text ', 'wp-shop-woocommerce') .$wp_shop_woocommerce_i,
				'description' => __('Edit the slider content text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_slider_section',
				'type'        => 'text',
			)
		);

		/*Main Slider Button1 Text*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_slider_button1_text'.$wp_shop_woocommerce_i,
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_slider_button1_text'.$wp_shop_woocommerce_i,
			array(
				'label'       => __('Edit Button #1 Text ', 'wp-shop-woocommerce') .$wp_shop_woocommerce_i,
				'description' => __('Edit the slider button text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_slider_section',
				'type'        => 'text',
			)
		);

		/*Main Slider Button1 URL*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_slider_button1_link'.$wp_shop_woocommerce_i,
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_slider_button1_link'.$wp_shop_woocommerce_i,
			array(
				'label'       => __('Edit Button #1 URL ', 'wp-shop-woocommerce') .$wp_shop_woocommerce_i,
				'description' => __('Edit the slider button url.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_slider_section',
				'type'        => 'url',
			)
		);

	}

	/*
	* Customizer About Us section
	*/
	/*About Us Options*/
	$wp_customize->add_section('wp_shop_woocommerce_product_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Product Category Option', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	/*Product Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_product',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 0,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_product',
		array(
			'label'       => __('Enable Product Section', 'wp-shop-woocommerce'),
			'description' => __('Select the category from dropdown', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_product_section',
			'type'        => 'checkbox',
		)
	);

	/*Portfolio Image*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_category_image',
			array(
				'capability'    => 'edit_theme_options',
		        'default'       => '',
		        'transport'     => 'postMessage',
		        'sanitize_callback' => 'esc_url_raw',
	    	)
	    );

		$wp_customize->add_control( 
			new WP_Customize_Image_Control( $wp_customize, 
				'wp_shop_woocommerce_category_image', 
				array(
			        'label' => __('Edit Portfolio Image ', 'wp-shop-woocommerce') ,
			        'description' => __('Edit the category image.', 'wp-shop-woocommerce'),
			        'section' => 'wp_shop_woocommerce_product_section',
				)
			)
		);

		/*Portfolio Heading*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_product_sale_heading',
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_product_sale_heading',
			array(
				'label'       => __('Edit Sale Heading', 'wp-shop-woocommerce') ,
				'description' => __('Edit Product Sale text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_product_section',
				'type'        => 'text',
			)
		);

		/*Portfolio Heading*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_product_discount_text',
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_product_discount_text',
			array(
				'label'       => __('Edit Discount Text', 'wp-shop-woocommerce') ,
				'description' => __('Edit Product Discount text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_product_section',
				'type'        => 'text',
			)
		);

		/*Portfolio Content*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_product_heading_text',
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_product_heading_text',
			array(
				'label'       => __('Edit Heading', 'wp-shop-woocommerce') ,
				'description' => __('Edit product heading text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_product_section',
				'type'        => 'text',
			)
		);

		/*Portfolio Content*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_product_sub_heading_text',
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_product_sub_heading_text',
			array(
				'label'       => __('Edit Sub Heading', 'wp-shop-woocommerce') ,
				'description' => __('Edit Product heading text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_product_section',
				'type'        => 'text',
			)
		);

		/*Portfolio Button*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_category_button1_text',
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_category_button1_text',
			array(
				'label'       => __('Edit Button Text', 'wp-shop-woocommerce') ,
				'description' => __('Edit portfolio button text.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_product_section',
				'type'        => 'text',
			)
		);

		/*Portfolio Button Link*/
		$wp_customize->add_setting(
			'wp_shop_woocommerce_category_button1_link',
			array(
				'capability'        => 'edit_theme_options',
				'transport'         => 'refresh',
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			'wp_shop_woocommerce_category_button1_link',
			array(
				'label'       => __('Edit Button Link ', 'wp-shop-woocommerce') ,
				'description' => __('Edit portfolio button link.', 'wp-shop-woocommerce'),
				'section'     => 'wp_shop_woocommerce_product_section',
				'type'        => 'url',
			)
		);

	/*Event Heading*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_event_heading',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_event_heading',
		array(
			'label'       => __('Edit Section Heading', 'wp-shop-woocommerce'),
			'description' => __('Edit product section heading', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_product_section',
			'type'        => 'text',
		)
	);

	$wp_shop_woocommerce_args = array(
       'type'      => 'product',
        'taxonomy' => 'product_cat'
    );
	$categories = get_categories($wp_shop_woocommerce_args);
		$wp_shop_woocommerce_cat_posts = array();
			$wp_shop_woocommerce_i = 0;
			$wp_shop_woocommerce_cat_posts[]='Select';
		foreach($categories as $wp_shop_woocommerce_category){
			if($wp_shop_woocommerce_i==0){
			$wp_shop_woocommerce_default = $wp_shop_woocommerce_category->slug;
			$wp_shop_woocommerce_i++;
		}
		$wp_shop_woocommerce_cat_posts[$wp_shop_woocommerce_category->slug] = $wp_shop_woocommerce_category->name;
	}

	$wp_customize->add_setting('wp_shop_woocommerce_best_product_category',array(
		'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices',
	));
	$wp_customize->add_control('wp_shop_woocommerce_best_product_category',array(
		'type'    => 'select',
		'choices' => $wp_shop_woocommerce_cat_posts,
		'label' => __('Select Product Category','wp-shop-woocommerce'),
		'section' => 'wp_shop_woocommerce_product_section',
	));

	/*
	* Customizer Footer Section
	*/
	/*Footer Options*/
	$wp_customize->add_section('wp_shop_woocommerce_footer_section', array(
		'priority'       => 5,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __('Footer Options', 'wp-shop-woocommerce'),
		'panel'       => 'wp_shop_woocommerce_panel',
	));

	/*Footer Enable Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_footer',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_footer',
		array(
			'label'       => __('Enable Footer', 'wp-shop-woocommerce'),
			'description' => __('Checked to show Footer', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_footer_section',
			'type'        => 'checkbox',
		)
	);

	/*Footer bg image Option*/
	$wp_customize->add_setting('wp_shop_woocommerce_footer_bg_image',array(
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize,'wp_shop_woocommerce_footer_bg_image',array(
        'label' => __('Footer Background Image','wp-shop-woocommerce'),
        'section' => 'wp_shop_woocommerce_footer_section',
        'priority' => 1,
    )));

	/*Footer Social Menu Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_footer_social_menu',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'wp_shop_woocommerce_footer_social_menu',
		array(
			'label'       => __('Enable Footer Social Menu', 'wp-shop-woocommerce'),
			'description' => __('Checked to show the footer social menu. Go to Dashboard >> Appearance >> Menus >> Create New Menu >> Add Custom Link >> Add Social Menu >> Checked Social Menu >> Save Menu.', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_footer_section',
			'type'        => 'checkbox',
		)
	);	

	/*Go To Top Option*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_enable_go_to_top_option',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => 1,
			'sanitize_callback' => 'wp_shop_woocommerce_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'wp_shop_woocommerce_enable_go_to_top_option',
		array(
			'label'       => __('Enable Go To Top', 'wp-shop-woocommerce'),
			'description' => __('Checked to enable Go To Top option.', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_footer_section',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting('wp_shop_woocommerce_go_to_top_position',array(
        'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
		'default'           => 'Right',
        'sanitize_callback' => 'wp_shop_woocommerce_sanitize_choices'
    ));
    $wp_customize->add_control('wp_shop_woocommerce_go_to_top_position',array(
        'type' => 'select',
        'section' => 'wp_shop_woocommerce_footer_section',
        'label' => esc_html__('Go To Top Positions','wp-shop-woocommerce'),
        'choices' => array(
            'Right' => __('Right','wp-shop-woocommerce'),
            'Left' => __('Left','wp-shop-woocommerce'),
            'Center' => __('Center','wp-shop-woocommerce')
        ),
    ) );

	/*Footer Copyright Text Enable*/
	$wp_customize->add_setting(
		'wp_shop_woocommerce_copyright_option',
		array(
			'capability'        => 'edit_theme_options',
			'transport'         => 'refresh',
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'wp_shop_woocommerce_copyright_option',
		array(
			'label'       => __('Edit Copyright Text', 'wp-shop-woocommerce'),
			'description' => __('Edit the Footer Copyright Section.', 'wp-shop-woocommerce'),
			'section'     => 'wp_shop_woocommerce_footer_section',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'wp_shop_woocommerce_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function wp_shop_woocommerce_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function wp_shop_woocommerce_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function wp_shop_woocommerce_customize_preview_js() {
	wp_enqueue_script( 'wp-shop-woocommerce-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), WP_SHOP_WOOCOMMERCE_VERSION, true );
}
add_action( 'customize_preview_init', 'wp_shop_woocommerce_customize_preview_js' );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class WP_Shop_Woocommerce_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $wp_shop_woocommerce_instance = null;

		if ( is_null( $wp_shop_woocommerce_instance ) ) {
			$wp_shop_woocommerce_instance = new self;
			$wp_shop_woocommerce_instance->setup_actions();
		}

		return $wp_shop_woocommerce_instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $wp_shop_woocommerce_manager
	 * @return void
	*/
	public function sections( $wp_shop_woocommerce_manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/revolution/inc/section-pro.php' );

		// Register custom section types.
		$wp_shop_woocommerce_manager->register_section_type( 'WP_Shop_Woocommerce_Customize_Section_Pro' );

		// Register sections.
		$wp_shop_woocommerce_manager->add_section( new WP_Shop_Woocommerce_Customize_Section_Pro( $wp_shop_woocommerce_manager,'wp_shop_woocommerce_go_pro', array(
			'priority'   => 1,
			'title'    => esc_html__( 'WP Shop Woocommerce', 'wp-shop-woocommerce' ),
			'pro_text' => esc_html__( 'Buy Pro', 'wp-shop-woocommerce' ),
			'pro_url'  => esc_url('https://www.revolutionwp.com/products/woocommerce-wordpress-theme'),
		) )	);

				// Register sections.
		$wp_shop_woocommerce_manager->add_section( new WP_Shop_Woocommerce_Customize_Section_Pro( $wp_shop_woocommerce_manager,'wp_shop_woocommerce_go_pro', array(
			'priority'   => 1,
			'title'    => esc_html__( 'WP Shop Woocommerce', 'wp-shop-woocommerce' ),
			'pro_text' => esc_html__( 'Buy Pro', 'wp-shop-woocommerce' ),
			'pro_url'    => esc_url( WP_SHOP_WOOCOMMERCE_BUY_NOW ),
		) )	);

		// Register sections.
		$wp_shop_woocommerce_manager->add_section( new WP_Shop_Woocommerce_Customize_Section_Pro( $wp_shop_woocommerce_manager,'wp_shop_woocommerce_lite_documentation', array(
			'priority'   => 1,
			'title'    => esc_html__( 'Lite Documentation', 'wp-shop-woocommerce' ),
			'pro_text' => esc_html__( 'Instruction', 'wp-shop-woocommerce' ),
			'pro_url'    => esc_url( WP_SHOP_WOOCOMMERCE_LITE_DOC ),
		) )	);

		$wp_shop_woocommerce_manager->add_section( new WP_Shop_Woocommerce_Customize_Section_Pro( $wp_shop_woocommerce_manager, 'wp_shop_woocommerce_live_demo', array(
		    'priority'   => 1,
		    'title'      => esc_html__( 'Pro Theme Demo', 'wp-shop-woocommerce' ),
		    'pro_text'   => esc_html__( 'Live Preview', 'wp-shop-woocommerce' ),
		    'pro_url'    => esc_url( WP_SHOP_WOOCOMMERCE_LIVE_DEMO ),
		) ) );
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'wp-shop-woocommerce-customize-controls', trailingslashit( get_template_directory_uri() ) . '/revolution/assets/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'wp-shop-woocommerce-customize-controls', trailingslashit( get_template_directory_uri() ) . '/revolution/assets/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
WP_Shop_Woocommerce_Customize::get_instance();