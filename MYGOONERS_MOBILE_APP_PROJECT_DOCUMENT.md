# MyGooners Mobile E-Commerce Application

## 1. Project Overview

**Project Name:** MyGooners Mobile E-Commerce Application

The proposed project is the development of a native mobile application for the MyGooners e-commerce platform, designed to provide customers with a seamless shopping experience on iOS and Android devices. The mobile app will focus exclusively on e-commerce functionality, enabling users to browse products, manage shopping carts, complete purchases, track orders, and manage their account—all from their mobile devices.

As a native mobile application, the system will leverage device-specific features such as push notifications, biometric authentication, camera integration for product reviews, and offline browsing capabilities. The app will provide a faster, more intuitive shopping experience compared to the web platform, with optimized performance for mobile networks and touch-based interactions.

**Target Users:**

The primary users include all customers who want to shop for products through their mobile devices. The app will serve as an alternative and enhanced shopping channel to the existing web platform, providing convenience and improved user experience for mobile-first users.

**Access Method:**

The application will be available for download from:
- **Apple App Store** (iOS devices: iPhone and iPad)
- **Google Play Store** (Android devices: smartphones and tablets)

Users will need to install the app on their devices, create an account or log in with existing credentials, and can then access all e-commerce features.

---

## 2. Problem Statement

Currently, customers access the MyGooners e-commerce platform through web browsers on their mobile devices. While the web platform is responsive and functional, it lacks the native mobile experience that users expect. Mobile web browsing presents several challenges:

1. **Limited Mobile Optimization:** Web interfaces cannot fully leverage native device features such as push notifications, biometric authentication, and camera access.

2. **Performance Issues:** Web applications tend to be slower on mobile devices, especially on slower network connections, leading to higher bounce rates and abandoned carts.

3. **User Engagement:** Without push notifications, customers miss important updates about order status, promotions, and product availability.

4. **Offline Limitations:** Web applications require constant internet connectivity, preventing users from browsing products or viewing order history when offline.

5. **Installation Friction:** Users must remember to bookmark the website and navigate to it manually, reducing repeat engagement compared to native apps.

These limitations result in reduced customer engagement, lower conversion rates, and missed opportunities for customer retention through push notifications and personalized experiences.

---

## 3. Proposed Solution

The proposed solution is a native mobile application built with Flutter that provides a dedicated, optimized e-commerce experience for iOS and Android devices. The app will integrate seamlessly with the existing MyGooners Laravel backend API, ensuring consistency with the web platform while delivering superior mobile performance and user experience.

The mobile app will feature:
- **Native Performance:** Fast loading times and smooth animations optimized for mobile devices
- **Push Notifications:** Real-time alerts for order updates, promotions, and cart reminders
- **Offline Support:** Ability to browse cached products and view order history without internet connectivity
- **Biometric Authentication:** Secure login using fingerprint or face recognition
- **Camera Integration:** Easy product review photo uploads using device camera
- **Optimized Checkout:** Streamlined payment process with saved payment methods and addresses

---

## 4. User Roles & Access Levels

The mobile application will implement Role-Based Access Control (RBAC) aligned with the existing web platform:

•	**Customer users:** Can browse products, add items to cart, complete purchases, track orders, submit reviews, request refunds, manage profile and addresses, and receive push notifications.

•	**Administrators:** Access to existing web admin panel (no mobile admin app in Phase 1). Administrators will manage products, orders, and refunds through the web interface.

---

## 5. Functional Requirements

### A. For All Customers

•	Secure login using email/password or Google OAuth.

•	Browse product catalog with categories, search, and filters.

•	View product details with images, descriptions, variations, and reviews.

•	Add products to cart with quantity and variation selection.

•	Manage shopping cart (update quantities, remove items).

•	Complete checkout with shipping/billing address selection.

•	Make payments using Stripe (Credit/Debit Cards) or ToyyibPay (FPX Bank Transfer, Credit Cards).

•	View and track order status (Pending Payment, Processing, Shipped, Delivered, Cancelled).

•	View order history and download invoices.

•	Submit product reviews with ratings and photos.

•	Request refunds with photo evidence.

•	Manage favorites/wishlist.

•	Receive push notifications for order updates, promotions, and cart reminders.

•	Offline browsing of cached products and order history.

### B. Mobile-Specific Features

•	Biometric authentication (Face ID / Touch ID / Fingerprint).

•	Camera access for review photos.

•	Push notifications for order updates and promotions.

•	Offline support for browsing cached content.

•	Deep linking for product pages.

---

## 6. Non-Functional Requirements

•	**Usability:** Intuitive interface following iOS and Android design guidelines, suitable for users of varying technical expertise.

•	**Performance:** Fast response times, smooth scrolling (60 FPS), optimized image loading.

•	**Security:** Secure API communication (HTTPS/TLS), token-based authentication, secure local storage, PCI-DSS compliance for payment processing.

•	**Availability:** 99.5% uptime (dependent on backend API availability), graceful error handling and offline fallbacks.

•	**Scalability:** Support for increasing product catalog size, efficient handling of large product images, optimized for varying network conditions.

•	**Compatibility:** Support for iOS 13.0+ and Android 8.0+ (API level 26+), various screen sizes and resolutions.

---

## 7. Mobile Application Architecture

The system will follow a standard mobile application architecture:

•	Users interact with the Flutter mobile app on iOS and Android devices.

•	The Flutter app communicates with the existing Laravel backend via RESTful API (HTTPS).

•	The Laravel backend handles business logic including product management, order processing, payments, and notifications.

•	A centralized MySQL database stores product data, user details, orders, reviews, and audit logs.

•	External services integrate for payments (Stripe, ToyyibPay) and push notifications (OneSignal).

---

## 8. Proposed Technology Stack

The MyGooners Mobile E-Commerce Application will use a Flutter-based mobile architecture:

•	**Frontend – Flutter:** Flutter framework with Dart programming language will provide cross-platform mobile app development, allowing single codebase for both iOS and Android with native performance.

•	**Backend – Laravel API:** Existing Laravel backend will be extended with mobile-specific API endpoints, handling authentication, product catalog, order management, payment processing, and push notifications.

•	**Database – MySQL:** Existing MySQL database stores all products, user data, orders, reviews, and audit trails; fully compatible with the current Laravel backend.

•	**Authentication & Authorization:** Secure login with email/password or Google OAuth, JWT token-based authentication, and role-based access control enforced via Laravel middleware.

•	**State Management:** Provider or Riverpod for Flutter state management.

•	**Local Storage:** Shared Preferences or Hive for offline data caching and local storage.

•	**HTTP Client:** Dio for API communication.

•	**Push Notifications – OneSignal:** OneSignal will be integrated to send automated notifications and reminders for order updates, payment confirmations, shipping updates, and promotional offers. This allows customers to receive real-time updates directly on their mobile devices, improving responsiveness and engagement.

•	**Payment Integration:** Flutter Stripe SDK for credit card payments, custom ToyyibPay integration for FPX bank transfers.

•	**Image Handling:** Cached Network Image for optimized image loading and caching.

•	**Biometric Authentication:** Local Auth package for fingerprint and face recognition.

•	**Camera Integration:** Image Picker package for product review photos.

---

## 9. Key Screens / Modules

### Common (All Users)

•	Login / Registration

•	Home / Product Catalog

•	Product Detail

•	Shopping Cart

•	Checkout / Payment

•	Order History / Tracking

•	Product Reviews

•	Refund Requests

•	Profile / Settings

•	Favorites / Wishlist

•	Notifications

---

## 10. Data & Audit Transparency

•	All actions (orders, payments, reviews) are logged with timestamp and user information via backend API.

•	Full history of orders and transactions is maintained in MySQL database.

•	Exportable order reports and invoices in PDF format for customer review.

•	Secure handling of payment information with PCI-DSS compliance.

---

## 11. Implementation Plan (3-Month Timeline)

| Phase | Description | Key Deliverables | Duration |
|-------|-------------|------------------|----------|
| **Phase 1: Planning & Setup** | Finalize requirements, create UI/UX designs, set up Flutter development environment, review API documentation | - Requirements documentation<br>- UI/UX mockups<br>- Flutter project setup<br>- API integration plan | 2 weeks<br>(Weeks 1–2) |
| **Phase 2: Backend API Preparation** | Extend existing Laravel API for mobile app, implement mobile-specific endpoints, set up push notifications | - Mobile API endpoints<br>- JWT authentication<br>- Push notification service<br>- API documentation | 2 weeks<br>(Weeks 3–4) |
| **Phase 3: Core Development** | Build core mobile app features: authentication, product browsing, cart, checkout, orders | - Authentication module<br>- Product catalog module<br>- Shopping cart module<br>- Checkout and payment<br>- Order management | 6 weeks<br>(Weeks 5–10) |
| **Phase 4: Advanced Features & Testing** | Implement reviews, refunds, notifications, offline support, and comprehensive testing | - Review system<br>- Refund management<br>- Push notifications<br>- Offline support<br>- Testing and bug fixes | 3 weeks<br>(Weeks 11–13) |
| **Phase 5: Deployment & Launch** | Submit to app stores, configure production services, launch and monitor | - App Store submission<br>- Play Store submission<br>- Production deployment<br>- User guide<br>- Post-launch monitoring | 1 week<br>(Week 14) |

**Total Duration: 14 weeks (approximately 3 months)**

---

## 12. Success Metrics

•	Number of app downloads from App Store and Play Store

•	Daily/Monthly Active Users (DAU/MAU)

•	User retention rate (Day 1, Day 7, Day 30)

•	Conversion rate (visitors to purchasers)

•	Average order value (AOV)

•	Number of orders placed through mobile app

•	App crash rate (< 0.1%)

•	User satisfaction rating (App Store/Play Store reviews)

---

## 13. Optional Enhancements

•	Barcode scanner to find products

•	Voice search for products

•	Personalized product recommendations

•	Loyalty program with points and rewards

•	Live chat support

•	Dark mode theme

•	Multi-language support (additional languages)

---

**Document Version:** 1.0  
**Last Updated:** January 2025  
**Project:** MyGooners Mobile E-Commerce Application  
**Status:** Proposal / Planning Phase
