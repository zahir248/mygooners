# Project Handover Document

**Project:** MyGooners  
**Last updated:** June 2026  
**Repositories:** Laravel backend (`Desktop/mygooners`) + Flutter mobile app (`mygooners`)

---

## 1. Project Summary

- **Project Name:** MyGooners
- **Purpose of the system:** Online marketplace and e-commerce platform for Arsenal merchandise, services, and content. It supports web shopping, admin operations, and a companion mobile app.
- **Main features:**
  - Product shop with cart, checkout, orders, invoices, and refunds
  - Services marketplace with seller onboarding and moderation
  - Blog/articles and video content
  - Admin panel for catalog, orders, users, settings, and logs
  - Mobile app for product browsing, checkout, ToyyibPay payments, and order tracking
  - Multi-language support (English and Bahasa Melayu)
- **Target users:**
  - Customers (web and mobile)
  - Sellers (service providers)
  - Administrators, writers, and super admins

---

## 2. System Overview

- **Frontend technology:**
  - **Web:** Blade templates, Tailwind CSS, Vite
  - **Mobile:** Flutter (Material 3), Dart `^3.10.0`
- **Backend technology:** Laravel 12, PHP 8+, Laravel Sanctum (mobile API auth), Laravel Socialite (Google OAuth)
- **Database technology:** MySQL/MariaDB in production; SQLite supported for local development
- **Overall architecture:** Monolithic Laravel backend serving three interfaces — client website, admin panel, and REST mobile API (`/api/mobile/*`). The Flutter app consumes the mobile API. Payments, invoices, and emails are handled in backend service classes. Scheduled jobs manage order lifecycle automation.

```
[Flutter Mobile App] ──REST/Sanctum──► [Laravel Backend] ──► [MySQL]
[Web Browser]        ──Session───────► [Laravel Backend] ──► [MySQL]
[Admin Panel]        ──Session───────► [Laravel Backend] ──► [MySQL]
                                              │
                                              ├── ToyyibPay / Stripe
                                              ├── SMTP (order emails)
                                              └── Google OAuth
```

---

## 3. Project Structure

Brief explanation of important folders and files.

| Folder/File | Purpose |
|------------|----------|
| `app/Http/Controllers/Client/` | Client website controllers (shop, cart, checkout, profile) |
| `app/Http/Controllers/Admin/` | Admin panel controllers (orders, products, settings, users) |
| `app/Http/Controllers/Mobile/` | Mobile API controllers (auth, products, orders, payments) |
| `app/Models/` | Eloquent models (Order, Product, User, etc.) |
| `app/Services/` | Business logic (ToyyibPay, Stripe, Invoice, OrderEmail) |
| `routes/web.php` | Web and admin routes |
| `routes/api.php` | Mobile API routes under `/api/mobile` |
| `routes/console.php` | Scheduled tasks (auto-cancel, auto-deliver) |
| `resources/views/` | Blade templates (client, admin, emails, PDF invoice) |
| `database/migrations/` | Database schema definitions |
| `config/services.php` | Third-party integration credentials (Google, ToyyibPay, Stripe) |
| `.env` / `.env.example` | Environment configuration |
| `public/` | Web root; assets and entry point |
| `storage/` | Uploads, logs, generated invoices (requires write permissions) |
| `CPANEL_DEPLOYMENT_GUIDE.md` | Production deployment instructions |
| `PROJECT_DOCUMENTATION.md` | Detailed technical reference |
| **Mobile repo (`mygooners/`)** | |
| `lib/screens/` | Flutter UI screens |
| `lib/services/` | API and local state services |
| `lib/config/api_config.dart` | API base URL and endpoints |
| `lib/localization/` | EN/MS language support |
| `android/`, `ios/` | Native mobile project files |

---

## 4. Setup & Run

### Backend (Laravel)

1. **Prerequisites:** PHP 8+, Composer, Node.js/npm, MySQL (or SQLite for quick local setup)
2. **Installation:**
   ```bash
   cd Desktop/mygooners
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   ```
3. **Environment variables required:**

   | Variable | Purpose |
   |----------|---------|
   | `APP_NAME`, `APP_ENV`, `APP_URL`, `APP_DEBUG`, `APP_KEY` | Core app config |
   | `DB_*` | Database connection |
   | `MAIL_*` | Email (order confirmation, invoices, newsletters) |
   | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` | Google OAuth (web + mobile) |
   | `TOYYIBPAY_SECRET_KEY`, `TOYYIBPAY_CATEGORY_CODE`, `TOYYIBPAY_BASE_URL` | ToyyibPay payments |
   | `STRIPE_SECRET`, `STRIPE_KEY`, `STRIPE_WEBHOOK_SECRET` | Stripe payments (web) |
   | `SANCTUM_STATEFUL_DOMAINS` | Sanctum SPA/mobile domain config |

4. **Run commands:**
   ```bash
   php artisan serve          # Backend at http://127.0.0.1:8000
   npm run dev                # Vite asset dev server
   # Or combined:
   composer run dev
   ```

### Mobile (Flutter)

1. **Prerequisites:** Flutter SDK (Dart `^3.10.0`), Android Studio and/or Xcode
2. **Installation:**
   ```bash
   cd mygooners
   flutter pub get
   ```
3. **Environment variables required:** Configure API host in `lib/config/api_config.dart` (uncomment the appropriate `baseUrl` line):
   - Production: `https://mygooners.my`
   - Local LAN: `http://<PC_IP>:8000`
   - Android emulator: `http://10.0.2.2:8000`
4. **Run commands:**
   ```bash
   flutter run
   flutter test
   flutter analyze
   ```

---

## 5. Deployment

- **Hosting/server:** Production site at **https://mygooners.my**, deployed on **cPanel** hosting (PHP 8.1+, MySQL 5.7+). See `CPANEL_DEPLOYMENT_GUIDE.md`.
- **Deployment process:**
  1. Upload Laravel files to server (including `.env`)
  2. Run `composer install --optimize-autoloader --no-dev`
  3. Set storage/bootstrap permissions (`775` on `storage/`, `bootstrap/cache/`)
  4. Run `php artisan migrate --force`, `php artisan storage:link`
  5. Cache config/routes/views: `php artisan config:cache`, `route:cache`, `view:cache`
  6. Ensure cron runs Laravel scheduler: `* * * * * php /path/to/artisan schedule:run`
- **Important deployment notes:**
  - Set `APP_DEBUG=false` and `APP_ENV=production` in production
  - Remove or restrict debug/test routes in `routes/web.php` before release
  - Disable maintenance mode in Admin → Settings when deploying mobile API changes
  - Mobile APK is distributed via GitHub releases (`MOBILE_APP_ANDROID_URL` in `.env`)
  - Android release signing keystore still needs production setup in the mobile repo

---

## 6. Database Overview

List of important tables only.

| Table | Purpose |
|--------|---------|
| `users` | All user accounts (customers, sellers, admins); roles and seller details |
| `personal_access_tokens` | Sanctum API tokens for mobile auth |
| `products` | Product catalog with variations and seller ownership |
| `product_variations` | Size/color/price variants for products |
| `product_categories` | Product category taxonomy |
| `product_reviews` | Customer product reviews (linked to orders) |
| `product_review_photos` | Photos attached to product reviews |
| `services` | Marketplace service listings |
| `service_categories` | Service category taxonomy |
| `service_reviews` | Reviews for services |
| `orders` | Order header (status, payment, shipping/billing snapshots) |
| `order_items` | Line items within each order |
| `carts` / `cart_items` | Session/user shopping carts (web) |
| `billing_details` | Saved customer billing addresses |
| `shipping_details` | Saved customer shipping addresses |
| `favourites` | User wishlist/favourites (web) |
| `refunds` / `refund_images` | Refund requests and supporting images |
| `articles` / `article_categories` | Blog content |
| `videos` / `video_categories` | Video content |
| `newsletters` | Newsletter subscriber records |
| `settings` | Admin-configurable app settings (payment toggles, maintenance mode) |
| `jobs` / `cache` | Queue jobs and database cache |

---

## 7. API & Integrations

| Integration | Purpose |
|------------|---------|
| **ToyyibPay** | Primary payment gateway (Malaysia); used on web checkout and mobile app |
| **Stripe** | Card payments on web checkout (toggleable via admin settings) |
| **Google OAuth (Socialite)** | Sign-in with Google on web and mobile |
| **SMTP / Mail** | Order confirmations, invoice emails, password reset, newsletters |
| **DomPDF** | PDF invoice generation and download |
| **Google Analytics (gtag)** | Web traffic tracking (`G-D7JL8SL7SN` in `layouts/app.blade.php`) |
| **Facebook / Instagram embeds** | Social content embedded in blog articles |
| **Laravel Scheduler** | Hourly auto-cancel of unpaid orders; daily auto-mark shipped orders as delivered |

**Mobile API base path:** `/api/mobile`  
Key public endpoints: login, register, Google login, products, ToyyibPay callback  
Key protected endpoints (Sanctum token): orders, profile, checkout, invoice download

---

## 8. Access & Credentials

DO NOT store or share actual secrets in this document.

| Access Type | Where It Is Stored |
|------------|-------------------|
| Database credentials | Server `.env` (`DB_*` variables) |
| Laravel app key | Server `.env` (`APP_KEY`) |
| SMTP / email | Server `.env` (`MAIL_*` variables) |
| Google OAuth | Server `.env` + Google Cloud Console; mobile client IDs in `lib/config/api_config.dart` |
| ToyyibPay API keys | Server `.env` (`TOYYIBPAY_*`); never exposed to mobile app |
| Stripe API keys | Server `.env` (`STRIPE_*`) |
| Sanctum tokens | Generated at login; stored in mobile `SharedPreferences` (`auth_token`) |
| Admin panel access | `users` table with admin/super_admin/writer roles |
| cPanel / server SSH | Hosting provider account (not in repository) |
| Android signing keystore | Not Identified (needs production setup) |

---

## 9. Important Notes

**Known issues / limitations:**
- Mobile wishlist, address book, and review state are in-memory only (reset on app restart); only cart is persisted locally
- Debug and test routes exist in `routes/web.php` (mail tests, SMTP checks, composer update route) — must be removed or gated in production
- `.env.example` defaults to SQLite; production uses MySQL
- Maintenance mode in admin settings blocks the mobile API

**Common troubleshooting tips:**
- **Mobile cannot reach API locally:** Use PC LAN IP (not `localhost`), same Wi-Fi, and `php artisan serve --host=0.0.0.0 --port=8000`. Turn off maintenance mode in admin.
- **Invoice/PDF fails on cPanel:** Check `storage/` permissions and run `php artisan storage:link`. Test with `php artisan test:invoice {order_id}`.
- **Payment callback issues:** Verify ToyyibPay/Stripe keys in `.env` and that callback URLs match production domain.
- **Images not loading on mobile:** API may return `localhost` URLs; `ApiConfig.normalizeImageUrl()` rewrites them for device use.
- **Scheduled tasks not running:** Ensure server cron executes `php artisan schedule:run` every minute.

**Things future developers should know:**
- Two separate repos: Laravel backend and Flutter mobile app — keep API contracts in sync
- Admin settings (`/admin/settings`) control payment method visibility and maintenance mode without code changes
- Order statuses: `pending` → `processing` → `shipped` → `delivered` (or `cancelled` / `refunded`)
- User roles: `admin`, `super_admin`, `writer` (limited admin access), `seller`, regular customer
- Detailed docs: `PROJECT_DOCUMENTATION.md` (backend), `PROJECT_DOCUMENTATION.md` (mobile repo), `GOOGLE_SIGNIN_SETUP.md`, `TOYYIBPAY_INTEGRATION_SUMMARY.md`
- i18n: Web uses `resources/lang/`; mobile uses `lib/localization/`

---

*For deeper technical detail, refer to `PROJECT_DOCUMENTATION.md` in each repository.*
