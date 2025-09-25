Developer Task Sheet: Subscription Management Architecture
Introduction
We are evaluating a subscription management system for our WooCommerce-based multisite setup. The
approach involves separating the storefront(s) from the billing logic to improve cost-effectiveness, security,
and scalability.
Your task is to design and describe how you would implement this solution as a Senior WordPress
Developer.
Background Context
- Storefront site(s): Handle product listings and checkout UX. Non-subscription orders are processed
  normally.
- Billing site: Dedicated WooCommerce installation with WooCommerce Subscriptions plugin and
  Mollie/Adyen integration.
- Orders for subscriptions: Captured on the storefront, redirected securely to the billing site, and managed
  there.
- Renewals & retries: Automated by billing site, with retry option for customers.
- Client dashboard: Standard WooCommerce account area, multi-language ready, login via one-time
  password (OTP) by email.
  Task Requirements
  The applicant should propose a detailed plan covering:
1. **Custom Payment Gateway Plugin (Storefront)**
- Handles initial order creation (pending status).
- Redirects subscription checkouts to the billing site with a secure token.
- Allows retry of failed payments using WooCommerce's order-pay flow.
2. **Billing Site Setup**
- WooCommerce Subscriptions plugin licensed and configured.
- Integration with Mollie/Adyen payment gateways.
  Developer Task Sheet: Subscription Management Architecture
- Token validation for storefront requests.
- Creation and management of subscriptions.
- Handling of renewals, retries, and failed payments.
3. **Secure Communication**
- Use of HMAC/JWT or equivalent for signing order tokens.
- Callback/webhook mechanism to update storefront order statuses.
4. **Client Dashboard**
- Customer access to subscriptions, payments, and billing history.
- Multi-language support by design.
- One-time password (OTP) email authentication instead of account registration.
5. **Scalability & Cost Awareness**
- Only one subscription plugin license is required (billing site).
- Multiple storefronts can connect without extra license cost.
6. **Security & Compliance**
- Explain how PCI compliance is respected.
- Ensure sensitive data (e.g., payment details) is not exposed to storefronts.
  Deliverables
- A short technical design document (architecture diagram optional).
- Description of key implementation steps.
- Code outline or pseudo-code for the custom payment gateway plugin.
- Notes on testing strategy (especially renewals and retries).
- Considerations for multi-language support and OTP login.