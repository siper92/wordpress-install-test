<?php

if (!defined('ABSPATH')) {
    exit;
}

class BSH_Token_Handler {

    private $shared_secret;

    public function __construct() {
        $this->shared_secret = get_option('bsh_shared_secret', '');

        add_action('init', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_subscription_checkout'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_query_vars() {
        global $wp;
        $wp->add_query_var('subscription_checkout');
    }

    public function handle_subscription_checkout() {
        if (!get_query_var('subscription_checkout')) {
            return;
        }

        if (!isset($_GET['token']) || empty($_GET['token'])) {
            wp_die(__('Invalid access - token required.', 'billing-site-handler'), 'Unauthorized', array('response' => 401));
        }

        $token_data = $this->validate_token($_GET['token']);

        if (!$token_data) {
            wp_die(__('Invalid or expired token.', 'billing-site-handler'), 'Unauthorized', array('response' => 401));
        }

        $subscription = $this->create_subscription_from_token($token_data);

        if ($subscription) {
            $checkout_url = $subscription->get_checkout_payment_url();
            wp_redirect($checkout_url);
            exit;
        } else {
            wp_die(__('Failed to create subscription. Please contact support.', 'billing-site-handler'), 'Error', array('response' => 500));
        }
    }

    public function validate_token($token) {
        if (empty($this->shared_secret)) {
            error_log('BSH: Shared secret not configured');
            return false;
        }

        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            error_log('BSH: Invalid token format');
            return false;
        }

        list($payload, $signature) = $parts;

        $expected_signature = hash_hmac('sha256', $payload, $this->shared_secret);
        if (!hash_equals($expected_signature, $signature)) {
            error_log('BSH: Token signature validation failed');
            return false;
        }

        $data = json_decode(base64_decode($payload), true);

        if (!$data) {
            error_log('BSH: Failed to decode token payload');
            return false;
        }

        if (!isset($data['expires']) || $data['expires'] < time()) {
            error_log('BSH: Token has expired');
            return false;
        }

        if (!isset($data['order_id'], $data['customer_email'], $data['items'])) {
            error_log('BSH: Token missing required fields');
            return false;
        }

        return $data;
    }

    public function create_subscription_from_token($token_data) {
        try {
            $customer = $this->get_or_create_customer($token_data);

            if (!$customer) {
                error_log('BSH: Failed to create/get customer');
                return false;
            }

            $subscription_items = array_filter($token_data['items'], function($item) {
                return !empty($item['is_subscription']);
            });

            if (empty($subscription_items)) {
                error_log('BSH: No subscription items found in token');
                return false;
            }

            $first_item = reset($subscription_items);

            $subscription = wcs_create_subscription(array(
                'order_id' => 0,
                'billing_period' => $first_item['subscription_period'] ?: 'month',
                'billing_interval' => $first_item['subscription_interval'] ?: 1,
                'customer_id' => $customer->get_id(),
                'status' => 'pending'
            ));

            if (!$subscription) {
                error_log('BSH: Failed to create subscription');
                return false;
            }

            foreach ($subscription_items as $item_data) {
                $product = $this->get_or_create_subscription_product($item_data);

                if ($product) {
                    $subscription->add_product(
                        $product,
                        $item_data['quantity'] ?: 1
                    );
                }
            }

            $subscription->set_billing_address($token_data['billing_address']);
            $subscription->set_shipping_address($token_data['billing_address']);

            $subscription->add_meta_data('_storefront_order_id', $token_data['order_id']);
            $subscription->add_meta_data('_storefront_url', $token_data['storefront_url']);

            $subscription->calculate_totals();
            $subscription->save();

            return $subscription;

        } catch (Exception $e) {
            error_log('BSH: Exception creating subscription: ' . $e->getMessage());
            return false;
        }
    }

    private function get_or_create_customer($token_data) {
        $email = $token_data['customer_email'];
        $customer = get_user_by('email', $email);

        if (!$customer) {
            $customer_id = wp_create_user(
                $email,
                wp_generate_password(),
                $email
            );

            if (is_wp_error($customer_id)) {
                error_log('BSH: Failed to create customer: ' . $customer_id->get_error_message());
                return false;
            }

            $customer = get_user_by('id', $customer_id);

            wp_update_user(array(
                'ID' => $customer_id,
                'first_name' => $token_data['customer_first_name'],
                'last_name' => $token_data['customer_last_name'],
                'display_name' => $token_data['customer_first_name'] . ' ' . $token_data['customer_last_name']
            ));
        }

        return new WC_Customer($customer->ID);
    }

    private function get_or_create_subscription_product($item_data) {
        $product_name = $item_data['name'];
        $price = floatval($item_data['price']) / intval($item_data['quantity']);

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_storefront_product_sync',
                    'value' => $item_data['product_id'],
                    'compare' => '='
                )
            )
        );

        $existing_products = get_posts($args);

        if (!empty($existing_products)) {
            return wc_get_product($existing_products[0]->ID);
        }

        $product = new WC_Product_Subscription();
        $product->set_name($product_name);
        $product->set_status('publish');
        $product->set_catalog_visibility('hidden');
        $product->set_regular_price($price);
        $product->set_subscription_price($price);
        $product->set_subscription_period($item_data['subscription_period'] ?: 'month');
        $product->set_subscription_period_interval($item_data['subscription_interval'] ?: 1);
        $product->add_meta_data('_storefront_product_sync', $item_data['product_id']);

        $product_id = $product->save();

        return $product_id ? $product : false;
    }

    public function add_admin_menu() {
        add_options_page(
            __('Billing Site Settings', 'billing-site-handler'),
            __('Billing Site', 'billing-site-handler'),
            'manage_options',
            'billing-site-settings',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('bsh_settings', 'bsh_shared_secret');
        register_setting('bsh_settings', 'bsh_webhook_urls');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Billing Site Settings', 'billing-site-handler'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('bsh_settings'); ?>
                <?php do_settings_sections('bsh_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Shared Secret', 'billing-site-handler'); ?></th>
                        <td>
                            <input type="password" name="bsh_shared_secret" value="<?php echo esc_attr($this->shared_secret); ?>" class="regular-text" />
                            <p class="description"><?php _e('Shared secret for token validation. Must match storefront configuration.', 'billing-site-handler'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Webhook URLs', 'billing-site-handler'); ?></th>
                        <td>
                            <textarea name="bsh_webhook_urls" class="large-text" rows="5"><?php echo esc_textarea(get_option('bsh_webhook_urls', '')); ?></textarea>
                            <p class="description"><?php _e('One webhook URL per line. These will receive payment status updates.', 'billing-site-handler'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}