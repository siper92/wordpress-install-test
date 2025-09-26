<?php

if (!defined('ABSPATH')) {
    exit;
}

class BSH_Subscription_Processor {

    public function __construct() {
        add_filter('woocommerce_subscription_payment_method_to_display', array($this, 'display_payment_method'), 10, 3);
        add_action('woocommerce_subscription_renewal_payment_complete', array($this, 'handle_renewal_complete'), 10, 2);
        add_action('woocommerce_subscription_renewal_payment_failed', array($this, 'handle_renewal_failed'), 10, 2);
        add_filter('wcs_renewal_order_created', array($this, 'setup_renewal_order'), 10, 2);
        add_action('wp_ajax_bsh_retry_payment', array($this, 'ajax_retry_payment'));
        add_action('wp_ajax_nopriv_bsh_retry_payment', array($this, 'ajax_retry_payment'));
    }

    public function display_payment_method($payment_method_to_display, $payment_method, $subscription) {
        if ($subscription->get_meta('_storefront_order_id')) {
            return __('Storefront Payment', 'billing-site-handler');
        }

        return $payment_method_to_display;
    }

    public function handle_renewal_complete($subscription, $last_order) {
        if (!$subscription || !$last_order) {
            return;
        }

        $subscription->add_order_note(
            sprintf(__('Renewal payment completed. Order #%s', 'billing-site-handler'), $last_order->get_order_number())
        );

        do_action('bsh_renewal_payment_complete', $subscription, $last_order);
    }

    public function handle_renewal_failed($subscription, $last_order) {
        if (!$subscription) {
            return;
        }

        $retry_count = intval($subscription->get_meta('_renewal_retry_count')) + 1;
        $subscription->update_meta_data('_renewal_retry_count', $retry_count);
        $subscription->save();

        $max_retries = apply_filters('bsh_max_renewal_retries', 3);

        if ($retry_count >= $max_retries) {
            $subscription->add_order_note(
                __('Maximum renewal retries reached. Subscription suspended.', 'billing-site-handler')
            );

            $subscription->update_status('on-hold');
        } else {
            $subscription->add_order_note(
                sprintf(__('Renewal payment failed. Retry %d of %d.', 'billing-site-handler'), $retry_count, $max_retries)
            );

            $retry_date = new DateTime();
            $retry_date->modify('+' . apply_filters('bsh_retry_delay_days', 3) . ' days');

            $subscription->update_dates(array(
                'next_payment' => $retry_date->format('Y-m-d H:i:s')
            ));
        }

        do_action('bsh_renewal_payment_failed', $subscription, $last_order, $retry_count);
    }

    public function setup_renewal_order($renewal_order, $subscription) {
        if (!$renewal_order || !$subscription) {
            return $renewal_order;
        }

        $storefront_order_id = $subscription->get_meta('_storefront_order_id');
        $storefront_url = $subscription->get_meta('_storefront_url');

        if ($storefront_order_id && $storefront_url) {
            $renewal_order->add_meta_data('_storefront_parent_order', $storefront_order_id);
            $renewal_order->add_meta_data('_storefront_url', $storefront_url);
            $renewal_order->save();
        }

        return $renewal_order;
    }

    public function ajax_retry_payment() {
        check_ajax_referer('bsh_retry_payment', 'nonce');

        if (!isset($_POST['subscription_id'])) {
            wp_die(__('Invalid request', 'billing-site-handler'));
        }

        $subscription_id = intval($_POST['subscription_id']);
        $subscription = wcs_get_subscription($subscription_id);

        if (!$subscription) {
            wp_send_json_error(__('Subscription not found', 'billing-site-handler'));
        }

        if (!current_user_can('edit_shop_orders') && $subscription->get_customer_id() !== get_current_user_id()) {
            wp_send_json_error(__('Access denied', 'billing-site-handler'));
        }

        if (!$subscription->can_be_updated_to('active')) {
            wp_send_json_error(__('Subscription cannot be reactivated', 'billing-site-handler'));
        }

        $renewal_order = wcs_create_renewal_order($subscription);

        if (!$renewal_order) {
            wp_send_json_error(__('Failed to create renewal order', 'billing-site-handler'));
        }

        $subscription->delete_meta_data('_renewal_retry_count');
        $subscription->save();

        $checkout_url = $renewal_order->get_checkout_payment_url();

        wp_send_json_success(array(
            'message' => __('Retry order created successfully', 'billing-site-handler'),
            'checkout_url' => $checkout_url,
            'order_id' => $renewal_order->get_id()
        ));
    }

    public static function get_subscription_retry_url($subscription_id) {
        $subscription = wcs_get_subscription($subscription_id);

        if (!$subscription || !$subscription->needs_payment()) {
            return false;
        }

        $renewal_order = wcs_create_renewal_order($subscription);

        if (!$renewal_order) {
            return false;
        }

        return $renewal_order->get_checkout_payment_url();
    }

    public function add_subscription_meta_boxes() {
        add_meta_box(
            'bsh_subscription_info',
            __('Storefront Integration', 'billing-site-handler'),
            array($this, 'render_subscription_meta_box'),
            'shop_subscription',
            'side',
            'default'
        );
    }

    public function render_subscription_meta_box($post) {
        $subscription = wcs_get_subscription($post->ID);

        if (!$subscription) {
            return;
        }

        $storefront_order_id = $subscription->get_meta('_storefront_order_id');
        $storefront_url = $subscription->get_meta('_storefront_url');
        $retry_count = $subscription->get_meta('_renewal_retry_count');

        ?>
        <div class="bsh-subscription-meta">
            <p>
                <strong><?php _e('Storefront Order ID:', 'billing-site-handler'); ?></strong><br>
                <?php echo $storefront_order_id ? esc_html($storefront_order_id) : '—'; ?>
            </p>
            <p>
                <strong><?php _e('Storefront URL:', 'billing-site-handler'); ?></strong><br>
                <?php echo $storefront_url ? '<a href="' . esc_url($storefront_url) . '" target="_blank">' . esc_html($storefront_url) . '</a>' : '—'; ?>
            </p>
            <?php if ($retry_count > 0): ?>
            <p>
                <strong><?php _e('Renewal Retries:', 'billing-site-handler'); ?></strong><br>
                <?php echo intval($retry_count); ?>
            </p>
            <?php endif; ?>

            <?php if ($subscription->needs_payment()): ?>
            <p>
                <a href="<?php echo esc_url($subscription->get_checkout_payment_url()); ?>" class="button button-primary">
                    <?php _e('Pay Now', 'billing-site-handler'); ?>
                </a>
            </p>
            <?php endif; ?>
        </div>
        <?php
    }
}