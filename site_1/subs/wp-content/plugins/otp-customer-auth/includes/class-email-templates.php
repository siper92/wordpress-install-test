<?php

if (!defined('ABSPATH')) {
    exit;
}

class OCA_Email_Templates {

    public function __construct() {
        add_filter('wp_mail', array($this, 'customize_otp_email'), 10, 1);
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function customize_otp_email($atts) {
        if (strpos($atts['subject'], __('Your login code', 'otp-customer-auth')) !== false) {
            $atts = $this->format_otp_email($atts);
        }

        return $atts;
    }

    private function format_otp_email($atts) {
        $template = get_option('oca_email_template', 'default');

        if ($template === 'html') {
            $atts['headers'][] = 'Content-Type: text/html; charset=UTF-8';
            $atts['message'] = $this->get_html_template($atts['message']);
        }

        $custom_subject = get_option('oca_email_subject', '');
        if (!empty($custom_subject)) {
            $atts['subject'] = $custom_subject;
        }

        return $atts;
    }

    private function get_html_template($message) {
        $lines = explode("\n", $message);
        $name = '';
        $code = '';

        foreach ($lines as $line) {
            if (strpos($line, 'Hello ') === 0) {
                $name = trim(str_replace('Hello ', '', str_replace(',', '', $line)));
            }
            if (preg_match('/code is: (\d{6})/', $line, $matches)) {
                $code = $matches[1];
            }
        }

        $site_name = get_bloginfo('name');
        $site_url = home_url();

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>" . __('Your Login Code', 'otp-customer-auth') . "</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .email-container {
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    border-bottom: 2px solid #007cba;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #007cba;
                    margin: 0;
                }
                .code-container {
                    background: #f8f9fa;
                    border: 2px solid #007cba;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    margin: 30px 0;
                }
                .code {
                    font-size: 36px;
                    font-weight: bold;
                    color: #007cba;
                    letter-spacing: 8px;
                    font-family: monospace;
                }
                .expires {
                    color: #666;
                    font-size: 14px;
                    margin-top: 10px;
                }
                .footer {
                    border-top: 1px solid #eee;
                    padding-top: 20px;
                    margin-top: 30px;
                    font-size: 14px;
                    color: #666;
                    text-align: center;
                }
                .warning {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 4px;
                    padding: 15px;
                    margin: 20px 0;
                    color: #856404;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>{$site_name}</h1>
                    <p>" . __('Customer Dashboard Access', 'otp-customer-auth') . "</p>
                </div>

                <p>" . sprintf(__('Hello %s,', 'otp-customer-auth'), '<strong>' . esc_html($name) . '</strong>') . "</p>

                <p>" . __('You requested access to your customer dashboard. Please use the following one-time code to login:', 'otp-customer-auth') . "</p>

                <div class='code-container'>
                    <div class='code'>{$code}</div>
                    <div class='expires'>" . __('This code expires in 15 minutes', 'otp-customer-auth') . "</div>
                </div>

                <div class='warning'>
                    <strong>" . __('Security Notice:', 'otp-customer-auth') . "</strong><br>
                    " . __('If you did not request this code, please ignore this email. Your account remains secure.', 'otp-customer-auth') . "
                </div>

                <p>" . __('For security reasons, this code can only be used once and will expire automatically.', 'otp-customer-auth') . "</p>

                <div class='footer'>
                    <p>" . __('This is an automated message from', 'otp-customer-auth') . " <a href='{$site_url}'>{$site_name}</a></p>
                    <p>" . __('Please do not reply to this email.', 'otp-customer-auth') . "</p>
                </div>
            </div>
        </body>
        </html>";
    }

    public function add_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __('OTP Email Settings', 'otp-customer-auth'),
            __('OTP Email', 'otp-customer-auth'),
            'manage_options',
            'oca-email-settings',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('oca_email_settings', 'oca_email_template');
        register_setting('oca_email_settings', 'oca_email_subject');
        register_setting('oca_email_settings', 'oca_email_from_name');
        register_setting('oca_email_settings', 'oca_email_from_email');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('OTP Email Settings', 'otp-customer-auth'); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields('oca_email_settings'); ?>
                <?php do_settings_sections('oca_email_settings'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Email Template', 'otp-customer-auth'); ?></th>
                        <td>
                            <label>
                                <input type="radio" name="oca_email_template" value="plain" <?php checked(get_option('oca_email_template', 'default'), 'plain'); ?> />
                                <?php _e('Plain Text', 'otp-customer-auth'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="oca_email_template" value="html" <?php checked(get_option('oca_email_template', 'default'), 'html'); ?> />
                                <?php _e('HTML', 'otp-customer-auth'); ?>
                            </label>
                            <p class="description"><?php _e('Choose the format for OTP emails.', 'otp-customer-auth'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Email Subject', 'otp-customer-auth'); ?></th>
                        <td>
                            <input type="text" name="oca_email_subject" value="<?php echo esc_attr(get_option('oca_email_subject', '')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Custom email subject. Leave blank to use default.', 'otp-customer-auth'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('From Name', 'otp-customer-auth'); ?></th>
                        <td>
                            <input type="text" name="oca_email_from_name" value="<?php echo esc_attr(get_option('oca_email_from_name', '')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Custom sender name. Leave blank to use site name.', 'otp-customer-auth'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('From Email', 'otp-customer-auth'); ?></th>
                        <td>
                            <input type="email" name="oca_email_from_email" value="<?php echo esc_attr(get_option('oca_email_from_email', '')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Custom sender email. Leave blank to use admin email.', 'otp-customer-auth'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Test Email', 'otp-customer-auth'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Send Test Email', 'otp-customer-auth'); ?></th>
                        <td>
                            <input type="email" id="test-email" placeholder="<?php _e('Enter email address', 'otp-customer-auth'); ?>" class="regular-text" />
                            <button type="button" id="send-test-email" class="button">
                                <?php _e('Send Test', 'otp-customer-auth'); ?>
                            </button>
                            <div id="test-result"></div>
                            <p class="description"><?php _e('Send a test OTP email to see how it looks.', 'otp-customer-auth'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#send-test-email').on('click', function() {
                var email = $('#test-email').val();
                var $button = $(this);
                var $result = $('#test-result');

                if (!email) {
                    $result.html('<div class="notice notice-error"><p><?php _e('Please enter an email address.', 'otp-customer-auth'); ?></p></div>');
                    return;
                }

                $button.prop('disabled', true).text('<?php _e('Sending...', 'otp-customer-auth'); ?>');
                $result.empty();

                $.post(ajaxurl, {
                    action: 'send_test_otp_email',
                    email: email,
                    nonce: '<?php echo wp_create_nonce('send_test_otp'); ?>'
                }, function(response) {
                    if (response.success) {
                        $result.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                    } else {
                        $result.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                }).always(function() {
                    $button.prop('disabled', false).text('<?php _e('Send Test', 'otp-customer-auth'); ?>');
                });
            });
        });
        </script>
        <?php
    }

    public function send_test_otp_email() {
        check_ajax_referer('send_test_otp', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Access denied.', 'otp-customer-auth')));
        }

        $email = sanitize_email($_POST['email']);
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Invalid email address.', 'otp-customer-auth')));
        }

        $test_code = '123456';
        $name = __('Test User', 'otp-customer-auth');

        $subject = get_option('oca_email_subject', '') ?: __('Your login code', 'otp-customer-auth');

        $message = sprintf(
            __('Hello %s,

Your one-time login code is: %s

This code will expire in 15 minutes.

If you did not request this code, please ignore this email.

Best regards,
Customer Support', 'otp-customer-auth'),
            $name,
            $test_code
        );

        $headers = array();

        $from_name = get_option('oca_email_from_name', '');
        $from_email = get_option('oca_email_from_email', '');

        if ($from_name && $from_email) {
            $headers[] = "From: {$from_name} <{$from_email}>";
        }

        $sent = wp_mail($email, $subject, $message, $headers);

        if ($sent) {
            wp_send_json_success(array(
                'message' => sprintf(__('Test email sent successfully to %s', 'otp-customer-auth'), $email)
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to send test email. Please check your email configuration.', 'otp-customer-auth')
            ));
        }
    }
}

add_action('wp_ajax_send_test_otp_email', array(new OCA_Email_Templates(), 'send_test_otp_email'));