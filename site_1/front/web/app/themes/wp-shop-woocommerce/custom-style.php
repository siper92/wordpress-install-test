<?php 
	$wp_shop_woocommerce_custom_css ='';

/*----------------Related Product show/hide -------------------*/

$wp_shop_woocommerce_enable_related_product = get_theme_mod('wp_shop_woocommerce_enable_related_product',1);

	if($wp_shop_woocommerce_enable_related_product == 0){
		$wp_shop_woocommerce_custom_css .='.related.products{';
			$wp_shop_woocommerce_custom_css .='display: none;';
		$wp_shop_woocommerce_custom_css .='}';
	}

/*----------------blog post content alignment -------------------*/

$wp_shop_woocommerce_blog_Post_content_layout = get_theme_mod( 'wp_shop_woocommerce_blog_Post_content_layout','Left');
    if($wp_shop_woocommerce_blog_Post_content_layout == 'Left'){
		$wp_shop_woocommerce_custom_css .='.ct-post-wrapper .card-item {';
			$wp_shop_woocommerce_custom_css .='text-align:start;';
		$wp_shop_woocommerce_custom_css .='}';
	}else if($wp_shop_woocommerce_blog_Post_content_layout == 'Center'){
		$wp_shop_woocommerce_custom_css .='.ct-post-wrapper .card-item {';
			$wp_shop_woocommerce_custom_css .='text-align:center;';
		$wp_shop_woocommerce_custom_css .='}';
	}else if($wp_shop_woocommerce_blog_Post_content_layout == 'Right'){
		$wp_shop_woocommerce_custom_css .='.ct-post-wrapper .card-item {';
			$wp_shop_woocommerce_custom_css .='text-align:end;';
		$wp_shop_woocommerce_custom_css .='}';
	}

	/*--------------------------- Footer background image -------------------*/

    $wp_shop_woocommerce_footer_bg_image = get_theme_mod('wp_shop_woocommerce_footer_bg_image');
    if($wp_shop_woocommerce_footer_bg_image != false){
        $wp_shop_woocommerce_custom_css .='.footer-top{';
            $wp_shop_woocommerce_custom_css .='background: url('.esc_attr($wp_shop_woocommerce_footer_bg_image).');';
        $wp_shop_woocommerce_custom_css .='}';
    }

	/*--------------------------- Go to top positions -------------------*/

    $wp_shop_woocommerce_go_to_top_position = get_theme_mod( 'wp_shop_woocommerce_go_to_top_position','Right');
    if($wp_shop_woocommerce_go_to_top_position == 'Right'){
        $wp_shop_woocommerce_custom_css .='.footer-go-to-top{';
            $wp_shop_woocommerce_custom_css .='right: 20px;';
        $wp_shop_woocommerce_custom_css .='}';
    }else if($wp_shop_woocommerce_go_to_top_position == 'Left'){
        $wp_shop_woocommerce_custom_css .='.footer-go-to-top{';
            $wp_shop_woocommerce_custom_css .='left: 20px;';
        $wp_shop_woocommerce_custom_css .='}';
    }else if($wp_shop_woocommerce_go_to_top_position == 'Center'){
        $wp_shop_woocommerce_custom_css .='.footer-go-to-top{';
            $wp_shop_woocommerce_custom_css .='right: 50%;left: 50%;';
        $wp_shop_woocommerce_custom_css .='}';
    }

    /*--------------------------- Woocommerce Product Sale Positions -------------------*/

    $wp_shop_woocommerce_product_sale = get_theme_mod( 'wp_shop_woocommerce_woocommerce_product_sale','Right');
    if($wp_shop_woocommerce_product_sale == 'Right'){
        $wp_shop_woocommerce_custom_css .='.woocommerce ul.products li.product .onsale{';
            $wp_shop_woocommerce_custom_css .='left: auto; ';
        $wp_shop_woocommerce_custom_css .='}';
    }else if($wp_shop_woocommerce_product_sale == 'Left'){
        $wp_shop_woocommerce_custom_css .='.woocommerce ul.products li.product .onsale{';
            $wp_shop_woocommerce_custom_css .='right: auto;left:0;';
        $wp_shop_woocommerce_custom_css .='}';
    }else if($wp_shop_woocommerce_product_sale == 'Center'){
        $wp_shop_woocommerce_custom_css .='.woocommerce ul.products li.product .onsale{';
            $wp_shop_woocommerce_custom_css .='right: 50%; left: 50%; ';
        $wp_shop_woocommerce_custom_css .='}';
    }

    /*-------------------- Primary Color -------------------*/

	$wp_shop_woocommerce_primary_color = get_theme_mod('wp_shop_woocommerce_primary_color', '#59A2FF'); // Add a fallback if the color isn't set

	if ($wp_shop_woocommerce_primary_color) {
		$wp_shop_woocommerce_custom_css .= ':root {';
		$wp_shop_woocommerce_custom_css .= '--primary-color: ' . esc_attr($wp_shop_woocommerce_primary_color) . ';';
		$wp_shop_woocommerce_custom_css .= '}';
	}

    /*-------------------- Secondary Color -------------------*/

	$wp_shop_woocommerce_secondary_color = get_theme_mod('wp_shop_woocommerce_secondary_color', '#26242D'); // Add a fallback if the color isn't set

	if ($wp_shop_woocommerce_secondary_color) {
		$wp_shop_woocommerce_custom_css .= ':root {';
		$wp_shop_woocommerce_custom_css .= '--secondary-color: ' . esc_attr($wp_shop_woocommerce_secondary_color) . ';';
		$wp_shop_woocommerce_custom_css .= '}';
	}

    /*----------------Enable/Disable Breadcrumbs -------------------*/

    $wp_shop_woocommerce_enable_breadcrumbs = get_theme_mod('wp_shop_woocommerce_enable_breadcrumbs',1);

    if($wp_shop_woocommerce_enable_breadcrumbs == 0){
        $wp_shop_woocommerce_custom_css .='.wp-shop-woocommerce-breadcrumbs, nav.woocommerce-breadcrumb{';
            $wp_shop_woocommerce_custom_css .='display: none;';
        $wp_shop_woocommerce_custom_css .='}';
    }