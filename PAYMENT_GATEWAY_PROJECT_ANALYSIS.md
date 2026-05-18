# Payment Gateway Project Analysis (MyGooners)

## Overview
This project implements two payment gateways for orders:
- `ToyyibPay`
- `Stripe`

Both gateways are used in:
- Normal cart checkout (`CheckoutController`)
- Direct product checkout (`DirectCheckoutController`)

Core payment state is persisted on `orders`:
- `payment_method` (`toyyibpay` or `stripe`)
- `payment_status` (`pending`, `paid`, `failed`, `refunded`)
- `toyyibpay_bill_code`
- `stripe_payment_intent_id`

## Key Files
- Routes: `routes/web.php`
- Stripe service: `app/Services/StripeService.php`
- ToyyibPay service: `app/Services/ToyyibPayService.php`
- Cart checkout logic: `app/Http/Controllers/Client/CheckoutController.php`
- Direct checkout logic: `app/Http/Controllers/Client/DirectCheckoutController.php`
- Order model: `app/Models/Order.php`
- Payment settings: `app/Models/Setting.php`, `app/helpers.php`, `app/Http/Controllers/Admin/SettingsController.php`
- Auto-cancel command: `app/Console/Commands/AutoCancelPendingOrders.php`

## Runtime Payment Flow
## 1) Checkout start
- User submits checkout with `payment_method` validation `in:toyyibpay,stripe`.
- System prepares pending checkout/order data and creates payment request:
- Stripe: create PaymentIntent and store `stripe_payment_intent_id`.
- ToyyibPay: create bill and use `BillCode`.

## 2) Redirect to gateway
- Stripe flow goes to Stripe payment page using `payment_intent_id` + `client_secret`.
- ToyyibPay flow redirects to hosted bill URL (`https://toyyibpay.com/{BillCode}` or configured base URL).

## 3) Return/callback handling
- Stripe return verifies PaymentIntent status through Stripe API.
- ToyyibPay return verifies bill transaction through ToyyibPay API (`getBillTransactions`).
- On success:
- `payment_status` becomes `paid`.
- Order is created or finalized depending on pending session flow.
- On failure/cancel:
- `payment_status` becomes `failed`.

## 4) Retry payment flow
- User can retry failed/pending payments.
- Existing gateway reference is reused when valid:
- Stripe intent reuse when status allows (`requires_payment_method`, `requires_confirmation`, `requires_action`).
- ToyyibPay bill reuse only when status is unpaid (`0`).
- Otherwise new intent/bill is created.
- Project supports retry with same method or switching method (`retry-payment-with-method`).

## 5) Cancellation flow
- User cancellation checks ownership/status, restores stock, and tries gateway cancellation:
- Stripe PaymentIntent cancellation attempted via API.
- ToyyibPay cancellation is logged only (no direct cancel API in current service implementation).

## 6) Automated cleanup
- Command `orders:auto-cancel-pending` runs hourly.
- Cancels orders pending over 24 hours with payment status `pending/failed`.
- Attempts remote cancellation for Stripe/ToyyibPay references.

## Implemented Gateway Integrations
## Stripe
- Library: `stripe/stripe-php` (Composer dependency).
- Service operations:
- `createPaymentIntent($order)`
- `reusePaymentIntent($paymentIntentId, $order)`
- `verifyPayment($paymentIntentId)`
- `cancelPaymentIntent($paymentIntentId)`
- `handleWebhook($payload, $sigHeader)`
- `getPaymentIntent($paymentIntentId)`
- Currency configured in service: `myr`.
- Amount conversion: `order total * 100`.

## ToyyibPay
- API via Laravel HTTP client.
- Service operations:
- `createBill($orderOrCheckoutData, $returnUrl = null, $isRetryPayment = false)`
- `reuseBill($billCode, $order)`
- `verifyPayment($billCode)`
- `cancelBill($billCode)` (logical/internal only, no real API cancel call)
- Uses:
- `userSecretKey`
- `categoryCode`
- `base_url`
- Amount conversion: `total * 100`.

## Payment Routes (Main)
Defined in `routes/web.php`:
- Checkout payment pages and returns:
- `GET /checkout/stripe-payment`
- `GET /checkout/toyyibpay/return`
- `POST /checkout/toyyibpay/callback`
- `GET /checkout/toyyibpay/cancel`
- `GET /checkout/stripe/return`
- Retry and method-switch:
- `POST /checkout/orders/{order}/retry-payment`
- `GET /checkout/orders/{order}/retry-payment`
- `POST /checkout/orders/{order}/retry-payment-with-method`
- Direct checkout equivalents:
- `GET /direct-checkout/toyyibpay/return`
- `GET /direct-checkout/toyyibpay/cancel`
- `GET /direct-checkout/stripe/return`
- `GET /direct-checkout/stripe/payment`

## Data Model & Schema
Order payment columns:
- `payment_method` (string, nullable)
- `payment_status` (string, default `pending`)
- `transaction_id` (nullable; appears legacy/unused in current gateway flow)
- `toyyibpay_bill_code` (nullable)
- `stripe_payment_intent_id` (nullable)

Relevant migrations:
- `2025_07_30_094149_create_orders_table.php`
- `2025_07_30_140010_add_toyyibpay_bill_code_to_orders_table.php`
- `2025_07_30_162033_add_stripe_payment_intent_id_to_orders_table.php`

## Admin/Feature Flags for Payment Methods
The project has setting keys:
- `stripe_payment_enabled` (default `false`)
- `toyyibpay_payment_enabled` (default `true`)

Observed use:
- UI visibility in checkout views uses `setting(...)`.
- Validation in controllers still allows both methods by request input (`in:toyyibpay,stripe`).

Implication:
- If Stripe is disabled in settings but user submits Stripe method directly, server-side validation alone does not block by setting state unless additional logic exists elsewhere.

## Session-based Pending Payment State
Controllers use session keys to carry pending checkout context across redirects.

Examples for normal checkout:
- `pending_checkout`
- `pending_stripe_payment_intent_id`
- `pending_payment_method`
- `pending_original_payment_method`
- `pending_original_stripe_intent_id`
- `pending_original_toyyibpay_bill_code`

Direct checkout uses equivalent `pending_direct_*` keys.

## Refund Interaction
Refund logic is implemented separately and updates `order.payment_status` to `refunded` when admin marks refund completed.
This is post-payment lifecycle handling, not direct gateway API refund execution.

## Important Findings / Risks
## 1) Stripe webhook config key mismatch risk
- `StripeService::handleWebhook()` uses `config('services.stripe.webhook_secret')`.
- `config/services.php` defines webhook secret under:
- `services.stripe.webhook.secret`

Impact:
- Webhook signature verification may fail if the accessed config path is incorrect.

## 2) Stripe webhook route placement
- `POST /stripe/webhook` appears inside `Route::prefix('checkout')->middleware('auth')->group(...)`.
- If this grouping is active, webhook would require auth and be unsuitable for Stripe server callbacks.

Impact:
- Webhook events may not reach or validate correctly in production.

## 3) Payment setting enforcement is mostly UI-level
- Frontend hides methods via setting flags.
- Server validation currently accepts both methods.

Impact:
- Method disablement may be bypassable through crafted requests.

## 4) Exposed unsafe maintenance route (non-payment but critical)
- `GET /run-composer-update` exists without auth.

Impact:
- Severe production risk and should be removed/locked immediately.

## Suggested Hardening (Priority)
1. Fix Stripe webhook secret path usage:
- Align to `config('services.stripe.webhook.secret')` or flatten config key consistently.
2. Move Stripe webhook route outside auth middleware and protect with signature verification only.
3. Enforce payment method setting flags server-side in checkout and retry endpoints.
4. Remove/secure `run-composer-update` route immediately.
5. Add idempotency protection around return handlers to prevent duplicate order finalization.
6. Add integration tests for:
- Stripe success/failure return
- ToyyibPay success/failure/cancel
- Retry same method and method switch
- Setting-based method disablement

## Quick Verification Checklist
- Confirm `.env` has:
- `TOYYIBPAY_SECRET_KEY`
- `TOYYIBPAY_CATEGORY_CODE`
- `TOYYIBPAY_BASE_URL`
- `STRIPE_SECRET`
- `STRIPE_KEY`
- `STRIPE_WEBHOOK_SECRET`
- Confirm admin settings rows exist for payment toggles.
- Confirm scheduled tasks run (`orders:auto-cancel-pending` hourly).
- Confirm Stripe webhook endpoint is publicly reachable and not auth-gated.

