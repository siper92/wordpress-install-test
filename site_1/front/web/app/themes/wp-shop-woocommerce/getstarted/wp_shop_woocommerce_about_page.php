<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'WP_Shop_Woocommerce_Welcome' ) ) {

	class WP_Shop_Woocommerce_Welcome {
		public $theme_fields;

		public function __construct( $fields = array() ) {
			$this->theme_fields = $fields;
			add_action ('admin_init' , array( $this, 'admin_scripts' ) );
			add_action('admin_menu', array( $this, 'wp_shop_woocommerce_getstart_page_menu' ));
		}

		public function admin_scripts() {
			global $pagenow;
			$file_dir = get_template_directory_uri() . '/getstarted/assets/';

			if ( $pagenow === 'themes.php' && isset($_GET['page']) && $_GET['page'] === 'wp-shop-woocommerce-getstart-page' ) {

				wp_enqueue_style (
					'wp-shop-woocommerce-getstart-page-style',
					$file_dir . 'css/getstart-page.css',
					array(), '1.0.0'
				);

				wp_enqueue_script (
					'wp-shop-woocommerce-getstart-page-functions',
					$file_dir . 'js/getstart-page.js',
					array('jquery'),
					'1.0.0',
					true
				);
			}
		}

        public function theme_info($id, $wp_shop_woocommerce_screenshot = false) {
            $themedata = wp_get_theme();
            return ($wp_shop_woocommerce_screenshot === true) ? esc_url($themedata->get_screenshot()) : esc_html($themedata->get($id));
        }

        public function wp_shop_woocommerce_getstart_page_menu() {
            add_theme_page(
                /* translators: 1: Theme Name. */
                sprintf(esc_html__('About %1$s', 'wp-shop-woocommerce'), $this->theme_info('Name')),
                sprintf(esc_html__('About %1$s', 'wp-shop-woocommerce'), $this->theme_info('Name')),
                'edit_theme_options',
                'wp-shop-woocommerce-getstart-page',
                array( $this, 'wp_shop_woocommerce_getstart_page' )
            );
		}

        public function wp_shop_woocommerce_getstart_page() {
            $wp_shop_woocommerce_tabs = array(
                'wp_shop_woocommerce_getting_started' => esc_html__('Getting Started', 'wp-shop-woocommerce'),
                'wp_shop_woocommerce_free_pro' => esc_html__('Free VS Pro', 'wp-shop-woocommerce'),
                'changelog' => esc_html__('Changelog', 'wp-shop-woocommerce'),
                'support' => esc_html__('Support', 'wp-shop-woocommerce'),
                'review' => esc_html__('Rate & Review', 'wp-shop-woocommerce'),
            );
            ?>
                <div class="wrap about-wrap access-wrap">

                    <div class="abt-promo-wrap clearfix">
                        <div class="abt-theme-wrap">
                            <h1>
                                <?php
                                printf(
                                    /* translators: 1: Theme Name. */
                                    esc_html__('Welcome to %1$s - Version %2$s', 'wp-shop-woocommerce'),
                                    esc_html($this->theme_info('Name')),
                                    esc_html($this->theme_info('Version'))
                                );
                                ?>
                            </h1>
                            <div class="buttons">
                                <a target="_blank" href="<?php echo esc_url('https://www.revolutionwp.com/products/woocommerce-wordpress-theme'); ?>"><?php echo esc_html__('Buy Pro Theme', 'wp-shop-woocommerce'); ?></a>
                                <a target="_blank" href="<?php echo esc_url('https://demo.revolutionwp.com/shop-cart-woocommerce-pro/'); ?>"><?php echo esc_html__('Preview Pro Version', 'wp-shop-woocommerce'); ?></a>
                            </div>
                        </div>
                    </div>

                    <div class="nav-tab-wrapper clearfix">
                        <?php
                            $tabHTML = '';

                            foreach ($wp_shop_woocommerce_tabs as $id => $wp_shop_woocommerce_label) :

                                $wp_shop_woocommerce_target = '';
                                $wp_shop_woocommerce_nav_class = 'nav-tab';
                                $wp_shop_woocommerce_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'wp_shop_woocommerce_getting_started';

                                if ($id === $wp_shop_woocommerce_section) {
                                    $wp_shop_woocommerce_nav_class .= ' nav-tab-active';
                                }

                                if ($id === 'wp_shop_woocommerce_free_pro') {
                                    $wp_shop_woocommerce_nav_class .= ' upgrade-button';
                                }

                                switch ($id) {

                                    case 'support':
                                        $wp_shop_woocommerce_target = 'target="_blank"';
                                        $wp_shop_woocommerce_url = esc_url('https://wordpress.org/support/theme/' . esc_html($this->theme_info('TextDomain')));
                                    break;

                                    case 'review':
                                        $wp_shop_woocommerce_target = 'target="_blank"';
                                        $wp_shop_woocommerce_url = esc_url('https://wordpress.org/support/theme/' . esc_html($this->theme_info('TextDomain')) . '/reviews/#new-post');
                                    break;
                                    
                                    case 'wp_shop_woocommerce_getting_started':
                                        $wp_shop_woocommerce_url = esc_url(admin_url('themes.php?page=wp-shop-woocommerce-getstart-page'));
                                    break;

                                    default:
                                        $wp_shop_woocommerce_url = esc_url(admin_url('themes.php?page=wp-shop-woocommerce-getstart-page&section=' . esc_attr($id)));
                                    break;

                                }

                                $tabHTML .= '<a ';
                                $tabHTML .= $wp_shop_woocommerce_target;
                                $tabHTML .= ' href="' . $wp_shop_woocommerce_url . '"';
                                $tabHTML .= ' class="' . esc_attr($wp_shop_woocommerce_nav_class) . '"';
                                $tabHTML .= '>';
                                $tabHTML .= esc_html($wp_shop_woocommerce_label);
                                $tabHTML .= '</a>';

                            endforeach;

                            echo $tabHTML;
                        ?>
                    </div>

                    <div class="getstart-section-wrapper">
                        <div class="getstart-section wp_shop_woocommerce_getting_started clearfix">
                            <?php
                                $wp_shop_woocommerce_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'wp_shop_woocommerce_getting_started';
                                switch ($wp_shop_woocommerce_section) {

                                    case 'wp_shop_woocommerce_free_pro':
                                        $this->wp_shop_woocommerce_free_pro();
                                    break;

                                    case 'changelog':
                                        $this->changelog();
                                    break;

                                    case 'wp_shop_woocommerce_getting_started':
                                    default:
                                        $this->wp_shop_woocommerce_getting_started();
                                    break;

                                }
                            ?>
                        </div>
                    </div>

                </div>
            <?php
		}

        public function wp_shop_woocommerce_getting_started() {
            ?>
            <div class="getting-started-top-wrap clearfix">
                <div class="theme-details">
                    <div class="theme-screenshot">
                        <img src="<?php echo esc_url( $this->theme_info( 'Screenshot', true ) ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'wp-shop-woocommerce' ); ?>"/>
                    </div>
                    <div class="about-text"><?php echo esc_html( $this->theme_info( 'Description' ) ); ?></div>
                    <div class="clearfix"></div>
                </div>
                <div class="theme-steps-list">
                    <div class="theme-steps demo-import">
                        <h3><?php echo esc_html__( 'One Click Demo Import', 'wp-shop-woocommerce' ); ?></h3>
                        <p><?php echo esc_html__( 'Easily set up your website with our One Click Demo Import feature. This functionality allows you to replicate our demo site with just a single click, ensuring you have a fully functional layout to start from. Whether you’re a beginner or an experienced developer, this tool simplifies the setup process, saving you time and effort.', 'wp-shop-woocommerce' ); ?></p>
                        <a target="_blank" class="button button-primary" href="<?php echo esc_url( WP_SHOP_WOOCOMMERCE_IMPORT_URL ); ?>"><?php echo esc_html__( 'Click Here For Demo Import', 'wp-shop-woocommerce' ); ?></a>
                    </div>
                    <div class="getstart">
                        <div class="theme-steps">
                            <h3><?php echo esc_html__( 'Documentation', 'wp-shop-woocommerce' ); ?></h3>
                            <p><?php echo esc_html__( 'Need more details? Check our comprehensive documentation for step-by-step guidance on using the WP Shop Woocommerce Theme.', 'wp-shop-woocommerce' ); ?></p>
                            <a target="_blank" class="button button-primary" href="<?php echo esc_url( 'https://demo.revolutionwp.com/wpdocs/shop-cart-woocommerce-free/' ); ?>"><?php echo esc_html__( 'Go to Free Docs', 'wp-shop-woocommerce' ); ?></a>
                        </div>

                        <div class="theme-steps">
                            <h3><?php echo esc_html__( 'Preview Pro Theme', 'wp-shop-woocommerce' ); ?></h3>
                            <p><?php echo esc_html__( 'Discover the full potential of our Pro Theme! Click the Live Demo button to experience premium features and beautiful designs.', 'wp-shop-woocommerce' ); ?></p>
                            <a target="_blank" class="button button-primary" href="<?php echo esc_url( 'https://demo.revolutionwp.com/shop-cart-woocommerce-pro/' ); ?>"><?php echo esc_html__( 'Live Demo', 'wp-shop-woocommerce' ); ?></a>
                        </div>

                        <div class="theme-steps highlight">
                            <h3><?php echo esc_html__( 'Buy WP Shop Woocommerce Pro', 'wp-shop-woocommerce' ); ?></h3>
                            <p><?php echo esc_html__( 'Unlock unlimited features and enhancements by purchasing the Pro version of WP Shop Woocommerce Theme.', 'wp-shop-woocommerce' ); ?></p>
                            <a target="_blank" class="button button-primary" href="<?php echo esc_url( 'https://www.revolutionwp.com/products/woocommerce-wordpress-theme' ); ?>"><?php echo esc_html__( 'Buy Pro Version @$39', 'wp-shop-woocommerce' ); ?></a>
                        </div>

                        <div class="theme-steps highlight">
                            <h3><?php echo esc_html__( 'Get the Bundle', 'wp-shop-woocommerce' ); ?></h3>
                            <p><?php echo esc_html__( 'The WordPress Theme Bundle is a comprehensive collection of 30+ premium themes, offering everything you need to create stunning, professional websites with ease.', 'wp-shop-woocommerce' ); ?></p>
                            <a target="_blank" class="button button-primary" href="<?php echo esc_url( 'https://www.revolutionwp.com/products/wordpress-theme-bundle' ); ?>"><?php echo esc_html__( 'Get Bundle', 'wp-shop-woocommerce' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

		public function wp_shop_woocommerce_free_pro() {
            ?>
            <table class="card table free-pro" cellspacing="0" cellpadding="0">
                <tbody class="table-body">
                    <tr class="table-head">
                        <th class="large"><?php echo esc_html__( 'Features', 'wp-shop-woocommerce' ); ?></th>
                        <th class="indicator"><?php echo esc_html__( 'Free theme', 'wp-shop-woocommerce' ); ?></th>
                        <th class="indicator"><?php echo esc_html__( 'Pro Theme', 'wp-shop-woocommerce' ); ?></th>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'One Click Demo Import', 'wp-shop-woocommerce' ); ?></h4>
                                <div class="feature-inline-row">
                                    <span class="info-icon dashicon dashicons dashicons-info"></span>
                                    <span class="feature-description">
                                        <?php echo esc_html__( 'After the activation of WP Shop Woocommerce theme, all settings will be imported and Data Import.', 'wp-shop-woocommerce' ); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Responsive Design', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Site Logo upload', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Footer Copyright text', 'wp-shop-woocommerce' ); ?></h4>
                                <div class="feature-inline-row">
                                    <span class="info-icon dashicon dashicons dashicons-info"></span>
                                    <span class="feature-description">
                                        <?php echo esc_html__( 'Remove the copyright text from the Footer.', 'wp-shop-woocommerce' ); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Global Color', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Regular Bug Fixes', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Theme Sections', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="abc"><?php echo esc_html__( '2 Sections', 'wp-shop-woocommerce' ); ?></span></td>
                        <td class="indicator"><span class="abc"><?php echo esc_html__( '15+ Sections', 'wp-shop-woocommerce' ); ?></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Custom colors', 'wp-shop-woocommerce' ); ?></h4>
                                <div class="feature-inline-row">
                                    <span class="info-icon dashicon dashicons dashicons-info"></span>
                                    <span class="feature-description">
                                        <?php echo esc_html__( 'Choose a color for links, buttons, icons and so on.', 'wp-shop-woocommerce' ); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Google fonts', 'wp-shop-woocommerce' ); ?></h4>
                                <div class="feature-inline-row">
                                    <span class="info-icon dashicon dashicons dashicons-info"></span>
                                    <span class="feature-description">
                                        <?php echo esc_html__( 'You can choose and use over 600 different fonts, for the logo, the menu and the titles.', 'wp-shop-woocommerce' ); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Enhanced Plugin Integration', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Fully SEO Optimized', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Premium Support', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Extensive Customization', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'Custom Post Types', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="feature-row">
                        <td class="large">
                            <div class="feature-wrap">
                                <h4><?php echo esc_html__( 'High-Level Compatibility with Modern Browsers', 'wp-shop-woocommerce' ); ?></h4>
                            </div>
                        </td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-no-alt" size="30"></span></td>
                        <td class="indicator"><span class="dashicon dashicons dashicons-yes" size="30"></span></td>
                    </tr>

                    <tr class="upsell-row">
                        <td></td>
                        <td><span class="abc"><?php echo esc_html__( 'Try Out Our Premium Version', 'wp-shop-woocommerce' ); ?></span></td>
                        <td>
                            <a target="_blank" href="<?php echo esc_url( 'https://www.revolutionwp.com/products/woocommerce-wordpress-theme' ); ?>" class="button button-primary"><?php echo esc_html__( 'Buy Pro Theme', 'wp-shop-woocommerce' ); ?></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

		public function changelog() {
            if ( is_file( trailingslashit( get_stylesheet_directory() ) . '/getstarted/wp_shop_woocommerce_changelog.php' ) ) {
                require_once( trailingslashit( get_stylesheet_directory() ) . '/getstarted/wp_shop_woocommerce_changelog.php' );
            } else {
                require_once( trailingslashit( get_template_directory() ) . '/getstarted/wp_shop_woocommerce_changelog.php' );
            }
        }
	}

}
new WP_Shop_Woocommerce_Welcome();
?>