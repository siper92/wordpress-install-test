<?php
/**
 * Plugin Name: OTP Customer Authentication
 * Plugin URI: https://example.com
 * Description: One-time password authentication system for customer dashboard access
 * Version: 1.0.0
 * Author: Development Team
 * License: GPL v2 or later
 * Text Domain: otp-customer-auth
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('OCA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('OCA_PLUGIN_URL', plugin_dir_url(__FILE__));

class OTP_Customer_Auth_Plugin {

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }

        include_once OCA_PLUGIN_PATH . 'includes/class-otp-authentication.php';
        include_once OCA_PLUGIN_PATH . 'includes/class-customer-dashboard.php';
        include_once OCA_PLUGIN_PATH . 'includes/class-email-templates.php';

        new OCA_Authentication();
        new OCA_Customer_Dashboard();
        new OCA_Email_Templates();
    }

    public function load_textdomain() {
        load_plugin_textdomain('otp-customer-auth', false,
                              dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p><strong>' .
             __('OTP Customer Authentication requires WooCommerce to be active.', 'otp-customer-auth') .
             '</strong></p></div>';
    }

    public function activate() {
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires WooCommerce to be installed and active.', 'otp-customer-auth'));
        }

        add_rewrite_rule(
            '^customer-login/?$',
            'index.php?customer_login=1',
            'top'
        );

        add_rewrite_rule(
            '^customer-dashboard/?$',
            'index.php?customer_dashboard=1',
            'top'
        );

        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}

new OTP_Customer_Auth_Plugin();