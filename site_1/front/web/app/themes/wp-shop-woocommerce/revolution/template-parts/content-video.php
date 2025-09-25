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
			// Get the post ID
			$post_id = get_the_ID();

			// Check if there are video embedded in the post content
			$post = get_post($post_id);
			$wp_shop_woocommerce_content = do_shortcode(apply_filters('the_content', $post->post_content));
			$wp_shop_woocommerce_embeds = get_media_embedded_in_content($wp_shop_woocommerce_content);

			// Track displayed video embeds
			$wp_shop_woocommerce_displayed_embeds = [];

			// Check if not in a singular view
			if (!is_singular() && !empty($wp_shop_woocommerce_embeds)) {
				// Loop through embedded media and display videos
			    foreach ($wp_shop_woocommerce_embeds as $wp_shop_woocommerce_embed) {
			        // Check if the embed code contains a video tag or specific video providers like YouTube or Vimeo
			        if (strpos($wp_shop_woocommerce_embed, 'video') !== false || strpos($wp_shop_woocommerce_embed, 'youtube') !== false || strpos($wp_shop_woocommerce_embed, 'vimeo') !== false || strpos($wp_shop_woocommerce_embed, 'dailymotion') !== false || strpos($wp_shop_woocommerce_embed, 'vine') !== false || strpos($wp_shop_woocommerce_embed, 'wordPress.tv') !== false || strpos($wp_shop_woocommerce_embed, 'hulu') !== false) {
			            ?>
			            <div class="custom-embedded-video">
			                <div class="video-container">
			                    <?php echo $wp_shop_woocommerce_embed; ?>
			                </div>
			            </div>
			            <?php
			        }
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