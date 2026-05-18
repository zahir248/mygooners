# MyGooners Project Documentation

## 1. Project Overview
MyGooners is a Laravel 12 web platform with three major interfaces:
- Client-facing website (shop, services, content, checkout, account)
- Admin panel (content, users, catalog, orders, refunds, logs, settings)
- Mobile API (Sanctum-based authentication and order/payment flows)

Core business capabilities:
- Product commerce with order lifecycle tracking
- Service marketplace and service reviews
- Blog/articles and video content
- Multi-gateway payments (ToyyibPay and Stripe)
- Invoice generation/download using DomPDF
- Refund request and admin refund processing
- Seller onboarding and moderation

## 2. Tech Stack
- Framework: Laravel `^12.0`
- PHP: `^8.0`
- API auth: Laravel Sanctum `^4.2`
- OAuth: Laravel Socialite `^5.12` (Google login)
- Payment: Stripe SDK `^17.4`, ToyyibPay HTTP integration
- PDF: `barryvdh/laravel-dompdf` `^3.1`
- DB utility: `doctrine/dbal` `^4.3`
- Frontend build: Vite

Reference: [composer.json](C:\laragon\www\mygooners\composer.json)

## 3. High-Level Architecture
### 3.1 Layers
- Controllers: HTTP entry points by domain (`Client`, `Admin`, `Mobile`)
- Models: Eloquent models for commerce/content/users
- Services: External/payment/invoice/email orchestration
- Views: Blade templates for client and admin pages
- Routes: Split into `web.php`, `api.php`, `console.php`

### 3.2 Key Directories
- `app/Http/Controllers/Client`
- `app/Http/Controllers/Admin`
- `app/Http/Controllers/Mobile`
- `app/Models`
- `app/Services`
- `routes`
- `resources/views`
- `database/migrations`
- `database/seeders`

## 4. Application Modules
### 4.1 Client Website
Main features:
- Home, blog/articles, services marketplace, product shop
- Cart and checkout flows
- Order history and timeline
- Retry payment (failed payments)
- Invoice view/download
- Refund requests
- Favourites/wishlist
- Profile and address management
- Product/service reviews

Main controllers:
- `HomeController`
- `ArticleController`
- `ServiceController`
- `ProductController`
- `CartController`
- `CheckoutController`
- `DirectCheckoutController`
- `RefundController`
- `FavouriteController`
- `ReviewController`
- `ServiceReviewController`
- `BillingDetailController`
- `ShippingDetailController`
- `NewsletterController`
- `AuthController`

### 4.2 Admin Panel
Main features:
- Dashboard and admin auth
- Article CRUD + rich content workflows
- Service and product moderation/management
- Order management (status/payment/invoice/export/stats)
- Refund approval workflow
- Product review and service review moderation
- Seller request verification (approve/reject)
- Settings management
- Log viewer and download/clear
- Newsletter management
- User management and account actions

Main controllers:
- `AdminController`
- `AuthController`
- `ArticleController`
- `ServiceController`
- `ProductController`
- `OrderController`
- `RefundController`
- `ProductReviewController`
- `ServiceReviewController`
- `SellerRequestController`
- `SettingsController`
- `LogController`
- `UserController`
- `VideoController`
- `NewsletterController`

### 4.3 Mobile API
Main features:
- Auth/login/register/google login
- Product listing/detail
- ToyyibPay payment creation and callback
- Order list/detail
- Order cancel and mark received
- Order review submission
- Invoice view/download endpoints
- Profile and password update

Main controllers:
- `Mobile\AuthController`
- `Mobile\ProductController`
- `Mobile\MobilePaymentController`
- `Mobile\MobileOrderController`
- `Mobile\MobileProfileController`

## 5. Routing Map
## 5.1 Web Routes
Reference: [routes/web.php](C:\laragon\www\mygooners\routes\web.php)

Major route groups:
- `/` (home)
- `/blog/*`
- `/services/*`
- `/shop/*`
- `/cart/*`
- `/checkout/*`
- `/direct-checkout/*`
- `/favourites/*`
- `/addresses/*`
- `/videos/*`
- `/newsletter/*`
- `/admin/*`

## 5.2 API Routes
Reference: [routes/api.php](C:\laragon\www\mygooners\routes\api.php)

Prefix: `/api/mobile`

Public:
- `POST /login`
- `POST /register`
- `POST /login/google`
- `POST /password/email`
- `GET /products`
- `GET /products/{id}`
- `POST /checkout/toyyibpay/callback`

Protected (`auth:sanctum`):
- `POST /logout`
- `POST /checkout/toyyibpay/create`
- `GET /checkout/toyyibpay/status/{billCode}`
- `GET /orders`
- `GET /orders/{id}`
- `POST /orders/{id}/cancel`
- `GET /orders/{id}/invoice/view`
- `GET /orders/{id}/invoice/download`
- `POST /orders/{id}/mark-received`
- `POST /orders/{id}/reviews`
- `GET /profile`
- `PUT /profile`
- `POST /change-password`

## 5.3 Console/Scheduler Routes
Reference: [routes/console.php](C:\laragon\www\mygooners\routes\console.php)

Scheduled commands:
- `orders:auto-mark-delivered` daily at `09:00`
- `orders:auto-cancel-pending` hourly

## 6. Data Model Summary
Key models:
- Commerce: `Order`, `OrderItem`, `Cart`, `CartItem`, `Product`, `ProductVariation`
- Reviews: `ProductReview`, `ProductReviewPhoto`, `ServiceReview`
- Services/content: `Service`, `Article`, `Video`
- User/account: `User`, `BillingDetail`, `ShippingDetail`, `Favourite`
- Refunds/settings: `Refund`, `RefundImage`, `Setting`
- Engagement: `Newsletter`

Key order-related fields:
- `orders.status`: `pending`, `processing`, `shipped`, `delivered`, `cancelled`, `refunded`
- `orders.payment_status`: string-based status (commonly `pending`, `paid`, `failed`, etc.)
- `orders.payment_method`: `toyyibpay` or `stripe`
- Shipping/billing details are snapshot columns on `orders`

## 7. Payments
## 7.1 ToyyibPay
Service: [ToyyibPayService.php](C:\laragon\www\mygooners\app\Services\ToyyibPayService.php)

Capabilities:
- Create bill
- Verify payment
- Reuse bill on retry
- Cancel-bill intent logging (no direct hard cancel API from provider)

Configuration keys:
- `TOYYIBPAY_SECRET_KEY`
- `TOYYIBPAY_CATEGORY_CODE`
- `TOYYIBPAY_BASE_URL`

## 7.2 Stripe
Service: [StripeService.php](C:\laragon\www\mygooners\app\Services\StripeService.php)

Capabilities:
- Create payment intent
- Reuse payment intent on retry
- Verify payment status
- Cancel payment intent (when cancellable)
- Webhook handling (`payment_intent.succeeded`, `payment_intent.payment_failed`)

Configuration keys:
- `STRIPE_SECRET`
- `STRIPE_KEY`
- `STRIPE_WEBHOOK_SECRET`
- `STRIPE_WEBHOOK_TOLERANCE`

## 8. Invoice System
Services/controllers:
- [InvoiceService.php](C:\laragon\www\mygooners\app\Services\InvoiceService.php)
- [CheckoutController.php](C:\laragon\www\mygooners\app\Http\Controllers\Client\CheckoutController.php)
- [DirectCheckoutController.php](C:\laragon\www\mygooners\app\Http\Controllers\Client\DirectCheckoutController.php)
- [Admin\OrderController.php](C:\laragon\www\mygooners\app\Http\Controllers\Admin\OrderController.php)
- [MobileOrderController.php](C:\laragon\www\mygooners\app\Http\Controllers\Mobile\MobileOrderController.php)

Flow:
- Generate invoice file from order data using Blade template `resources/views/pdf/invoice.blade.php`
- Support download and inline view
- Mobile now exposes secure invoice URLs and download endpoint for own orders

## 9. Order Lifecycle
Typical lifecycle:
1. `pending` (created)
2. `processing` (admin progression)
3. `shipped`
4. `delivered`

Terminal alternatives:
- `cancelled`
- `refunded`

Automations:
- Hourly auto-cancel stale pending unpaid orders
- Daily auto-mark shipped orders as delivered after threshold

## 10. Authentication and Authorization
### 10.1 Web Auth
- Session-based auth for client/admin portals
- Google OAuth supported

### 10.2 Mobile Auth
- Sanctum bearer token auth on protected mobile endpoints

### 10.3 Middleware
- `admin`: restrict to admin/super_admin/writer roles
- `writer`: additional route restrictions for writer role in admin area

References:
- [bootstrap/app.php](C:\laragon\www\mygooners\bootstrap\app.php)
- [AdminMiddleware.php](C:\laragon\www\mygooners\app\Http\Middleware\AdminMiddleware.php)
- [WriterAccessMiddleware.php](C:\laragon\www\mygooners\app\Http\Middleware\WriterAccessMiddleware.php)

## 11. Setup Guide (Local)
## 11.1 Prerequisites
- PHP 8+
- Composer
- Node.js + npm
- MySQL/MariaDB or SQLite

## 11.2 Install
1. `composer install`
2. `npm install`
3. Copy env: `.env.example` to `.env`
4. Generate app key: `php artisan key:generate`
5. Configure DB in `.env`
6. Run migrations: `php artisan migrate`
7. Link storage: `php artisan storage:link`
8. Seed data (optional): `php artisan db:seed`

## 11.3 Run
- Backend: `php artisan serve`
- Frontend: `npm run dev`

## 12. Environment Configuration
Reference templates:
- [.env.example](C:\laragon\www\mygooners\.env.example)
- [config/services.php](C:\laragon\www\mygooners\config\services.php)
- [config/sanctum.php](C:\laragon\www\mygooners\config\sanctum.php)

Important env groups:
- App: `APP_NAME`, `APP_ENV`, `APP_URL`, `APP_DEBUG`
- DB: `DB_*`
- Mail: `MAIL_*`
- Google OAuth: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- ToyyibPay: `TOYYIBPAY_SECRET_KEY`, `TOYYIBPAY_CATEGORY_CODE`, `TOYYIBPAY_BASE_URL`
- Stripe: `STRIPE_SECRET`, `STRIPE_KEY`, `STRIPE_WEBHOOK_SECRET`
- Sanctum SPA domains: `SANCTUM_STATEFUL_DOMAINS`

## 13. Testing and Quality
Available baseline commands:
- `php artisan test`
- `php artisan config:clear`
- `php artisan route:list`

Suggested additions for stronger coverage:
- Feature tests for checkout/payment callbacks
- API tests for mobile auth/order/invoice flows
- Regression tests for invoice generation and cancel rules

## 14. Operational Commands
Custom artisan commands:
- `orders:auto-cancel-pending`
- `orders:auto-mark-delivered`
- `test:invoice {order_id}`
- `docs:generate` (if mapped; see command class naming)
- `articles:reprocess` (if mapped; see command class naming)

Command classes:
- [AutoCancelPendingOrders.php](C:\laragon\www\mygooners\app\Console\Commands\AutoCancelPendingOrders.php)
- [AutoMarkOrdersAsDelivered.php](C:\laragon\www\mygooners\app\Console\Commands\AutoMarkOrdersAsDelivered.php)
- [TestInvoiceEmail.php](C:\laragon\www\mygooners\app\Console\Commands\TestInvoiceEmail.php)
- [GenerateDocumentationPDF.php](C:\laragon\www\mygooners\app\Console\Commands\GenerateDocumentationPDF.php)
- [ReprocessArticleContent.php](C:\laragon\www\mygooners\app\Console\Commands\ReprocessArticleContent.php)

## 15. Storage and Media
Media is served through explicit routes (article/service/product/variation/profile/seller assets).

Important implication:
- Production file permissions and `storage:link` must be correct.
- Missing files return 404 by route closures.

## 16. Security Notes
Current codebase includes several utility/testing routes in `web.php` (e.g. mail tests, smtp checks, ping tests, debug image helpers, and a composer update route).

Recommendation for production hardening:
- Remove or strictly gate any diagnostic/debug routes.
- Remove route executing shell commands (`/run-composer-update`) from production.
- Restrict any route exposing environment/config details.
- Ensure `APP_DEBUG=false` in production.
- Use HTTPS and secure cookie/session settings.

## 17. Recent Mobile Order API Enhancements
Implemented endpoints:
- `POST /api/mobile/orders/{id}/cancel`
- `GET /api/mobile/orders/{id}/invoice/view`
- `GET /api/mobile/orders/{id}/invoice/download`

Also added into mobile order list/detail payload:
- `can_cancel`
- `invoice_view_url`
- `invoice_download_url`

Ownership and access controls:
- Sanctum-protected routes
- User scoped to own orders only
- JSON responses for success/error paths

## 18. Recommended Documentation Maintenance Process
1. Update this document when routes/controllers/services change.
2. Add changelog entries for payment, order, or auth changes.
3. Keep Postman collection in sync with mobile API.
4. Add test evidence links when major flows are modified.
5. Review and prune debug routes before each production release.

## 19. Quick Reference Index
- Routing: [routes/web.php](C:\laragon\www\mygooners\routes\web.php), [routes/api.php](C:\laragon\www\mygooners\routes\api.php), [routes/console.php](C:\laragon\www\mygooners\routes\console.php)
- Mobile orders/payments: [MobileOrderController.php](C:\laragon\www\mygooners\app\Http\Controllers\Mobile\MobileOrderController.php), [MobilePaymentController.php](C:\laragon\www\mygooners\app\Http\Controllers\Mobile\MobilePaymentController.php)
- Website checkout: [CheckoutController.php](C:\laragon\www\mygooners\app\Http\Controllers\Client\CheckoutController.php), [DirectCheckoutController.php](C:\laragon\www\mygooners\app\Http\Controllers\Client\DirectCheckoutController.php)
- Admin order/refund: [OrderController.php](C:\laragon\www\mygooners\app\Http\Controllers\Admin\OrderController.php), [RefundController.php](C:\laragon\www\mygooners\app\Http\Controllers\Admin\RefundController.php)
- Services: [ToyyibPayService.php](C:\laragon\www\mygooners\app\Services\ToyyibPayService.php), [StripeService.php](C:\laragon\www\mygooners\app\Services\StripeService.php), [InvoiceService.php](C:\laragon\www\mygooners\app\Services\InvoiceService.php), [OrderEmailService.php](C:\laragon\www\mygooners\app\Services\OrderEmailService.php)
- Middleware: [AdminMiddleware.php](C:\laragon\www\mygooners\app\Http\Middleware\AdminMiddleware.php), [WriterAccessMiddleware.php](C:\laragon\www\mygooners\app\Http\Middleware\WriterAccessMiddleware.php)

---
Last updated: 2026-05-14
