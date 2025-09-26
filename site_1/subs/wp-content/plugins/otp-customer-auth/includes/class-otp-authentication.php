<?php

if (!defined('ABSPATH')) {
    exit;
}

class OCA_Authentication {

    private $otp_expiry = 900; // 15 minutes
    private $max_attempts = 3;
    private $rate_limit_window = 300; // 5 minutes

    public function __construct() {
        add_action('init', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_customer_login'));

        add_action('wp_ajax_nopriv_request_otp', array($this, 'ajax_request_otp'));
        add_action('wp_ajax_nopriv_verify_otp', array($this, 'ajax_verify_otp'));
        add_action('wp_ajax_logout_customer', array($this, 'ajax_logout_customer'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'add_custom_styles'));
    }

    public function add_query_vars() {
        global $wp;
        $wp->add_query_var('customer_login');
        $wp->add_query_var('customer_dashboard');
    }

    public function handle_customer_login() {
        if (!get_query_var('customer_login')) {
            return;
        }

        $this->render_login_page();
        exit;
    }

    public function render_login_page() {
        get_header();
        ?>
        <div class="oca-login-container">
            <div class="oca-login-form">
                <h2><?php _e('Customer Dashboard Access', 'otp-customer-auth'); ?></h2>

                <div id="oca-step-email" class="oca-step">
                    <p><?php _e('Enter your email address to receive a login code:', 'otp-customer-auth'); ?></p>
                    <form id="oca-email-form">
                        <p>
                            <input type="email" id="oca-email" name="email" placeholder="<?php _e('Your email address', 'otp-customer-auth'); ?>" required>
                        </p>
                        <p>
                            <button type="submit" class="button button-primary">
                                <?php _e('Send Login Code', 'otp-customer-auth'); ?>
                            </button>
                        </p>
                        <div id="oca-email-message" class="oca-message"></div>
                    </form>
                </div>

                <div id="oca-step-otp" class="oca-step" style="display: none;">
                    <p><?php _e('Enter the 6-digit code sent to your email:', 'otp-customer-auth'); ?></p>
                    <form id="oca-otp-form">
                        <p>
                            <input type="text" id="oca-otp-code" name="otp_code" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>
                            <input type="hidden" id="oca-verify-email" name="email">
                        </p>
                        <p>
                            <button type="submit" class="button button-primary">
                                <?php _e('Verify & Login', 'otp-customer-auth'); ?>
                            </button>
                            <button type="button" id="oca-back-to-email" class="button">
                                <?php _e('Back', 'otp-customer-auth'); ?>
                            </button>
                        </p>
                        <div id="oca-otp-message" class="oca-message"></div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#oca-email-form').on('submit', function(e) {
                e.preventDefault();

                var email = $('#oca-email').val();
                var $message = $('#oca-email-message');
                var $button = $(this).find('button[type="submit"]');

                $button.prop('disabled', true).text('<?php _e('Sending...', 'otp-customer-auth'); ?>');
                $message.removeClass('success error').empty();

                $.post(oca_ajax.ajaxurl, {
                    action: 'request_otp',
                    email: email,
                    nonce: oca_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        $message.addClass('success').text(response.data.message);
                        $('#oca-verify-email').val(email);
                        $('#oca-step-email').hide();
                        $('#oca-step-otp').show();
                        $('#oca-otp-code').focus();
                    } else {
                        $message.addClass('error').text(response.data.message || '<?php _e('Failed to send login code', 'otp-customer-auth'); ?>');
                    }
                }).always(function() {
                    $button.prop('disabled', false).text('<?php _e('Send Login Code', 'otp-customer-auth'); ?>');
                });
            });

            $('#oca-otp-form').on('submit', function(e) {
                e.preventDefault();

                var email = $('#oca-verify-email').val();
                var otpCode = $('#oca-otp-code').val();
                var $message = $('#oca-otp-message');
                var $button = $(this).find('button[type="submit"]');

                $button.prop('disabled', true).text('<?php _e('Verifying...', 'otp-customer-auth'); ?>');
                $message.removeClass('success error').empty();

                $.post(oca_ajax.ajaxurl, {
                    action: 'verify_otp',
                    email: email,
                    otp_code: otpCode,
                    nonce: oca_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        $message.addClass('success').text(response.data.message);
                        window.location.href = response.data.redirect_url;
                    } else {
                        $message.addClass('error').text(response.data.message || '<?php _e('Invalid login code', 'otp-customer-auth'); ?>');
                    }
                }).always(function() {
                    $button.prop('disabled', false).text('<?php _e('Verify & Login', 'otp-customer-auth'); ?>');
                });
            });

            $('#oca-back-to-email').on('click', function() {
                $('#oca-step-otp').hide();
                $('#oca-step-email').show();
                $('#oca-email').focus();
            });

            $('#oca-otp-code').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            });
        });
        </script>
        <?php
        get_footer();
    }

    public function ajax_request_otp() {
        check_ajax_referer('oca_nonce', 'nonce');

        $email = sanitize_email($_POST['email']);

        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Invalid email address.', 'otp-customer-auth')
            ));
        }

        if ($this->is_rate_limited($email)) {
            wp_send_json_error(array(
                'message' => __('Too many requests. Please wait before requesting another code.', 'otp-customer-auth')
            ));
        }

        $customer = get_user_by('email', $email);
        if (!$customer) {
            wp_send_json_error(array(
                'message' => __('No account found with this email address.', 'otp-customer-auth')
            ));
        }

        $subscriptions = wcs_get_users_subscriptions($customer->ID);
        if (empty($subscriptions)) {
            wp_send_json_error(array(
                'message' => __('No active subscriptions found for this email.', 'otp-customer-auth')
            ));
        }

        $result = $this->generate_and_send_otp($email, $customer);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
        }

        $this->set_rate_limit($email);

        wp_send_json_success(array(
            'message' => __('Login code sent to your email address.', 'otp-customer-auth')
        ));
    }

    public function ajax_verify_otp() {
        check_ajax_referer('oca_nonce', 'nonce');

        $email = sanitize_email($_POST['email']);
        $otp_code = sanitize_text_field($_POST['otp_code']);

        if (!is_email($email) || empty($otp_code)) {
            wp_send_json_error(array(
                'message' => __('Invalid email or code.', 'otp-customer-auth')
            ));
        }

        $result = $this->verify_otp($email, $otp_code);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
        }

        wp_send_json_success(array(
            'message' => __('Login successful! Redirecting...', 'otp-customer-auth'),
            'redirect_url' => home_url('/customer-dashboard/')
        ));
    }

    public function ajax_logout_customer() {
        check_ajax_referer('oca_nonce', 'nonce');

        wp_logout();

        wp_send_json_success(array(
            'message' => __('Logged out successfully.', 'otp-customer-auth'),
            'redirect_url' => home_url('/customer-login/')
        ));
    }

    private function generate_and_send_otp($email, $customer) {
        $otp_code = $this->generate_otp_code();

        $otp_data = array(
            'code' => $otp_code,
            'expires' => time() + $this->otp_expiry,
            'attempts' => 0,
            'created' => time()
        );

        update_user_meta($customer->ID, '_oca_otp_code', $otp_data);

        $sent = $this->send_otp_email($email, $otp_code, $customer->display_name);

        if (!$sent) {
            return new WP_Error('email_failed', __('Failed to send email. Please try again.', 'otp-customer-auth'));
        }

        return true;
    }

    private function verify_otp($email, $otp_code) {
        $customer = get_user_by('email', $email);
        if (!$customer) {
            return new WP_Error('customer_not_found', __('Customer not found.', 'otp-customer-auth'));
        }

        $stored_otp = get_user_meta($customer->ID, '_oca_otp_code', true);

        if (!$stored_otp || !is_array($stored_otp)) {
            return new WP_Error('otp_not_found', __('No login code found. Please request a new one.', 'otp-customer-auth'));
        }

        if ($stored_otp['expires'] < time()) {
            delete_user_meta($customer->ID, '_oca_otp_code');
            return new WP_Error('otp_expired', __('Login code has expired. Please request a new one.', 'otp-customer-auth'));
        }

        if ($stored_otp['attempts'] >= $this->max_attempts) {
            delete_user_meta($customer->ID, '_oca_otp_code');
            return new WP_Error('too_many_attempts', __('Too many failed attempts. Please request a new code.', 'otp-customer-auth'));
        }

        if ($stored_otp['code'] !== $otp_code) {
            $stored_otp['attempts']++;
            update_user_meta($customer->ID, '_oca_otp_code', $stored_otp);

            $remaining = $this->max_attempts - $stored_otp['attempts'];
            return new WP_Error('invalid_otp', sprintf(
                __('Invalid login code. %d attempts remaining.', 'otp-customer-auth'),
                $remaining
            ));
        }

        delete_user_meta($customer->ID, '_oca_otp_code');

        wp_set_current_user($customer->ID);
        wp_set_auth_cookie($customer->ID, true);

        update_user_meta($customer->ID, '_oca_last_login', time());

        return true;
    }

    private function generate_otp_code() {
        return str_pad(wp_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function send_otp_email($email, $otp_code, $name) {
        $subject = __('Your login code', 'otp-customer-auth');

        $message = sprintf(
            __('Hello %s,

Your one-time login code is: %s

This code will expire in 15 minutes.

If you did not request this code, please ignore this email.

Best regards,
Customer Support', 'otp-customer-auth'),
            $name,
            $otp_code
        );

        $headers = array('Content-Type: text/plain; charset=UTF-8');

        return wp_mail($email, $subject, $message, $headers);
    }

    private function is_rate_limited($email) {
        $rate_limit_key = 'oca_rate_limit_' . md5($email);
        $last_request = get_transient($rate_limit_key);

        return $last_request !== false;
    }

    private function set_rate_limit($email) {
        $rate_limit_key = 'oca_rate_limit_' . md5($email);
        set_transient($rate_limit_key, time(), $this->rate_limit_window);
    }

    public function enqueue_scripts() {
        if (get_query_var('customer_login') || get_query_var('customer_dashboard')) {
            wp_enqueue_script('jquery');
            wp_localize_script('jquery', 'oca_ajax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('oca_nonce')
            ));
        }
    }

    public function add_custom_styles() {
        if (get_query_var('customer_login') || get_query_var('customer_dashboard')) {
            ?>
            <style>
            .oca-login-container {
                max-width: 400px;
                margin: 50px auto;
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            .oca-login-form h2 {
                text-align: center;
                margin-bottom: 20px;
                color: #333;
            }

            .oca-step p {
                margin-bottom: 15px;
            }

            .oca-step input[type="email"],
            .oca-step input[type="text"] {
                width: 100%;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 16px;
                box-sizing: border-box;
            }

            .oca-step input[type="text"] {
                text-align: center;
                font-family: monospace;
                font-size: 18px;
                letter-spacing: 2px;
            }

            .oca-step .button {
                padding: 12px 24px;
                font-size: 16px;
                margin-right: 10px;
            }

            .oca-message {
                margin-top: 15px;
                padding: 10px;
                border-radius: 4px;
            }

            .oca-message.success {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .oca-message.error {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            </style>
            <?php
        }
    }
}