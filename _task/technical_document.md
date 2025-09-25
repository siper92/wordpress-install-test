# Technical Design Document: Subscription Management Architecture

## Executive Summary

This document outlines the technical implementation of a separated subscription management system for WooCommerce-based multisite environments. The architecture separates storefronts from billing logic to achieve cost-effectiveness, enhanced security, and improved scalability while maintaining PCI compliance.

## System Architecture Overview

```
┌─────────────────────┐    ┌──────────────────┐    ┌─────────────────────┐
│   Storefront Site   │    │  Secure Token    │    │    Billing Site     │
│                     │───▶│    Exchange      │───▶│                     │
│ Custom Gateway      │    │                  │    │ WooCommerce         │
│ Product Display     │    │ HMAC-SHA256      │    │ Subscriptions       │
│ Initial Checkout    │    │ JWT Validation   │    │ Mollie/Adyen        │
└─────────────────────┘    └──────────────────┘    └─────────────────────┘
         ▲                                                   │
         │                 ┌──────────────────┐             │
         │                 │   Webhook        │             │
         └─────────────────│   Callbacks      │◀────────────┘
                           │                  │
                           │ Status Updates   │
                           │ Payment Results  │
                           └──────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                          Customer Dashboard                             │
│                                                                         │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────────────┐ │
│  │   OTP Login     │  │   Multi-lang    │  │   Subscription Mgmt     │ │
│  │   Email Auth    │  │   Support       │  │   Payment History       │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
```

## Core Components

### 1. Custom Payment Gateway Plugin (Storefront)

**Purpose**: Handles subscription order initiation and secure handoff to billing site.

**Key Responsibilities**:
- Create orders with 'pending' status on storefront
- Generate secure tokens for billing site communication
- Redirect customers to billing site with encrypted order data
- Handle payment retry scenarios using WooCommerce's order-pay flow
- Process webhook callbacks for order status updates

### 2. Billing Site Setup

**Purpose**: Dedicated subscription management and payment processing hub.

**Configuration Requirements**:
- WooCommerce Subscriptions plugin (single license)
- Mollie/Adyen payment gateway integration
- Token validation and subscription creation endpoints
- Automated renewal and retry processing
- Webhook system for status communication

### 3. Secure Communication Protocol

**Purpose**: Ensures secure, authenticated data exchange between systems.

**Security Features**:
- HMAC-SHA256 token signing with shared secrets
- JWT-based authentication with expiration
- Timestamp validation to prevent replay attacks
- HTTPS-only communication
- Webhook signature verification

### 4. Customer Dashboard with OTP Authentication

**Purpose**: Provides subscription access without traditional account registration.

**Features**:
- One-time password email authentication
- Multi-language interface support
- Subscription management and billing history
- Payment retry functionality
- Localized date and currency formatting

## Implementation Details

### Custom Payment Gateway Code Structure

```php
<?php
/**
 * Storefront Subscription Gateway
 * Handles subscription order processing and billing site integration
 */
class Storefront_Subscription_Gateway extends WC_Payment_Gateway {

    private $billing_site_url;
    private $shared_secret;
    private $token_expiry = 1800; // 30 minutes

    public function __construct() {
        $this->id = 'storefront_subscription';
        $this->method_title = 'Subscription Billing';
        $this->method_description = 'Redirects to secure billing site for subscription processing';
        $this->has_fields = false;
        $this->supports = array('subscriptions', 'subscription_cancellation');

        $this->init_form_fields();
        $this->init_settings();

        $this->billing_site_url = $this->get_option('billing_site_url');
        $this->shared_secret = $this->get_option('shared_secret');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id,
                  array($this, 'process_admin_options'));
        add_action('woocommerce_api_' . $this->id,
                  array($this, 'handle_webhook'));
    }

    /**
     * Process subscription order and redirect to billing site
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        // Create secure token
        $token = $this->generate_secure_token($order);

        // Update order status
        $order->update_status('pending', 'Redirecting to billing site for subscription processing');

        // Build redirect URL
        $redirect_url = add_query_arg(array(
            'token' => $token,
            'return_url' => $this->get_return_url($order)
        ), $this->billing_site_url . '/subscription-checkout/');

        return array(
            'result' => 'success',
            'redirect' => $redirect_url
        );
    }

    /**
     * Generate HMAC-signed token for billing site
     */
    private function generate_secure_token($order) {
        $timestamp = time();
        $payload = array(
            'order_id' => $order->get_id(),
            'customer_email' => $order->get_billing_email(),
            'customer_first_name' => $order->get_billing_first_name(),
            'customer_last_name' => $order->get_billing_last_name(),
            'billing_address' => $order->get_billing_address(),
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

    /**
     * Extract order items for token payload
     */
    private function get_order_items($order) {
        $items = array();
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            $items[] = array(
                'name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'price' => $item->get_total(),
                'is_subscription' => WC_Subscriptions_Product::is_subscription($product),
                'subscription_period' => WC_Subscriptions_Product::get_period($product),
                'subscription_interval' => WC_Subscriptions_Product::get_interval($product)
            );
        }
        return $items;
    }

    /**
     * Handle webhooks from billing site
     */
    public function handle_webhook() {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

        if (!$this->verify_webhook_signature($payload, $signature)) {
            wp_die('Invalid signature', 'Unauthorized', array('response' => 401));
        }

        $data = json_decode($payload, true);
        $order = wc_get_order($data['order_id']);

        if (!$order) {
            wp_die('Order not found', 'Not Found', array('response' => 404));
        }

        switch ($data['status']) {
            case 'completed':
                $order->payment_complete($data['transaction_id']);
                $order->add_order_note('Payment completed on billing site');
                break;

            case 'failed':
                $order->update_status('failed', 'Payment failed on billing site');
                break;

            case 'cancelled':
                $order->update_status('cancelled', 'Payment cancelled by customer');
                break;
        }

        http_response_code(200);
        echo 'OK';
    }

    /**
     * Verify webhook signature
     */
    private function verify_webhook_signature($payload, $signature) {
        $expected = hash_hmac('sha256', $payload, $this->shared_secret);
        return hash_equals($expected, $signature);
    }

    /**
     * Handle payment retry for failed orders
     */
    public function process_retry_payment($order_id) {
        $order = wc_get_order($order_id);

        if (!$order || $order->get_status() !== 'failed') {
            return false;
        }

        // Generate new token for retry
        $token = $this->generate_secure_token($order);

        // Build retry URL
        $retry_url = add_query_arg(array(
            'token' => $token,
            'retry' => 'true',
            'return_url' => $order->get_checkout_order_received_url()
        ), $this->billing_site_url . '/subscription-checkout/');

        return $retry_url;
    }
}
```

### Billing Site Token Validation

```php
<?php
/**
 * Billing Site Token Handler
 */
class Billing_Site_Token_Handler {

    private $shared_secret;

    public function __construct() {
        $this->shared_secret = get_option('storefront_shared_secret');
        add_action('init', array($this, 'handle_token_request'));
    }

    /**
     * Process incoming token from storefront
     */
    public function handle_token_request() {
        if (!isset($_GET['token']) || $_GET['token'] === '') {
            return;
        }

        $token_data = $this->validate_token($_GET['token']);

        if (!$token_data) {
            wp_die('Invalid or expired token', 'Unauthorized', array('response' => 401));
        }

        // Create subscription on billing site
        $subscription = $this->create_subscription_from_token($token_data);

        if ($subscription) {
            // Redirect to payment
            wp_redirect($subscription->get_checkout_payment_url());
            exit;
        }
    }

    /**
     * Validate HMAC-signed token
     */
    private function validate_token($token) {
        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            return false;
        }

        list($payload, $signature) = $parts;

        // Verify signature
        $expected_signature = hash_hmac('sha256', $payload, $this->shared_secret);
        if (!hash_equals($expected_signature, $signature)) {
            return false;
        }

        // Decode and validate payload
        $data = json_decode(base64_decode($payload), true);

        if (!$data || $data['expires'] < time()) {
            return false;
        }

        return $data;
    }

    /**
     * Create WooCommerce subscription from token data
     */
    private function create_subscription_from_token($token_data) {
        // Create customer if not exists
        $customer = $this->get_or_create_customer($token_data);

        // Create subscription
        $subscription = wcs_create_subscription(array(
            'order_id' => $token_data['order_id'],
            'billing_period' => $token_data['items'][0]['subscription_period'],
            'billing_interval' => $token_data['items'][0]['subscription_interval'],
            'customer_id' => $customer->get_id()
        ));

        // Add subscription items
        foreach ($token_data['items'] as $item_data) {
            if ($item_data['is_subscription']) {
                $subscription->add_product(
                    wc_get_product($item_data['product_id']),
                    $item_data['quantity']
                );
            }
        }

        // Set billing address
        $subscription->set_billing_address($token_data['billing_address']);

        // Calculate totals
        $subscription->calculate_totals();

        return $subscription;
    }
}
```

### OTP Authentication System

```php
<?php
/**
 * OTP Authentication for Customer Dashboard
 */
class OTP_Authentication {

    private $otp_expiry = 900; // 15 minutes

    public function __construct() {
        add_action('init', array($this, 'handle_otp_request'));
        add_action('wp_ajax_nopriv_request_otp', array($this, 'ajax_request_otp'));
        add_action('wp_ajax_nopriv_verify_otp', array($this, 'ajax_verify_otp'));
    }

    /**
     * Generate and send OTP to customer email
     */
    public function request_otp($email) {
        if (!is_email($email)) {
            return new WP_Error('invalid_email', 'Invalid email address');
        }

        // Check if customer exists
        $customer = get_user_by('email', $email);
        if (!$customer) {
            return new WP_Error('customer_not_found', 'No account found with this email');
        }

        // Generate OTP
        $otp = wp_generate_password(6, false, false);
        $otp_numeric = substr(str_replace(array('o', 'l', 'i'), array('0', '1', '1'), $otp), 0, 6);

        // Store OTP with expiration
        update_user_meta($customer->ID, '_otp_code', array(
            'code' => $otp_numeric,
            'expires' => time() + $this->otp_expiry,
            'attempts' => 0
        ));

        // Send OTP email
        $this->send_otp_email($email, $otp_numeric, $customer->display_name);

        return true;
    }

    /**
     * Verify OTP and create session
     */
    public function verify_otp($email, $otp_code) {
        $customer = get_user_by('email', $email);
        if (!$customer) {
            return new WP_Error('customer_not_found', 'Customer not found');
        }

        $stored_otp = get_user_meta($customer->ID, '_otp_code', true);

        // Check expiration
        if (!$stored_otp || $stored_otp['expires'] < time()) {
            delete_user_meta($customer->ID, '_otp_code');
            return new WP_Error('otp_expired', 'OTP has expired');
        }

        // Check attempts
        if ($stored_otp['attempts'] >= 3) {
            delete_user_meta($customer->ID, '_otp_code');
            return new WP_Error('too_many_attempts', 'Too many failed attempts');
        }

        // Verify code
        if ($stored_otp['code'] !== $otp_code) {
            $stored_otp['attempts']++;
            update_user_meta($customer->ID, '_otp_code', $stored_otp);
            return new WP_Error('invalid_otp', 'Invalid OTP code');
        }

        // OTP verified - clean up and create session
        delete_user_meta($customer->ID, '_otp_code');
        wp_set_current_user($customer->ID);
        wp_set_auth_cookie($customer->ID, true);

        return true;
    }

    /**
     * Send OTP email with multi-language support
     */
    private function send_otp_email($email, $otp, $name) {
        $subject = __('Your login code for subscription management', 'subscription-gateway');

        $message = sprintf(
            __('Hello %s,\n\nYour one-time login code is: %s\n\nThis code will expire in 15 minutes.\n\nIf you did not request this code, please ignore this email.\n\nBest regards,\nYour Subscription Team', 'subscription-gateway'),
            $name,
            $otp
        );

        wp_mail($email, $subject, $message);
    }
}
```

## Security & Compliance

### PCI Compliance Strategy

**Scope Reduction**: Storefronts never handle payment card data, significantly reducing PCI compliance scope.

**Implementation**:
- All payment processing occurs on the certified billing site
- Storefronts only handle order metadata and redirects
- Payment card data never touches storefront servers
- Secure token-based communication eliminates need for card data storage

### Data Protection Measures

**In Transit**:
- HTTPS/TLS encryption for all communications
- HMAC-SHA256 signing for token authenticity
- JWT tokens with short expiration windows

**At Rest**:
- Minimal sensitive data storage on storefronts
- Payment data exclusively on PCI-compliant billing site
- Regular security audits and vulnerability assessments

**Access Control**:
- OTP-based authentication eliminates password vulnerabilities
- Role-based access control for administrative functions
- API rate limiting and request validation

## Testing Strategy

### Unit Testing

**Token Generation & Validation**:
```php
// Test token generation
public function test_token_generation() {
    $order = $this->create_sample_order();
    $gateway = new Storefront_Subscription_Gateway();

    $token = $gateway->generate_secure_token($order);
    $this->assertNotEmpty($token);

    // Verify token structure
    $parts = explode('.', $token);
    $this->assertEquals(2, count($parts));

    // Verify payload integrity
    $payload = json_decode(base64_decode($parts[0]), true);
    $this->assertEquals($order->get_id(), $payload['order_id']);
}

// Test webhook signature validation
public function test_webhook_signature() {
    $payload = '{"order_id": 123, "status": "completed"}';
    $signature = hash_hmac('sha256', $payload, 'test_secret');

    $gateway = new Storefront_Subscription_Gateway();
    $result = $gateway->verify_webhook_signature($payload, $signature);

    $this->assertTrue($result);
}
```

**OTP System Testing**:
```php
// Test OTP generation and validation
public function test_otp_workflow() {
    $auth = new OTP_Authentication();
    $email = 'test@example.com';

    // Request OTP
    $result = $auth->request_otp($email);
    $this->assertTrue($result);

    // Get stored OTP
    $customer = get_user_by('email', $email);
    $otp_data = get_user_meta($customer->ID, '_otp_code', true);

    // Verify OTP
    $verify_result = $auth->verify_otp($email, $otp_data['code']);
    $this->assertTrue($verify_result);
}
```

### Integration Testing

**End-to-End Subscription Flow**:
1. Create subscription product on storefront
2. Process checkout with custom gateway
3. Verify token generation and redirect
4. Validate token on billing site
5. Create subscription and process payment
6. Confirm webhook callback updates storefront order

**Payment Retry Testing**:
1. Simulate failed payment on billing site
2. Verify webhook updates order status to 'failed'
3. Test customer retry functionality
4. Confirm successful retry completes order

### Renewal & Retry Testing

**Automated Renewal Testing**:
```php
// Test subscription renewal process
public function test_subscription_renewal() {
    $subscription = $this->create_test_subscription();

    // Simulate renewal date
    $subscription->set_date('next_payment', date('Y-m-d H:i:s', strtotime('-1 day')));

    // Trigger renewal process
    do_action('woocommerce_scheduled_subscription_payment', $subscription->get_id());

    // Verify renewal order creation
    $renewal_orders = wcs_get_subscriptions_for_renewal_order($subscription);
    $this->assertNotEmpty($renewal_orders);
}
```

**Failed Payment Retry Logic**:
```php
// Test payment retry functionality
public function test_payment_retry() {
    $failed_order = $this->create_failed_order();
    $gateway = new Storefront_Subscription_Gateway();

    $retry_url = $gateway->process_retry_payment($failed_order->get_id());
    $this->assertNotEmpty($retry_url);

    // Verify retry token validity
    $parsed_url = wp_parse_url($retry_url);
    parse_str($parsed_url['query'], $query_params);

    $this->assertArrayHasKey('token', $query_params);
    $this->assertArrayHasKey('retry', $query_params);
}
```

## Multi-language Support Implementation

### Internationalization (i18n)

**Text Domain Setup**:
```php
// Plugin header
Text Domain: subscription-gateway
Domain Path: /languages

// Load text domain
add_action('plugins_loaded', function() {
    load_plugin_textdomain('subscription-gateway', false,
                          dirname(plugin_basename(__FILE__)) . '/languages/');
});
```

**Translation Functions**:
```php
// Gateway titles and descriptions
$this->method_title = __('Subscription Billing', 'subscription-gateway');
$this->method_description = __('Redirects to secure billing site for subscription processing', 'subscription-gateway');

// Error messages
return new WP_Error('invalid_token',
    __('Invalid or expired payment token. Please try again.', 'subscription-gateway'));

// Email templates
$subject = __('Your subscription payment confirmation', 'subscription-gateway');
```

### Language Detection & Persistence

**Browser-based Detection**:
```php
// Detect customer language preference
function detect_customer_language() {
    if (isset($_GET['lang'])) {
        return sanitize_text_field($_GET['lang']);
    }

    $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $preferred_language = substr($accept_language, 0, 2);

    $supported_languages = array('en', 'es', 'fr', 'de', 'it');

    return in_array($preferred_language, $supported_languages)
        ? $preferred_language
        : 'en';
}
```

**Language Persistence**:
```php
// Store language preference in session
function set_customer_language($language) {
    if (session_id() === '') {
        session_start();
    }
    $_SESSION['customer_language'] = $language;
}

// Include language in token payload
'language' => $this->get_customer_language(),
'locale' => get_locale()
```

## Scalability Considerations

### Cost Optimization

**Single License Architecture**:
- Only the billing site requires WooCommerce Subscriptions license
- Multiple storefronts connect without additional subscription costs
- Shared infrastructure reduces operational overhead

**Efficient Resource Usage**:
```php
// Webhook batching for high-volume scenarios
class Webhook_Batch_Processor {
    private $batch_size = 50;
    private $batch_timeout = 30;

    public function queue_webhook($webhook_data) {
        $batch_key = 'webhook_batch_' . date('YmdH');
        $current_batch = get_transient($batch_key) ?: array();

        $current_batch[] = $webhook_data;

        if (count($current_batch) >= $this->batch_size) {
            $this->process_batch($current_batch);
            delete_transient($batch_key);
        } else {
            set_transient($batch_key, $current_batch, $this->batch_timeout);
        }
    }
}
```

### Performance Optimization

**Caching Strategy**:
```php
// Cache frequently accessed subscription data
function get_cached_subscription_status($subscription_id) {
    $cache_key = 'subscription_status_' . $subscription_id;
    $status = wp_cache_get($cache_key, 'subscriptions');

    if (false === $status) {
        $subscription = wcs_get_subscription($subscription_id);
        $status = $subscription->get_status();
        wp_cache_set($cache_key, $status, 'subscriptions', 300); // 5 minutes
    }

    return $status;
}
```

## Deployment & Maintenance

### Environment Configuration

**Development Setup**:
```yaml
# docker-compose.yml
version: '3.8'
services:
  storefront:
    image: wordpress:latest
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_NAME: storefront_db
      BILLING_SITE_URL: http://billing:8080
      SHARED_SECRET: dev_secret_key

  billing:
    image: wordpress:latest
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_NAME: billing_db
      WOOCOMMERCE_SUBSCRIPTIONS_KEY: ${WCS_LICENSE_KEY}
```

**Production Considerations**:
- Blue-green deployment strategy
- Database migration scripts
- Environment-specific configuration management
- Comprehensive monitoring and logging

### Monitoring & Alerting

**Key Metrics**:
- Subscription conversion rates
- Payment retry success rates
- Webhook delivery reliability
- Token validation failure rates
- System response times

**Alert Thresholds**:
```php
// Monitor critical subscription metrics
function monitor_subscription_health() {
    $failed_renewals = wcs_get_subscriptions(array(
        'subscription_status' => 'on-hold',
        'date_created' => '>' . (time() - DAY_IN_SECONDS)
    ));

    if (count($failed_renewals) > 10) {
        wp_mail(
            get_option('admin_email'),
            'High renewal failure rate detected',
            sprintf('Found %d failed renewals in the last 24 hours', count($failed_renewals))
        );
    }
}
```

## Risk Mitigation

### Technical Risk Management

**Backup & Recovery**:
- Automated daily database backups
- Point-in-time recovery capabilities
- Cross-region backup replication
- Disaster recovery testing procedures

**Version Control & Rollback**:
```bash
# Deployment script with rollback capability
#!/bin/bash
CURRENT_VERSION=$(git rev-parse HEAD)
BACKUP_DIR="/var/backups/$(date +%Y%m%d_%H%M%S)"

# Create backup
mkdir -p $BACKUP_DIR
cp -r /var/www/html $BACKUP_DIR/

# Deploy new version
git pull origin main
composer install --no-dev

# Health check
if ! wp option get siteurl > /dev/null 2>&1; then
    echo "Health check failed, rolling back..."
    rm -rf /var/www/html
    cp -r $BACKUP_DIR/html /var/www/
    git checkout $CURRENT_VERSION
    exit 1
fi

echo "Deployment successful"
```

### Business Continuity

**Failover Mechanisms**:
- Load balancer health checks
- Automatic failover to backup billing site
- Grace period for payment processing during outages
- Customer communication templates for service interruptions

This technical design provides a comprehensive foundation for implementing the subscription management architecture while maintaining security, scalability, and cost-effectiveness across multiple storefronts.