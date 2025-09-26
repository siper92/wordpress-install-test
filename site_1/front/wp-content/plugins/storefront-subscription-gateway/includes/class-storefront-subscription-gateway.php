<?php

if (!defined('ABSPATH')) {
    exit;
}

class Storefront_Subscription_Gateway extends WC_Payment_Gateway {

    private $billing_site_url;
    private $shared_secret;
    private $token_expiry = 1800; // 30 minutes

    public function __construct() {
        $this->id = 'storefront_subscription';
        $this->icon = '';
        $this->has_fields = false;
        $this->method_title = __('Subscription Billing', 'storefront-subscription-gateway');
        $this->method_description = __('Redirects subscription orders to secure billing site for payment processing', 'storefront-subscription-gateway');

        $this->supports = array(
            'products',
            'subscriptions',
            'subscription_cancellation',
            'subscription_suspension',
            'subscription_reactivation'
        );

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->billing_site_url = $this->get_option('billing_site_url');
        $this->shared_secret = $this->get_option('shared_secret');
        $this->enabled = $this->get_option('enabled');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_' . $this->id, array($this, 'handle_webhook'));
        add_action('woocommerce_order_status_failed', array($this, 'add_retry_payment_button'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'storefront-subscription-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable Storefront Subscription Gateway', 'storefront-subscription-gateway'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'storefront-subscription-gateway'),
                'type' => 'text',
                'description' => __('This controls the title displayed during checkout.', 'storefront-subscription-gateway'),
                'default' => __('Subscription Payment', 'storefront-subscription-gateway'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'storefront-subscription-gateway'),
                'type' => 'textarea',
                'description' => __('Payment method description that customers will see on checkout.', 'storefront-subscription-gateway'),
                'default' => __('Secure subscription payment processing.', 'storefront-subscription-gateway'),
                'desc_tip' => true,
            ),
            'billing_site_url' => array(
                'title' => __('Billing Site URL', 'storefront-subscription-gateway'),
                'type' => 'url',
                'description' => __('URL of the billing site that will process subscriptions.', 'storefront-subscription-gateway'),
                'default' => '',
                'desc_tip' => true,
            ),
            'shared_secret' => array(
                'title' => __('Shared Secret', 'storefront-subscription-gateway'),
                'type' => 'password',
                'description' => __('Shared secret key for HMAC token signing. Must match billing site configuration.', 'storefront-subscription-gateway'),
                'default' => '',
                'desc_tip' => true,
            )
        );
    }

    public function is_available() {
        if (!$this->enabled || empty($this->billing_site_url) || empty($this->shared_secret)) {
            return false;
        }

        if (!WC_Subscriptions_Cart::cart_contains_subscription()) {
            return false;
        }

        return parent::is_available();
    }

    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        if (!$order) {
            return array(
                'result' => 'failure',
                'messages' => __('Order not found.', 'storefront-subscription-gateway')
            );
        }

        try {
            $token = $this->generate_secure_token($order);

            $order->update_status('pending', __('Redirecting to billing site for subscription processing', 'storefront-subscription-gateway'));
            $order->add_order_note(__('Customer redirected to billing site for payment.', 'storefront-subscription-gateway'));

            $redirect_url = add_query_arg(array(
                'token' => $token,
                'return_url' => $this->get_return_url($order),
                'language' => $this->get_customer_language()
            ), trailingslashit($this->billing_site_url) . 'subscription-checkout/');

            return array(
                'result' => 'success',
                'redirect' => $redirect_url
            );

        } catch (Exception $e) {
            wc_add_notice(__('Payment processing error: ', 'storefront-subscription-gateway') . $e->getMessage(), 'error');
            return array(
                'result' => 'failure'
            );
        }
    }

    private function generate_secure_token($order) {
        if (empty($this->shared_secret)) {
            throw new Exception(__('Shared secret not configured.', 'storefront-subscription-gateway'));
        }

        $timestamp = time();
        $payload = array(
            'order_id' => $order->get_id(),
            'customer_email' => $order->get_billing_email(),
            'customer_first_name' => $order->get_billing_first_name(),
            'customer_last_name' => $order->get_billing_last_name(),
            'billing_address' => array(
                'first_name' => $order->get_billing_first_name(),
                'last_name' => $order->get_billing_last_name(),
                'company' => $order->get_billing_company(),
                'address_1' => $order->get_billing_address_1(),
                'address_2' => $order->get_billing_address_2(),
                'city' => $order->get_billing_city(),
                'state' => $order->get_billing_state(),
                'postcode' => $order->get_billing_postcode(),
                'country' => $order->get_billing_country(),
                'email' => $order->get_billing_email(),
                'phone' => $order->get_billing_phone()
            ),
            'order_total' => $order->get_total(),
            'currency' => $order->get_currency(),
            'items' => $this->get_order_items($order),
            'timestamp' => $timestamp,
            'expires' => $timestamp + $this->token_expiry,
            'storefront_url' => home_url(),
            'language' => get_locale()
        );

        $encoded_payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', $encoded_payload, $this->shared_secret);

        return $encoded_payload . '.' . $signature;
    }

    private function get_order_items($order) {
        $items = array();

        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();

            if (!$product) {
                continue;
            }

            $item_data = array(
                'name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'price' => $item->get_total(),
                'product_id' => $product->get_id(),
                'is_subscription' => false,
                'subscription_period' => '',
                'subscription_interval' => 1
            );

            if (class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription($product)) {
                $item_data['is_subscription'] = true;
                $item_data['subscription_period'] = WC_Subscriptions_Product::get_period($product);
                $item_data['subscription_interval'] = WC_Subscriptions_Product::get_interval($product);
            }

            $items[] = $item_data;
        }

        return $items;
    }

    public function handle_webhook() {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

        if (!$this->verify_webhook_signature($payload, $signature)) {
            wp_die(__('Invalid signature', 'storefront-subscription-gateway'), 'Unauthorized', array('response' => 401));
        }

        $data = json_decode($payload, true);

        if (!$data || !isset($data['order_id'])) {
            wp_die(__('Invalid webhook data', 'storefront-subscription-gateway'), 'Bad Request', array('response' => 400));
        }

        $order = wc_get_order($data['order_id']);

        if (!$order) {
            wp_die(__('Order not found', 'storefront-subscription-gateway'), 'Not Found', array('response' => 404));
        }

        switch ($data['status']) {
            case 'completed':
                $transaction_id = isset($data['transaction_id']) ? $data['transaction_id'] : '';
                $order->payment_complete($transaction_id);
                $order->add_order_note(__('Payment completed on billing site', 'storefront-subscription-gateway'));
                break;

            case 'processing':
                $order->update_status('processing', __('Payment processing on billing site', 'storefront-subscription-gateway'));
                break;

            case 'failed':
                $order->update_status('failed', __('Payment failed on billing site', 'storefront-subscription-gateway'));
                break;

            case 'cancelled':
                $order->update_status('cancelled', __('Payment cancelled by customer', 'storefront-subscription-gateway'));
                break;

            default:
                wp_die(__('Unknown status', 'storefront-subscription-gateway'), 'Bad Request', array('response' => 400));
        }

        http_response_code(200);
        echo 'OK';
        exit;
    }

    private function verify_webhook_signature($payload, $signature) {
        if (empty($this->shared_secret) || empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $this->shared_secret);
        return hash_equals($expected, $signature);
    }

    public function process_retry_payment($order_id) {
        $order = wc_get_order($order_id);

        if (!$order || !in_array($order->get_status(), array('failed', 'pending'))) {
            return false;
        }

        try {
            $token = $this->generate_secure_token($order);

            $retry_url = add_query_arg(array(
                'token' => $token,
                'retry' => 'true',
                'return_url' => $order->get_checkout_order_received_url(),
                'language' => $this->get_customer_language()
            ), trailingslashit($this->billing_site_url) . 'subscription-checkout/');

            return $retry_url;

        } catch (Exception $e) {
            return false;
        }
    }

    public function add_retry_payment_button($order_id) {
        $order = wc_get_order($order_id);

        if (!$order || $order->get_payment_method() !== $this->id) {
            return;
        }

        $retry_url = $this->process_retry_payment($order_id);

        if ($retry_url) {
            $order->add_order_note(sprintf(
                __('Payment retry link: <a href="%s" target="_blank">Retry Payment</a>', 'storefront-subscription-gateway'),
                esc_url($retry_url)
            ));
        }
    }

    private function get_customer_language() {
        if (isset($_GET['lang'])) {
            return sanitize_text_field($_GET['lang']);
        }

        $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $preferred_language = substr($accept_language, 0, 2);

        $supported_languages = array('en', 'es', 'fr', 'de', 'it', 'nl', 'pt');

        return in_array($preferred_language, $supported_languages) ? $preferred_language : 'en';
    }
}