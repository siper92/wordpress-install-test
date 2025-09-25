<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package WP Shop Woocommerce
 */

?>

<section class="no-results not-found">
	<header class="page-header">
		<?php wp_shop_woocommerce_breadcrumbs(); ?>
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'wp-shop-woocommerce' ); ?></h1>
	</header>

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'wp-shop-woocommerce' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wp-shop-woocommerce' ); ?></p>
			<?php
			get_search_form();

		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wp-shop-woocommerce' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div>
</section>