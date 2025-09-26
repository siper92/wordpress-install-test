<?php
/**
 * Plugin Name: Billing Site Handler
 * Plugin URI: https://example.com
 * Description: Handles token validation and subscription processing for the billing site
 * Version: 1.0.0
 * Author: Development Team
 * License: GPL v2 or later
 * Text Domain: billing-site-handler
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BSH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BSH_PLUGIN_URL', plugin_dir_url(__FILE__));

class Billing_Site_Handler_Plugin {

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    public function init() {
        if (!class_exists('WC_Subscriptions') || !class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'requirements_missing_notice'));
            return;
        }

        include_once BSH_PLUGIN_PATH . 'includes/class-token-handler.php';
        include_once BSH_PLUGIN_PATH . 'includes/class-webhook-sender.php';
        include_once BSH_PLUGIN_PATH . 'includes/class-subscription-processor.php';

        new BSH_Token_Handler();
        new BSH_Webhook_Sender();
        new BSH_Subscription_Processor();
    }

    public function load_textdomain() {
        load_plugin_textdomain('billing-site-handler', false,
                              dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function requirements_missing_notice() {
        echo '<div class="error"><p><strong>' .
             __('Billing Site Handler requires WooCommerce and WooCommerce Subscriptions to be active.', 'billing-site-handler') .
             '</strong></p></div>';
    }

    public function activate() {
        if (!class_exists('WooCommerce') || !class_exists('WC_Subscriptions')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires WooCommerce and WooCommerce Subscriptions to be installed and active.', 'billing-site-handler'));
        }

        add_rewrite_rule(
            '^subscription-checkout/?$',
            'index.php?subscription_checkout=1',
            'top'
        );

        flush_rewrite_rules();
    }
}

new Billing_Site_Handler_Plugin();