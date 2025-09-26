# Subscription Management Architecture - Setup Instructions

This document provides step-by-step instructions for setting up and configuring the subscription management system with separated storefront and billing sites.

## Architecture Overview

- **Storefront Site** (`site_1/front`): Handles product display and initial checkout
- **Billing Site** (`site_1/subs`): Manages subscriptions, payments, and customer dashboard
- **Secure Communication**: HMAC-signed tokens and webhooks for data exchange

## Prerequisites

Before starting the setup, ensure you have:

1. **WooCommerce** installed and activated on both sites
2. **WooCommerce Subscriptions** plugin licensed and installed on the billing site only
3. **Payment Gateway** (Mollie/Adyen) configured on the billing site
4. **SSL certificates** for both sites (required for secure communication)
5. **Email configuration** for OTP delivery

## Step 1: Plugin Installation

### Storefront Site (site_1/front)

1. Upload the `storefront-subscription-gateway` plugin to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Navigate to **WooCommerce > Settings > Payments**
4. Enable and configure the "Subscription Billing" payment method

### Billing Site (site_1/subs)

1. Upload both plugins to `/wp-content/plugins/`:
   - `billing-site-handler`
   - `otp-customer-auth`
2. Activate both plugins through WordPress admin
3. Ensure WooCommerce Subscriptions is properly licensed and activated

## Step 2: Configuration

### Storefront Configuration

Navigate to **WooCommerce > Settings > Payments > Subscription Billing**:

1. **Enable/Disable**: Check to enable the gateway
2. **Title**: "Subscription Payment" (or customize as needed)
3. **Description**: Customer-facing description for checkout
4. **Billing Site URL**: `https://your-billing-site.com` (without trailing slash)
5. **Shared Secret**: Generate a strong, unique key (minimum 32 characters)

Example configuration:
```
Title: Secure Subscription Payment
Description: Secure subscription payment processing
Billing Site URL: https://subs.yoursite.com
Shared Secret: your-super-secret-key-here-min-32-chars
```

### Billing Site Configuration

#### 1. Billing Site Handler Settings

Navigate to **Settings > Billing Site**:

1. **Shared Secret**: Use the SAME secret key as configured on storefront
2. **Webhook URLs**: Add storefront webhook URL (one per line):
   ```
   https://your-storefront.com/wc-api/storefront_subscription/
   ```

#### 2. OTP Email Settings (Optional)

Navigate to **Settings > OTP Email**:

1. **Email Template**: Choose HTML for better formatting
2. **Email Subject**: Customize if desired
3. **From Name**: Your company name
4. **From Email**: noreply@yourdomain.com
5. Test email functionality using the test feature

### Payment Gateway Setup

Configure your payment gateway (Mollie/Adyen) on the billing site:

1. Install and configure your chosen payment gateway plugin
2. Enable the gateway in **WooCommerce > Settings > Payments**
3. Configure API keys and webhook endpoints
4. Test payment processing

## Step 3: URL Structure Setup

### Rewrite Rules

Both plugins automatically add rewrite rules during activation. If URLs don't work:

1. Go to **Settings > Permalinks**
2. Click "Save Changes" to flush rewrite rules
3. Test the following URLs:
   - Billing site: `https://billing-site.com/subscription-checkout/`
   - Customer login: `https://billing-site.com/customer-login/`
   - Customer dashboard: `https://billing-site.com/customer-dashboard/`

## Step 4: Security Configuration

### Shared Secret Management

1. **Generate Strong Key**: Use a cryptographically secure random string
2. **Keep Secret Safe**: Store in environment variables or secure configuration
3. **Regular Rotation**: Change periodically for enhanced security

Example key generation (PHP):
```php
$shared_secret = bin2hex(random_bytes(32)); // 64-character hex string
```

### SSL/TLS Configuration

1. Ensure both sites have valid SSL certificates
2. Force HTTPS redirects
3. Configure proper HSTS headers
4. Test webhook delivery over HTTPS

## Step 5: Testing the Setup

### 1. Token Generation Test

Create a test subscription product on the storefront and attempt checkout:

1. Product should redirect to billing site with token parameter
2. Check browser network tab for proper redirect URL
3. Verify token contains expected data structure

### 2. Webhook Communication Test

1. Complete a test subscription on billing site
2. Check storefront order status updates
3. Monitor webhook logs for delivery confirmation
4. Verify order notes reflect status changes

### 3. OTP Authentication Test

1. Visit `https://billing-site.com/customer-login/`
2. Enter customer email address
3. Check email delivery and code format
4. Test login with correct/incorrect codes
5. Verify dashboard access after successful login

### 4. Customer Dashboard Test

After successful OTP login:

1. Verify subscription display and details
2. Test subscription cancellation
3. Check payment history display
4. Test retry payment functionality

## Step 6: Production Deployment

### Environment-Specific Configuration

#### Development
```php
// wp-config.php additions
define('BSH_SHARED_SECRET', 'dev-secret-key');
define('BSH_BILLING_URL', 'http://localhost:8080');
define('WP_DEBUG', true);
```

#### Production
```php
// wp-config.php additions
define('BSH_SHARED_SECRET', $_ENV['BSH_SHARED_SECRET']);
define('BSH_BILLING_URL', 'https://billing.yourdomain.com');
define('WP_DEBUG', false);
```

### Security Checklist

- [ ] SSL certificates installed and properly configured
- [ ] Shared secrets stored securely (not in code)
- [ ] Payment gateway webhooks use proper signatures
- [ ] Rate limiting configured for OTP requests
- [ ] Log monitoring set up for failed authentications
- [ ] Backup strategy implemented
- [ ] Security headers configured

### Monitoring Setup

1. **Error Logging**: Monitor plugin error logs
2. **Webhook Delivery**: Track successful/failed webhook deliveries
3. **Payment Processing**: Monitor subscription payment success rates
4. **OTP Delivery**: Track email delivery rates
5. **Security Events**: Log failed authentication attempts

## Troubleshooting

### Common Issues

#### 1. Token Validation Failures
- **Symptom**: "Invalid or expired token" errors
- **Causes**:
  - Mismatched shared secrets
  - Clock synchronization issues
  - Network connectivity problems
- **Solutions**:
  - Verify shared secret matches on both sites
  - Check server time synchronization
  - Test network connectivity between sites

#### 2. Webhook Delivery Failures
- **Symptom**: Order statuses not updating on storefront
- **Causes**:
  - Incorrect webhook URLs
  - SSL certificate issues
  - Firewall blocking requests
- **Solutions**:
  - Verify webhook URL configuration
  - Check SSL certificate validity
  - Review firewall rules

#### 3. OTP Email Issues
- **Symptom**: Login codes not received
- **Causes**:
  - SMTP configuration problems
  - Email blocked by spam filters
  - Rate limiting active
- **Solutions**:
  - Test email configuration
  - Check spam folders
  - Review rate limiting settings

#### 4. Subscription Creation Failures
- **Symptom**: Subscriptions not created on billing site
- **Causes**:
  - Missing WooCommerce Subscriptions plugin
  - Product configuration issues
  - Customer creation failures
- **Solutions**:
  - Verify plugin activation and licensing
  - Check product subscription settings
  - Review customer creation logs

### Debug Mode

Enable debug logging by adding to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('BSH_DEBUG', true);
```

Check logs in `/wp-content/debug.log` for detailed error information.

## Support and Maintenance

### Regular Tasks

1. **Monitor Logs**: Review error logs weekly
2. **Update Plugins**: Keep all plugins updated
3. **Test Workflows**: Monthly end-to-end testing
4. **Security Review**: Quarterly security audits
5. **Backup Verification**: Test backup restoration monthly

### Performance Optimization

1. **Caching**: Implement object caching (Redis/Memcached)
2. **Database**: Optimize subscription-related queries
3. **CDN**: Use CDN for static assets
4. **Monitoring**: Set up performance monitoring

This setup provides a secure, scalable subscription management system that separates concerns while maintaining seamless customer experience.