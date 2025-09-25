<?php
/**
 * The header for our theme
 *
 * @package WP Shop Woocommerce
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-shop-woocommerce' ); ?></a>

	<?php
		$wp_shop_woocommerce_preloader_wrap = absint(get_theme_mod('wp_shop_woocommerce_enable_preloader', 0));
		if($wp_shop_woocommerce_preloader_wrap == 1){ ?>
			<div id="loader">
				<div class="loader-container">
					<div id="preloader" class="loader-2">
						<div class="dot"></div>
					</div>
				</div>
			</div>
	<?php } ?>

	<header id="masthead" class="site-header">
		<?php $wp_shop_woocommerce_has_header_image = has_header_image(); ?>
		<div class="main-header-wrap">
			<div class="top-box">
				<div class="container">
					<div class="flex-row">
						<div class="nav-box-header-left">
							<?php if ( get_theme_mod('wp_shop_woocommerce_header_topbar_text','FREE SHIPPING on orders over $99. This offer is valid on all store items.') ) : ?><p><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_header_topbar_text','FREE SHIPPING on orders over $99. This offer is valid on all store items.') ); ?></p><?php endif; ?>
						</div>
						<div class="nav-box-header-right">
							<?php if ( get_theme_mod('wp_shop_woocommerce_header_info_phone','+123 456 7890') ) : ?><p><i class="<?php echo esc_attr(get_theme_mod('wp_shop_woocommerce_header_phone_icon','fas fa-phone-alt')); ?>"></i> <?php echo esc_html( get_theme_mod('wp_shop_woocommerce_header_info_phone','+123 456 7890') ); ?></p><?php endif; ?>
							<?php if ( get_theme_mod('wp_shop_woocommerce_header_info_email','support@example.com') ) : ?><p><i class="<?php echo esc_attr(get_theme_mod('wp_shop_woocommerce_header_mail_icon','fas fa-envelope')); ?> mail"></i> <?php echo esc_html( get_theme_mod('wp_shop_woocommerce_header_info_email','support@example.com') ); ?></p><?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="header-info-box" <?php if (!empty($wp_shop_woocommerce_has_header_image)) { ?> style="background-image: url(<?php echo header_image(); ?>);" <?php } ?> >
			<div class="container">
				<div class="flex-row header-space">
					<div class="head-1">
						<div class="site-branding">
							<?php
							the_custom_logo();
							if ( is_front_page() && is_home() ) :
								?>
								<?php if( get_theme_mod('wp_shop_woocommerce_site_title_text',true)){ ?>
									<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
								<?php } ?>
								<?php
							else :
								?>
								<?php if( get_theme_mod('wp_shop_woocommerce_site_title_text',true)){ ?>
									<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
								<?php } ?>
								<?php
							endif; ?>
							<?php $wp_shop_woocommerce_description = get_bloginfo( 'description', 'display' );
								if ( $wp_shop_woocommerce_description || is_customize_preview() ) :
								?>
								<?php if( get_theme_mod('wp_shop_woocommerce_site_tagline_text',false)){ ?>
									<p class="site-description"><?php echo $wp_shop_woocommerce_description; ?></p>
								<?php } ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="head-2">
						<?php if(class_exists('woocommerce')){ ?>
				          <?php get_product_search_form(); ?>
				        <?php } ?>
					</div>
					<div class="head-3">
						<div class="account">
			              <?php if(class_exists('woocommerce')){ ?>
			                <?php if ( is_user_logged_in() ) { ?>
			                  <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_attr_e('My Account','wp-shop-woocommerce'); ?>"><i class="fas fa-sign-in-alt"></i><?php esc_html_e('My Account','wp-shop-woocommerce'); ?><span class="screen-reader-text"><?php esc_html_e( 'My Account','wp-shop-woocommerce' );?></span></a>
			                <?php }
			                else { ?>
			                  <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_attr_e('Login / Register','wp-shop-woocommerce'); ?>"><i class="fas fa-user"></i><?php esc_html_e('Login / Register','wp-shop-woocommerce'); ?><span class="screen-reader-text"><?php esc_html_e( 'Login / Register','wp-shop-woocommerce' );?></span></a>
			                <?php } ?>
			              <?php }?>
			            </div>
					</div>
					<div class="head-4">
						<?php if(class_exists('woocommerce')){ ?>
						<span class="cart_no">
			                <a href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_attr_e( 'shopping cart','wp-shop-woocommerce' ); ?>"><i class="fas fa-shopping-basket"></i><span class="screen-reader-text"><?php esc_html_e( 'shopping cart','wp-shop-woocommerce' );?></span></a>
			                <span class="cart-value"> <?php echo esc_html(wp_kses_data( WC()->cart->get_cart_contents_count() ));?></span>
		              	</span>
		              	<?php }?>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-box">
			<div class="container <?php echo esc_attr( get_theme_mod( 'wp_shop_woocommerce_enable_sticky_header', false ) ? 'sticky-header' : '' ); ?>">
				<div class="nav-box-header-lefto">
					<nav id="site-navigation" class="main-navigation">
						<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><i class="fas fa-bars"></i></button>
						<?php
							wp_nav_menu(
								array(
									'theme_location' => 'menu-1',
									'menu_id'        => 'primary-menu',
								)
							);
						?>
					</nav>
				</div>
			</div>
    	</div>
	</header>