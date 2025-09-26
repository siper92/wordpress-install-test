<?php

if (!defined('ABSPATH')) {
    exit;
}

class BSH_Webhook_Sender {

    private $shared_secret;

    public function __construct() {
        $this->shared_secret = get_option('bsh_shared_secret', '');

        add_action('woocommerce_subscription_status_changed', array($this, 'send_subscription_webhook'), 10, 3);
        add_action('woocommerce_subscription_payment_complete', array($this, 'send_payment_complete_webhook'), 10, 1);
        add_action('woocommerce_subscription_payment_failed', array($this, 'send_payment_failed_webhook'), 10, 2);
        add_action('woocommerce_order_status_changed', array($this, 'send_order_status_webhook'), 10, 4);
    }

    public function send_subscription_webhook($subscription, $new_status, $old_status) {
        if (!$subscription || $new_status === $old_status) {
            return;
        }

        $storefront_order_id = $subscription->get_meta('_storefront_order_id');
        $storefront_url = $subscription->get_meta('_storefront_url');

        if (!$storefront_order_id || !$storefront_url) {
            return;
        }

        $webhook_data = array(
            'type' => 'subscription_status',
            'order_id' => $storefront_order_id,
            'subscription_id' => $subscription->get_id(),
            'old_status' => $old_status,
            'new_status' => $new_status,
            'timestamp' => time()
        );

        $webhook_url = trailingslashit($storefront_url) . 'wc-api/storefront_subscription/';
        $this->send_webhook($webhook_url, $webhook_data);
    }

    public function send_payment_complete_webhook($subscription) {
        if (!$subscription) {
            return;
        }

        $storefront_order_id = $subscription->get_meta('_storefront_order_id');
        $storefront_url = $subscription->get_meta('_storefront_url');

        if (!$storefront_order_id || !$storefront_url) {
            return;
        }

        $last_order = $subscription->get_last_order('all', 'any');
        $transaction_id = $last_order ? $last_order->get_transaction_id() : '';

        $webhook_data = array(
            'type' => 'payment_complete',
            'order_id' => $storefront_order_id,
            'subscription_id' => $subscription->get_id(),
            'status' => 'completed',
            'transaction_id' => $transaction_id,
            'amount' => $subscription->get_total(),
            'timestamp' => time()
        );

        $webhook_url = trailingslashit($storefront_url) . 'wc-api/storefront_subscription/';
        $this->send_webhook($webhook_url, $webhook_data);
    }

    public function send_payment_failed_webhook($subscription, $renewal_order) {
        if (!$subscription) {
            return;
        }

        $storefront_order_id = $subscription->get_meta('_storefront_order_id');
        $storefront_url = $subscription->get_meta('_storefront_url');

        if (!$storefront_order_id || !$storefront_url) {
            return;
        }

        $webhook_data = array(
            'type' => 'payment_failed',
            'order_id' => $storefront_order_id,
            'subscription_id' => $subscription->get_id(),
            'status' => 'failed',
            'renewal_order_id' => $renewal_order ? $renewal_order->get_id() : null,
            'timestamp' => time()
        );

        $webhook_url = trailingslashit($storefront_url) . 'wc-api/storefront_subscription/';
        $this->send_webhook($webhook_url, $webhook_data);
    }

    public function send_order_status_webhook($order_id, $old_status, $new_status, $order) {
        if (!$order || $new_status === $old_status) {
            return;
        }

        if (!wcs_order_contains_subscription($order, 'any')) {
            return;
        }

        $subscriptions = wcs_get_subscriptions_for_order($order);

        foreach ($subscriptions as $subscription) {
            $storefront_order_id = $subscription->get_meta('_storefront_order_id');
            $storefront_url = $subscription->get_meta('_storefront_url');

            if (!$storefront_order_id || !$storefront_url) {
                continue;
            }

            $webhook_data = array(
                'type' => 'order_status',
                'order_id' => $storefront_order_id,
                'subscription_id' => $subscription->get_id(),
                'billing_order_id' => $order_id,
                'old_status' => $old_status,
                'new_status' => $new_status,
                'transaction_id' => $order->get_transaction_id(),
                'timestamp' => time()
            );

            $webhook_url = trailingslashit($storefront_url) . 'wc-api/storefront_subscription/';
            $this->send_webhook($webhook_url, $webhook_data);
        }
    }

    private function send_webhook($url, $data) {
        if (empty($this->shared_secret)) {
            error_log('BSH: Cannot send webhook - shared secret not configured');
            return false;
        }

        $payload = json_encode($data);
        $signature = hash_hmac('sha256', $payload, $this->shared_secret);

        $args = array(
            'body' => $payload,
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-Signature' => $signature,
                'User-Agent' => 'BillingWP/1.0'
            ),
            'timeout' => 30,
            'sslverify' => true
        );

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            error_log('BSH: Webhook failed - ' . $response->get_error_message() . ' - URL: ' . $url);
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code !== 200) {
            error_log('BSH: Webhook returned non-200 response - Code: ' . $response_code . ' - URL: ' . $url);
            return false;
        }

        return true;
    }

    public function send_additional_webhooks($webhook_data) {
        $webhook_urls = get_option('bsh_webhook_urls', '');

        if (empty($webhook_urls)) {
            return;
        }

        $urls = array_filter(array_map('trim', explode("\n", $webhook_urls)));

        foreach ($urls as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $this->send_webhook($url, $webhook_data);
            }
        }
    }
}