<?php
/**
 * Template Name: Home Page
 */

get_header();
?>

<main id="primary">
    <div class="slider-bg">
       <div class="container">
            <div class="slider-divide">
            <div class="category-list">
              <?php if(class_exists('woocommerce')){ ?>
                <div class="categry-title">
                    <strong><i class="fa fa-bars" aria-hidden="true"></i><?php echo esc_html_e('ALL CATEGORIES','wp-shop-woocommerce'); ?></strong>
                </div>
                <div class="product-cat" id="style-2">
                  <?php
                    $wp_shop_woocommerce_args = array(                  
                      'orderby'    => 'title',
                      'order'      => 'ASC',
                      'hide_empty' => 0,
                      'parent'  => 0
                    );
                    $product_categories = get_terms( 'product_cat', $wp_shop_woocommerce_args );
                    $wp_shop_woocommerce_count = count($product_categories);
                    if ( $wp_shop_woocommerce_count > 0 ){
                        foreach ( $product_categories as $product_category ) {
                          $wp_shop_woocommerce_kids_cat_id   = $product_category->term_id;
                          $cat_link = get_category_link( $wp_shop_woocommerce_kids_cat_id );
                          if ($product_category->category_parent == 0) { ?>
                        <li class="drp_dwn_menu"><a href="<?php echo esc_url(get_term_link( $product_category ) ); ?>">
                        <?php
                      }
                        echo esc_html( $product_category->name ); ?></a><i class="fas fa-caret-right"></i></li>
                        <?php
                        }
                      }
                  ?>
                </div>
              <?php }?> 
            </div>
            <?php 
            $wp_shop_woocommerce_main_slider_wrap = absint(get_theme_mod('wp_shop_woocommerce_enable_slider', 0));
            if($wp_shop_woocommerce_main_slider_wrap == 1){ 
            ?>
        

            <div class="slider-boxx">
                <section id="main-slider-wrap">
                    <div class="owl-carousel">
                        <?php for ($wp_shop_woocommerce_i=1; $wp_shop_woocommerce_i <= 3; $wp_shop_woocommerce_i++) { ?>
                          <?php  if (
                                get_theme_mod( 'wp_shop_woocommerce_slider_image'.$wp_shop_woocommerce_i ) != '' ||
                                get_theme_mod( 'wp_shop_woocommerce_slider_xtra_heading'.$wp_shop_woocommerce_i ) != '' ||
                                get_theme_mod( 'wp_shop_woocommerce_slider_heading'.$wp_shop_woocommerce_i ) != '' ||
                                get_theme_mod( 'wp_shop_woocommerce_slider_text'.$wp_shop_woocommerce_i ) != '' ||
                                get_theme_mod( 'wp_shop_woocommerce_slider_button1_link'.$wp_shop_woocommerce_i ) != '' ||
                                get_theme_mod( 'wp_shop_woocommerce_slider_button1_text'.$wp_shop_woocommerce_i ) != ''
                            ) {  ?>
                            <div class="main-slider-inner-box">
                                <?php if ( get_theme_mod('wp_shop_woocommerce_slider_image'.$wp_shop_woocommerce_i) ) : ?>
                                    <img src="<?php echo esc_url( get_theme_mod('wp_shop_woocommerce_slider_image'.$wp_shop_woocommerce_i) ); ?>">
                                    <div class="main-slider-content-box">
                                        <?php if ( get_theme_mod('wp_shop_woocommerce_slider_xtra_heading'.$wp_shop_woocommerce_i) ) : ?><p class="xtra-head"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_slider_xtra_heading'.$wp_shop_woocommerce_i) ); ?></p><?php endif; ?>
                                        <?php if ( get_theme_mod('wp_shop_woocommerce_slider_heading'.$wp_shop_woocommerce_i) ) : ?><h3><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_slider_heading'.$wp_shop_woocommerce_i) ); ?></h3><?php endif; ?>
                                        <hr>
                                        <?php if ( get_theme_mod('wp_shop_woocommerce_slider_text'.$wp_shop_woocommerce_i) ) : ?><p><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_slider_text'.$wp_shop_woocommerce_i) ); ?></p><?php endif; ?>
                                        <div class="main-slider-button">
                                            <?php if ( get_theme_mod('wp_shop_woocommerce_slider_button1_link'.$wp_shop_woocommerce_i) ||  get_theme_mod('wp_shop_woocommerce_slider_button1_text'.$wp_shop_woocommerce_i )) : ?><a class="btn-1" href="<?php echo esc_url( get_theme_mod('wp_shop_woocommerce_slider_button1_link'.$wp_shop_woocommerce_i) ); ?>"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_slider_button1_text'.$wp_shop_woocommerce_i) ); ?></a><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php }?>
                        <?php } ?>
                    </div>
                </section>
            </div>
            <?php } ?>
        </div>
    </div>  
    </div> 
    <?php 
    $wp_shop_woocommerce_main_expert_wrap = absint(get_theme_mod('wp_shop_woocommerce_enable_product', 0));
    if($wp_shop_woocommerce_main_expert_wrap == 1){ 
    ?>
    <section id="product-sec" class="product-section">
        <div class="container">
            <div class="product-divide">
                <div class="product-blog">
                   <div class="top-expert-wrap">   
                        <div class="box">
                            <?php if ( get_theme_mod('wp_shop_woocommerce_category_image') ) : ?><img src="<?php echo esc_url( get_theme_mod('wp_shop_woocommerce_category_image') ); ?>"><?php endif; ?>
                            <div class="box-content">
                                <?php if ( get_theme_mod('wp_shop_woocommerce_product_sale_heading') ) : ?><h3 class="sale-tag"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_product_sale_heading') ); ?></h3><?php endif; ?>
                                <?php if ( get_theme_mod('wp_shop_woocommerce_product_discount_text') ) : ?><p class="discount-text"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_product_discount_text') ); ?></p><?php endif; ?>
                                <?php if ( get_theme_mod('wp_shop_woocommerce_product_heading_text') ) : ?><h4 class="product-head"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_product_heading_text') ); ?></h4><?php endif; ?>
                                <?php if ( get_theme_mod('wp_shop_woocommerce_product_sub_heading_text') ) : ?><p class="product-sub-head"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_product_sub_heading_text') ); ?></p><?php endif; ?>
                                <div class="main-expert-button">
                                    <?php if ( get_theme_mod('wp_shop_woocommerce_category_button1_link') ||  get_theme_mod('wp_shop_woocommerce_category_button1_text' )) : ?><a href="<?php echo esc_url( get_theme_mod('wp_shop_woocommerce_category_button1_link') ); ?>"><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_category_button1_text') ); ?></a><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
                 <div class="product-list">
                    <div class="heading-expert-wrap">
                        <?php if ( get_theme_mod('wp_shop_woocommerce_event_heading') ) : ?><h5><?php echo esc_html( get_theme_mod('wp_shop_woocommerce_event_heading') ); ?></h5>
                    <hr><?php endif; ?>

                    </div>

                    <div class="flex-row">
                      <?php if ( class_exists( 'WooCommerce' ) ) {
                        $wp_shop_woocommerce_args = array( 
                          'post_type' => 'product',
                          'product_cat' => get_theme_mod('wp_shop_woocommerce_best_product_category'),
                          'order' => 'ASC',
                          'posts_per_page' => '10'
                        );
                        $loop = new WP_Query( $wp_shop_woocommerce_args );
                        while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>         
                        <div class="product-box">  
                          <div class="product-box-content">
                            <div class="product-image">
                                <?php 
                                    if ( has_post_thumbnail() ) {
                                        echo get_the_post_thumbnail( get_the_ID(), 'shop_catalog' );
                                    } else {
                                        echo '<img src="' . esc_url(woocommerce_placeholder_img_src()) . '" alt="Placeholder" />';
                                    }
                                ?>
                            </div>
                            <div class="product-detail">
                               <h6 class="product-heading-text"><a href="<?php echo esc_url(get_permalink( $loop->post->ID )); ?>"><?php the_title(); ?></a></h6>
                                <p class="product-rating <?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?></p>  
                            </div>
                          </div>
                        </div> 
                    <?php endwhile; wp_reset_postdata(); ?>
                    <?php } ?>
                    </div> 
                </div>
            </div>
        </div>
    </section>
    <?php } ?>
</main>

<?php
get_footer();