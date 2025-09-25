<?php
/**
 * Template part for displaying posts
 *
 * @package WP Shop Woocommerce
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="card-item card-blog-post">
		<?php
		    // Check if there is a gallery embedded in the post content
		    $post_id = get_the_ID(); // Add this line to get the post ID
		    $wp_shop_woocommerce_gallery_shortcode = get_post_gallery();

			if (!is_singular() && !empty($wp_shop_woocommerce_gallery_shortcode)) {
				if (!empty($wp_shop_woocommerce_gallery_shortcode)) {
					// Display the gallery
					echo '<div class="embedded-gallery">' . do_shortcode($wp_shop_woocommerce_gallery_shortcode) . '</div>';
				}
			}
		?>
		
		<!-- .TITLE & META -->
		<header class="entry-header">
			<?php
			if ( 'post' === get_post_type() ) :

				if (is_singular()) {
					wp_shop_woocommerce_breadcrumbs();
				}
				
				if ( is_singular() ) :
					$wp_shop_woocommerce_single_enable_title = absint(get_theme_mod('wp_shop_woocommerce_enable_single_blog_post_title', 1));
					if ($wp_shop_woocommerce_single_enable_title == 1) {
						the_title( '<h1 class="entry-title">', '</h1>' );
					} ?>
				<?php
				else :
					$wp_shop_woocommerce_enable_title = absint(get_theme_mod('wp_shop_woocommerce_enable_blog_post_title', 1));
					if ($wp_shop_woocommerce_enable_title == 1) {
						the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
					}
				endif;

				// Check if is singular
				if ( is_singular() ) : ?>
					<?php
					$wp_shop_woocommerce_single_blog_meta = absint(get_theme_mod('wp_shop_woocommerce_enable_single_blog_post_meta', 1));
					if($wp_shop_woocommerce_single_blog_meta == 1){ ?>
					<div class="entry-meta">
						<?php
						wp_shop_woocommerce_posted_on();
						wp_shop_woocommerce_posted_by();
						?>
					</div><!-- .entry-meta -->
					<?php } ?>
				<?php else : 
					$wp_shop_woocommerce_blog_meta = absint(get_theme_mod('wp_shop_woocommerce_enable_blog_post_meta', 1));
					if($wp_shop_woocommerce_blog_meta == 1){ ?>
						<div class="entry-meta">
							<?php
							wp_shop_woocommerce_posted_on();
							wp_shop_woocommerce_posted_by();
							?>
						</div><!-- .entry-meta -->
					<?php }
				endif;

			endif;
			?>
		</header>
		<!-- .TITLE & META -->

		
		<!-- .POST TAG -->
		<?php
		// Check if is singular
		if ( is_singular() ) : ?>
			<?php
			$wp_shop_woocommerce_single_post_tags = absint(get_theme_mod('wp_shop_woocommerce_enable_single_blog_post_tags', 1));
			if($wp_shop_woocommerce_single_post_tags == 1){ ?>
			<?php
				$post_tags = get_the_tags();
				if ( $post_tags ) {
					echo '<div class="post-tags"><strong>' . esc_html__('Post Tags: ', 'wp-shop-woocommerce') . '</strong>';
					the_tags('', ', ', '');
					echo '</div>';
				}
			?><!-- .tags -->
			<?php } ?>
		<?php else : 
			$wp_shop_woocommerce_post_tags = absint(get_theme_mod('wp_shop_woocommerce_enable_blog_post_tags', 1));
			if($wp_shop_woocommerce_post_tags == 1){ ?>
				<?php
					$post_tags = get_the_tags();
					if ( $post_tags ) {
						echo '<div class="post-tags"><strong>' . esc_html__('Post Tags: ', 'wp-shop-woocommerce') . '</strong>';
						the_tags('', ', ', '');
						echo '</div>';
					}
				?><!-- .tags -->
			<?php }
		endif;
		?>
		<!-- .POST TAG -->

		<!-- .IMAGE -->
		<?php if ( is_singular() ) : ?>
			<?php 
			$wp_shop_woocommerce_blog_thumbnail = absint(get_theme_mod('wp_shop_woocommerce_enable_single_post_image', 1));
			if ( $wp_shop_woocommerce_blog_thumbnail == 1 ) { 
			?>
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="card-media">
						<?php wp_shop_woocommerce_post_thumbnail(); ?>
					</div>
				<?php } else {
					// Fallback default image
					$wp_shop_woocommerce_default_post_thumbnail = get_template_directory_uri() . '/revolution/assets/images/slider1.png';
					echo '<img class="default-post-img" src="' . esc_url( $wp_shop_woocommerce_default_post_thumbnail ) . '" alt="' . esc_attr( get_the_title() ) . '">';
				} ?>
			<?php } ?>
		<?php else : ?>
		<?php 
			$wp_shop_woocommerce_blog_thumbnail = absint(get_theme_mod('wp_shop_woocommerce_enable_blog_post_image', 1));
			if ( $wp_shop_woocommerce_blog_thumbnail == 1 ) { 
			?>
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="card-media">
						<?php wp_shop_woocommerce_post_thumbnail(); ?>
					</div>
				<?php } else {
					// Fallback default image
					$wp_shop_woocommerce_default_post_thumbnail = get_template_directory_uri() . '/revolution/assets/images/slider1.png';
					echo '<img class="default-post-img" src="' . esc_url( $wp_shop_woocommerce_default_post_thumbnail ) . '" alt="' . esc_attr( get_the_title() ) . '">';
				} ?>
			<?php } ?>
		<?php endif; ?>
		<!-- .IMAGE -->

		<!-- .CONTENT & BUTTON -->
		<div class="entry-content">
			<?php
				if ( is_singular() ) :
					$wp_shop_woocommerce_single_enable_excerpt = absint(get_theme_mod('wp_shop_woocommerce_enable_single_blog_post_content', 1));
					if ($wp_shop_woocommerce_single_enable_excerpt == 1) {
						the_content();
					} ?>
				<?php else :
					// Excerpt functionality for archive pages
					$wp_shop_woocommerce_enable_excerpt = absint(get_theme_mod('wp_shop_woocommerce_enable_blog_post_content', 1));
					if ($wp_shop_woocommerce_enable_excerpt == 1) {
						echo "<p>".wp_trim_words(get_the_excerpt(), get_theme_mod('wp_shop_woocommerce_excerpt_limit', 25))."</p>";
					}
					?>
					<?php // Check if 'Continue Reading' button should be displayed
					$wp_shop_woocommerce_enable_read_more = absint(get_theme_mod('wp_shop_woocommerce_enable_blog_post_button', 1));
					if ($wp_shop_woocommerce_enable_read_more == 1) {
						if ( get_theme_mod( 'wp_shop_woocommerce_read_more_text', __('Continue Reading....', 'wp-shop-woocommerce') ) ) :
							?>
							<a href="<?php the_permalink(); ?>" class="btn read-btn text-uppercase">
								<?php echo esc_html( get_theme_mod( 'wp_shop_woocommerce_read_more_text', __('Continue Reading....', 'wp-shop-woocommerce') ) ); ?>
							</a>
							<?php
						endif;
					}?>
				<?php endif; ?>
			<?php
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wp-shop-woocommerce' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>
		<!-- .CONTENT & BUTTON -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->