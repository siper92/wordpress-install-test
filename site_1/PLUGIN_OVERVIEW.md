# Subscription Management Plugins Overview

This document provides a comprehensive overview of the three custom plugins developed for the subscription management architecture.

## Plugin Architecture

The system consists of three specialized plugins working together to create a secure, scalable subscription management solution:

1. **Storefront Subscription Gateway** (Frontend) - Handles initial order processing and redirects
2. **Billing Site Handler** (Backend) - Processes tokens and manages subscriptions
3. **OTP Customer Authentication** (Customer Access) - Provides secure dashboard access

## 1. Storefront Subscription Gateway

**Location**: `site_1/front/wp-content/plugins/storefront-subscription-gateway/`

### Purpose
Handles subscription orders on the storefront and securely redirects customers to the billing site for payment processing.

### Key Features

- **Subscription Detection**: Only appears for orders containing subscription products
- **Secure Token Generation**: Creates HMAC-signed tokens containing order data
- **Seamless Redirects**: Automatically redirects customers to billing site
- **Webhook Handling**: Receives status updates from billing site
- **Payment Retries**: Supports failed payment retry functionality
- **Multi-language**: Includes language detection and support

### Main Classes

#### `Storefront_Subscription_Gateway`
- Extends `WC_Payment_Gateway`
- Handles payment processing and token generation
- Manages webhook callbacks and order status updates

### Security Features

- **HMAC-SHA256 Signing**: All tokens signed with shared secret
- **Token Expiration**: 30-minute expiry window
- **Signature Verification**: Validates all incoming webhooks
- **SSL Requirements**: Enforces HTTPS communication

### Configuration Options

- **Billing Site URL**: Target URL for subscription processing
- **Shared Secret**: Cryptographic key for token signing
- **Gateway Title/Description**: Customer-facing text
- **Enable/Disable**: Gateway activation control

## 2. Billing Site Handler

**Location**: `site_1/subs/wp-content/plugins/billing-site-handler/`

### Purpose
Receives tokens from storefronts, validates them, creates subscriptions, and manages the complete subscription lifecycle.

### Key Features

- **Token Validation**: Verifies HMAC signatures and expiration
- **Subscription Creation**: Automatically creates WooCommerce subscriptions
- **Customer Management**: Creates/manages customer accounts
- **Product Synchronization**: Creates billing-side products from token data
- **Webhook Broadcasting**: Sends status updates to configured endpoints
- **Renewal Processing**: Handles automated renewals and retries

### Main Classes

#### `BSH_Token_Handler`
- Validates incoming tokens from storefronts
- Creates subscriptions and customers
- Provides admin settings interface

#### `BSH_Webhook_Sender`
- Sends status updates to storefront sites
- Handles subscription and payment events
- Manages webhook delivery and retries

#### `BSH_Subscription_Processor`
- Processes subscription lifecycle events
- Handles renewal payments and failures
- Manages retry logic and suspension

### URL Endpoints

- `/subscription-checkout/` - Token processing endpoint
- **Admin Settings**: Settings > Billing Site

### Security Features

- **Token Validation**: Comprehensive signature and expiry checking
- **Rate Limiting**: Prevents abuse of token endpoints
- **Error Handling**: Secure error messages without information leakage
- **Audit Logging**: Comprehensive logging of all operations

## 3. OTP Customer Authentication

**Location**: `site_1/subs/wp-content/plugins/otp-customer-auth/`

### Purpose
Provides secure, password-free access to customer subscription dashboard using one-time passwords sent via email.

### Key Features

- **Email-based Authentication**: Uses email addresses as primary identifier
- **One-time Passwords**: 6-digit numeric codes with 15-minute expiry
- **Rate Limiting**: Prevents spam and abuse
- **Multi-language Dashboard**: Supports internationalization
- **Subscription Management**: Full subscription view and control
- **Payment History**: Displays payment records and status
- **Mobile Responsive**: Works across all device types

### Main Classes

#### `OCA_Authentication`
- Generates and validates OTP codes
- Handles login flow and session management
- Provides rate limiting and security controls

#### `OCA_Customer_Dashboard`
- Renders subscription management interface
- Handles subscription modifications and cancellations
- Displays payment history and account information

#### `OCA_Email_Templates`
- Manages OTP email formatting and delivery
- Provides HTML and plain text templates
- Includes admin configuration interface

### URL Endpoints

- `/customer-login/` - OTP login interface
- `/customer-dashboard/` - Subscription management dashboard
- **Admin Settings**: Settings > OTP Email

### Dashboard Features

- **Subscription Cards**: Visual subscription overview
- **Status Indicators**: Clear status display (Active, Cancelled, On-Hold)
- **Payment Actions**: Pay now, cancel, view details
- **Payment History**: Chronological payment records
- **Account Information**: Customer details and login history

### Security Features

- **Rate Limiting**: 5-minute cooldown between OTP requests
- **Attempt Limiting**: Maximum 3 verification attempts per code
- **Session Security**: Secure cookie handling
- **Input Validation**: Comprehensive data sanitization

## Integration Flow

### 1. Checkout Process
```
Customer → Storefront → Token Generation → Billing Site → Subscription Creation → Payment Processing
```

### 2. Status Updates
```
Billing Site → Webhook → Storefront → Order Status Update → Customer Notification
```

### 3. Customer Access
```
Customer → OTP Request → Email → Code Entry → Dashboard Access → Subscription Management
```

## Technical Specifications

### Token Structure
```json
{
  "order_id": 123,
  "customer_email": "customer@example.com",
  "customer_first_name": "John",
  "customer_last_name": "Doe",
  "billing_address": {...},
  "order_total": "29.99",
  "currency": "USD",
  "items": [...],
  "timestamp": 1234567890,
  "expires": 1234569690,
  "storefront_url": "https://shop.example.com",
  "language": "en_US"
}
```

### Webhook Payload
```json
{
  "type": "payment_complete",
  "order_id": 123,
  "subscription_id": 456,
  "status": "completed",
  "transaction_id": "txn_abc123",
  "timestamp": 1234567890
}
```

### OTP Structure
```json
{
  "code": "123456",
  "expires": 1234567890,
  "attempts": 0,
  "created": 1234567890
}
```

## Database Impact

### New Meta Fields

#### Orders (Storefront)
- `_billing_site_token` - Generated token for billing site
- `_billing_subscription_id` - Remote subscription ID

#### Subscriptions (Billing Site)
- `_storefront_order_id` - Original storefront order ID
- `_storefront_url` - Originating storefront URL
- `_renewal_retry_count` - Number of renewal attempts

#### Users
- `_oca_otp_code` - Active OTP data
- `_oca_last_login` - Last successful login timestamp

#### Products (Billing Site)
- `_storefront_product_sync` - Synced storefront product ID

### Transients Used
- `oca_rate_limit_{email_hash}` - OTP request rate limiting
- `webhook_batch_{date_hour}` - Webhook batching for performance

## Performance Considerations

### Optimization Features

- **Webhook Batching**: Groups multiple webhooks for efficiency
- **Subscription Caching**: Caches frequently accessed subscription data
- **Rate Limiting**: Prevents system abuse and overload
- **Lazy Loading**: Dashboard components load on demand
- **Database Indexing**: Optimized queries for subscription lookups

### Scalability Features

- **Stateless Design**: No session dependencies between requests
- **Horizontal Scaling**: Multiple storefronts can use single billing site
- **Caching Ready**: Compatible with object caching solutions
- **CDN Friendly**: Static assets can be served via CDN

## Error Handling and Logging

### Error Categories

1. **Authentication Errors**: Invalid tokens, expired codes
2. **Processing Errors**: Subscription creation failures
3. **Communication Errors**: Webhook delivery failures
4. **Security Errors**: Rate limiting, invalid signatures

### Logging Locations

- WordPress debug log (`/wp-content/debug.log`)
- Plugin-specific logs (when debug enabled)
- Payment gateway logs
- Email delivery logs

### Error Recovery

- **Token Regeneration**: Automatic retry on token failures
- **Webhook Retries**: Automatic retry with exponential backoff
- **Graceful Degradation**: System continues operating with reduced functionality
- **Manual Intervention**: Admin tools for manual processing

## Maintenance and Updates

### Regular Tasks

- **Log Monitoring**: Weekly review of error logs
- **Security Updates**: Keep WordPress and plugins updated
- **Token Key Rotation**: Periodic shared secret updates
- **Performance Monitoring**: Track response times and success rates

### Update Procedures

1. **Test Environment**: Always test updates in staging
2. **Backup Strategy**: Full backup before any updates
3. **Rollback Plan**: Ability to quickly revert changes
4. **Communication**: Notify customers of any downtime

This plugin architecture provides a robust, secure, and scalable solution for managing subscriptions across multiple storefronts while maintaining security and cost-effectiveness.