<?php
/**
 * Plugin Name: Storefront Subscription Gateway
 * Plugin URI: https://example.com
 * Description: Custom payment gateway that redirects subscription orders to a secure billing site
 * Version: 1.0.0
 * Author: Development Team
 * License: GPL v2 or later
 * Text Domain: storefront-subscription-gateway
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SSG_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SSG_PLUGIN_URL', plugin_dir_url(__FILE__));

class Storefront_Subscription_Gateway_Plugin {

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    public function init() {
        if (!class_exists('WC_Payment_Gateway')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }

        include_once SSG_PLUGIN_PATH . 'includes/class-storefront-subscription-gateway.php';
        add_filter('woocommerce_payment_gateways', array($this, 'add_gateway'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('storefront-subscription-gateway', false,
                              dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function add_gateway($gateways) {
        $gateways[] = 'Storefront_Subscription_Gateway';
        return $gateways;
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p><strong>' .
             __('Storefront Subscription Gateway requires WooCommerce to be active.', 'storefront-subscription-gateway') .
             '</strong></p></div>';
    }

    public function activate() {
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires WooCommerce to be installed and active.', 'storefront-subscription-gateway'));
        }
    }
}

new Storefront_Subscription_Gateway_Plugin();