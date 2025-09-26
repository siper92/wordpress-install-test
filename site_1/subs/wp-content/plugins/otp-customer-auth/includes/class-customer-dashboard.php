<?php

if (!defined('ABSPATH')) {
    exit;
}

class OCA_Customer_Dashboard {

    public function __construct() {
        add_action('template_redirect', array($this, 'handle_customer_dashboard'));
        add_action('wp_ajax_get_subscription_details', array($this, 'ajax_get_subscription_details'));
        add_action('wp_ajax_cancel_subscription', array($this, 'ajax_cancel_subscription'));
        add_action('wp_ajax_update_payment_method', array($this, 'ajax_update_payment_method'));
    }

    public function handle_customer_dashboard() {
        if (!get_query_var('customer_dashboard')) {
            return;
        }

        if (!is_user_logged_in()) {
            wp_redirect(home_url('/customer-login/'));
            exit;
        }

        $this->render_dashboard_page();
        exit;
    }

    public function render_dashboard_page() {
        $current_user = wp_get_current_user();
        $subscriptions = wcs_get_users_subscriptions($current_user->ID);

        get_header();
        ?>
        <div class="oca-dashboard-container">
            <div class="oca-dashboard-header">
                <h1><?php printf(__('Welcome, %s', 'otp-customer-auth'), $current_user->display_name); ?></h1>
                <div class="oca-dashboard-actions">
                    <button id="oca-logout" class="button">
                        <?php _e('Logout', 'otp-customer-auth'); ?>
                    </button>
                </div>
            </div>

            <div class="oca-dashboard-content">
                <div class="oca-section">
                    <h2><?php _e('My Subscriptions', 'otp-customer-auth'); ?></h2>

                    <?php if (empty($subscriptions)): ?>
                        <p><?php _e('You have no active subscriptions.', 'otp-customer-auth'); ?></p>
                    <?php else: ?>
                        <div class="oca-subscriptions-grid">
                            <?php foreach ($subscriptions as $subscription): ?>
                                <?php $this->render_subscription_card($subscription); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="oca-section">
                    <h2><?php _e('Payment History', 'otp-customer-auth'); ?></h2>
                    <div id="oca-payment-history">
                        <p><?php _e('Loading payment history...', 'otp-customer-auth'); ?></p>
                    </div>
                </div>

                <div class="oca-section">
                    <h2><?php _e('Account Information', 'otp-customer-auth'); ?></h2>
                    <div class="oca-account-info">
                        <p><strong><?php _e('Email:', 'otp-customer-auth'); ?></strong> <?php echo esc_html($current_user->user_email); ?></p>
                        <p><strong><?php _e('Member since:', 'otp-customer-auth'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($current_user->user_registered)); ?></p>
                        <p><strong><?php _e('Last login:', 'otp-customer-auth'); ?></strong>
                            <?php
                            $last_login = get_user_meta($current_user->ID, '_oca_last_login', true);
                            echo $last_login ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_login) : __('N/A', 'otp-customer-auth');
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#oca-logout').on('click', function() {
                if (confirm('<?php _e('Are you sure you want to logout?', 'otp-customer-auth'); ?>')) {
                    $.post(oca_ajax.ajaxurl, {
                        action: 'logout_customer',
                        nonce: oca_ajax.nonce
                    }, function(response) {
                        if (response.success) {
                            window.location.href = response.data.redirect_url;
                        }
                    });
                }
            });

            $('.oca-subscription-card').on('click', '.oca-view-details', function(e) {
                e.preventDefault();
                var subscriptionId = $(this).data('subscription-id');
                loadSubscriptionDetails(subscriptionId);
            });

            $('.oca-subscription-card').on('click', '.oca-cancel-subscription', function(e) {
                e.preventDefault();

                if (!confirm('<?php _e('Are you sure you want to cancel this subscription?', 'otp-customer-auth'); ?>')) {
                    return;
                }

                var subscriptionId = $(this).data('subscription-id');
                cancelSubscription(subscriptionId);
            });

            loadPaymentHistory();

            function loadSubscriptionDetails(subscriptionId) {
                $.post(oca_ajax.ajaxurl, {
                    action: 'get_subscription_details',
                    subscription_id: subscriptionId,
                    nonce: oca_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        showModal('<?php _e('Subscription Details', 'otp-customer-auth'); ?>', response.data.html);
                    } else {
                        alert(response.data.message || '<?php _e('Failed to load subscription details', 'otp-customer-auth'); ?>');
                    }
                });
            }

            function cancelSubscription(subscriptionId) {
                $.post(oca_ajax.ajaxurl, {
                    action: 'cancel_subscription',
                    subscription_id: subscriptionId,
                    nonce: oca_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message || '<?php _e('Failed to cancel subscription', 'otp-customer-auth'); ?>');
                    }
                });
            }

            function loadPaymentHistory() {
                var subscriptionIds = [];
                $('.oca-subscription-card').each(function() {
                    subscriptionIds.push($(this).data('subscription-id'));
                });

                if (subscriptionIds.length === 0) {
                    $('#oca-payment-history').html('<p><?php _e('No payment history available.', 'otp-customer-auth'); ?></p>');
                    return;
                }

                $.post(oca_ajax.ajaxurl, {
                    action: 'get_payment_history',
                    subscription_ids: subscriptionIds,
                    nonce: oca_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        $('#oca-payment-history').html(response.data.html);
                    } else {
                        $('#oca-payment-history').html('<p><?php _e('Failed to load payment history.', 'otp-customer-auth'); ?></p>');
                    }
                });
            }

            function showModal(title, content) {
                var modal = $('<div class="oca-modal"><div class="oca-modal-content"><div class="oca-modal-header"><h3>' + title + '</h3><span class="oca-modal-close">&times;</span></div><div class="oca-modal-body">' + content + '</div></div></div>');

                $('body').append(modal);
                modal.show();

                modal.on('click', '.oca-modal-close', function() {
                    modal.remove();
                });

                modal.on('click', function(e) {
                    if (e.target === this) {
                        modal.remove();
                    }
                });
            }
        });
        </script>

        <style>
        .oca-dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .oca-dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .oca-dashboard-header h1 {
            margin: 0;
            color: #333;
        }

        .oca-section {
            margin-bottom: 40px;
        }

        .oca-section h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 10px;
        }

        .oca-subscriptions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .oca-subscription-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }

        .oca-subscription-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .oca-subscription-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .oca-status-active {
            background: #d4edda;
            color: #155724;
        }

        .oca-status-cancelled,
        .oca-status-expired {
            background: #f8d7da;
            color: #721c24;
        }

        .oca-status-on-hold {
            background: #fff3cd;
            color: #856404;
        }

        .oca-subscription-actions {
            margin-top: 15px;
        }

        .oca-subscription-actions .button {
            margin-right: 10px;
            margin-bottom: 5px;
        }

        .oca-account-info p {
            margin-bottom: 10px;
        }

        .oca-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .oca-modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .oca-modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .oca-modal-header h3 {
            margin: 0;
        }

        .oca-modal-close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }

        .oca-modal-close:hover {
            color: #000;
        }

        .oca-modal-body {
            padding: 20px;
        }

        @media (max-width: 768px) {
            .oca-dashboard-header {
                flex-direction: column;
                gap: 10px;
            }

            .oca-subscriptions-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
        get_footer();
    }

    private function render_subscription_card($subscription) {
        $status = $subscription->get_status();
        $status_class = 'oca-status-' . $status;
        $next_payment = $subscription->get_date('next_payment');
        $total = $subscription->get_total();
        $currency = $subscription->get_currency();
        ?>
        <div class="oca-subscription-card" data-subscription-id="<?php echo $subscription->get_id(); ?>">
            <div class="oca-subscription-status <?php echo $status_class; ?>">
                <?php echo wcs_get_subscription_status_name($status); ?>
            </div>

            <h3><?php echo esc_html($subscription->get_formatted_billing_period()); ?></h3>

            <p><strong><?php _e('Amount:', 'otp-customer-auth'); ?></strong> <?php echo wc_price($total, array('currency' => $currency)); ?></p>

            <?php if ($next_payment): ?>
                <p><strong><?php _e('Next Payment:', 'otp-customer-auth'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($next_payment)); ?></p>
            <?php endif; ?>

            <p><strong><?php _e('Start Date:', 'otp-customer-auth'); ?></strong> <?php echo date_i18n(get_option('date_format'), strtotime($subscription->get_date('date_created'))); ?></p>

            <div class="oca-subscription-actions">
                <button class="button oca-view-details" data-subscription-id="<?php echo $subscription->get_id(); ?>">
                    <?php _e('View Details', 'otp-customer-auth'); ?>
                </button>

                <?php if ($subscription->can_be_updated_to('cancelled')): ?>
                    <button class="button oca-cancel-subscription" data-subscription-id="<?php echo $subscription->get_id(); ?>">
                        <?php _e('Cancel', 'otp-customer-auth'); ?>
                    </button>
                <?php endif; ?>

                <?php if ($subscription->needs_payment()): ?>
                    <a href="<?php echo esc_url($subscription->get_checkout_payment_url()); ?>" class="button button-primary">
                        <?php _e('Pay Now', 'otp-customer-auth'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function ajax_get_subscription_details() {
        check_ajax_referer('oca_nonce', 'nonce');

        $subscription_id = intval($_POST['subscription_id']);
        $subscription = wcs_get_subscription($subscription_id);

        if (!$subscription || $subscription->get_customer_id() !== get_current_user_id()) {
            wp_send_json_error(array(
                'message' => __('Subscription not found or access denied.', 'otp-customer-auth')
            ));
        }

        ob_start();
        ?>
        <div class="oca-subscription-details">
            <table class="oca-details-table">
                <tr>
                    <td><strong><?php _e('Subscription ID:', 'otp-customer-auth'); ?></strong></td>
                    <td>#<?php echo $subscription->get_id(); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Status:', 'otp-customer-auth'); ?></strong></td>
                    <td><?php echo wcs_get_subscription_status_name($subscription->get_status()); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Amount:', 'otp-customer-auth'); ?></strong></td>
                    <td><?php echo wc_price($subscription->get_total(), array('currency' => $subscription->get_currency())); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Billing Cycle:', 'otp-customer-auth'); ?></strong></td>
                    <td><?php echo $subscription->get_formatted_billing_period(); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Start Date:', 'otp-customer-auth'); ?></strong></td>
                    <td><?php echo date_i18n(get_option('date_format'), strtotime($subscription->get_date('date_created'))); ?></td>
                </tr>
                <?php if ($subscription->get_date('next_payment')): ?>
                <tr>
                    <td><strong><?php _e('Next Payment:', 'otp-customer-auth'); ?></strong></td>
                    <td><?php echo date_i18n(get_option('date_format'), strtotime($subscription->get_date('next_payment'))); ?></td>
                </tr>
                <?php endif; ?>
            </table>

            <h4><?php _e('Subscription Items', 'otp-customer-auth'); ?></h4>
            <div class="oca-subscription-items">
                <?php foreach ($subscription->get_items() as $item): ?>
                    <div class="oca-item">
                        <strong><?php echo esc_html($item->get_name()); ?></strong>
                        <span>x <?php echo $item->get_quantity(); ?></span>
                        <span class="price"><?php echo wc_price($item->get_total(), array('currency' => $subscription->get_currency())); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <style>
        .oca-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .oca-details-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .oca-details-table td:first-child {
            width: 40%;
        }

        .oca-subscription-items .oca-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .oca-subscription-items .oca-item:last-child {
            border-bottom: none;
        }

        .oca-subscription-items .price {
            font-weight: bold;
        }
        </style>
        <?php
        $html = ob_get_clean();

        wp_send_json_success(array('html' => $html));
    }

    public function ajax_cancel_subscription() {
        check_ajax_referer('oca_nonce', 'nonce');

        $subscription_id = intval($_POST['subscription_id']);
        $subscription = wcs_get_subscription($subscription_id);

        if (!$subscription || $subscription->get_customer_id() !== get_current_user_id()) {
            wp_send_json_error(array(
                'message' => __('Subscription not found or access denied.', 'otp-customer-auth')
            ));
        }

        if (!$subscription->can_be_updated_to('cancelled')) {
            wp_send_json_error(array(
                'message' => __('This subscription cannot be cancelled.', 'otp-customer-auth')
            ));
        }

        $subscription->update_status('cancelled', __('Cancelled by customer via dashboard.', 'otp-customer-auth'));

        wp_send_json_success(array(
            'message' => __('Subscription cancelled successfully.', 'otp-customer-auth')
        ));
    }
}