# 6.3 Implementation of the Program — Rajabharana Jewellery Management System

**Section:** System Implementation (Module-by-Module)  
**Stack:** Laravel 12 · PHP 8.2 · Blade · Tailwind · MySQL

| Resource | File |
|----------|------|
| **Printable view** | [`IMPLEMENTATION.html`](IMPLEMENTATION.html) |
| **Technology stack** | [`TECHNOLOGY_STACK.md`](TECHNOLOGY_STACK.md) |
| **Design patterns** | [`DESIGN_PATTERNS.md`](DESIGN_PATTERNS.md) |

---

## Introduction

The Rajabharana Jewellery Management System is implemented as a **web application** using Laravel's **Model–View–Controller (MVC)** architecture. Functionality is divided into **11 modules**, each covering a distinct business area. Every module consists of:

- **Models** — Eloquent classes mapped to database tables  
- **Controllers** — Handle HTTP requests and responses  
- **Form Requests** — Input validation and authorization  
- **Views** — Blade templates for the user interface  
- **Routes** — URL mapping with middleware protection  

The following sections describe how each module was implemented.

---

## Module 1 — Authentication and User Management

**Purpose:** Register users, authenticate login, manage sessions, and recover passwords.

| Item | Implementation |
|------|----------------|
| **Users** | Guest (register/login), all authenticated users |
| **Database tables** | `users`, `password_reset_tokens`, `sessions` |
| **Package** | Laravel Breeze |

### Key components

| Layer | Files |
|-------|-------|
| Controllers | `Auth\RegisteredUserController`, `AuthenticatedSessionController`, `PasswordResetLinkController`, `NewPasswordController`, `VerifyEmailController`, `ProfileController` |
| Model | `User` (role, phone, address, city, profile photo) |
| Views | `auth/login`, `auth/register`, `auth/forgot-password`, `auth/reset-password`, `auth/verify-email`, `profile/edit` |
| Routes | `routes/auth.php`, profile routes in `web.php` |

### Implemented features

1. Customer self-registration with email verification  
2. Session-based login and logout  
3. Forgot password / reset password flow  
4. Profile update (name, email, phone, address, profile photo)  
5. Account deletion with password confirmation  
6. Password change with strength rules (min 8 chars, mixed case, numbers)  
7. Role stored on `users.role` column (`UserRole` enum)

### Security

- Passwords hashed with Bcrypt  
- CSRF protection on all forms  
- Email verification required before customer features  
- Session stored in database (`sessions` table)

---

## Module 2 — Customer Portal

**Purpose:** Provide registered customers with a dashboard, order management, invoice viewing, and notifications.

| Item | Implementation |
|------|----------------|
| **Users** | Customer role only |
| **Middleware** | `EnsureCustomer` |
| **Route prefix** | `/dashboard`, `/orders/*`, `/notifications/*` |

### Key components

| Layer | Files |
|-------|-------|
| Controllers | `Customer\DashboardController`, `Customer\OrderController`, `Customer\InvoiceController`, `Customer\NotificationController` |
| Views | `customer/dashboard`, `customer/orders/*`, `customer/invoices/show`, `customer/notifications/index` |
| Layout | `AppLayout`, `layouts/navigation.blade.php` |

### Implemented features

1. **Dashboard** — Order statistics, recent orders, today's metal prices, overdue delivery alerts  
2. **Place order** — Catalog or custom design order form with image upload  
3. **My orders** — List all orders with status, price, and invoice link  
4. **Order detail** — Full specifications, delivery info, cancel (pending/confirmed only)  
5. **View invoice** — Read issued invoice with line items, tax, discount, payment history  
6. **Notifications** — In-app alerts when invoice issued or payment recorded  
7. **Profile** — Shared profile module for contact details required before ordering  

### Business rules

- Customer must complete phone and address before placing first order  
- Can only view own orders and invoices (`user_id` check)  
- Invoice visible only when status is issued (not draft)  
- Order cancellation limited to Pending and Confirmed statuses  

---

## Module 3 — Public Catalogue and Jewellery Design

**Purpose:** Display available jewellery designs to guests and customers; drive order placement.

| Item | Implementation |
|------|----------------|
| **Users** | Guest (browse), Customer (order from catalog) |
| **Database tables** | `catalog_designs`, `catalog_images` |
| **Route prefix** | `/catalog`, `/` (welcome) |

### Key components

| Layer | Files |
|-------|-------|
| Controllers | `CatalogController` (public) |
| Models | `CatalogDesign`, `CatalogImage` |
| Views | `welcome`, `catalog/index`, `catalog/show`, `layouts/public` |
| Enum | `AvailabilityStatus`, `DesignType` |

### Implemented features

1. Public home page with brand introduction  
2. Browse catalog designs filtered by category  
3. Design detail page with image gallery and specifications  
4. "Order this design" links to customer order form (pre-selected catalog item)  
5. Purchase/login redirect for guests attempting to order  
6. Only **available** designs shown on public catalog (`scopeAvailable`)  
7. Auto-generated catalog item codes (`RJ-YYYYMMDD-XXXX`)  

---

## Module 4 — Order Management

**Purpose:** Manage the full order lifecycle from customer submission to delivery.

| Item | Implementation |
|------|----------------|
| **Users** | Customer (place/view), Staff/Admin (manage) |
| **Database table** | `orders` |
| **Statuses** | Pending → Confirmed → In Production → Quality Check → Ready → Delivered / Cancelled |

### Key components — Customer side

| Layer | Files |
|-------|-------|
| Controller | `Customer\OrderController` |
| Form Request | `StoreOrderRequest` |
| Views | `customer/orders/create`, `index`, `show` |

### Key components — Admin side

| Layer | Files |
|-------|-------|
| Controller | `Admin\OrderController` |
| Form Requests | `UpdateOrderRequest`, `FilterOrdersRequest` |
| Views | `admin/orders/index`, `show` |
| Components | `order-status-badge`, `delivery-alert` |

### Implemented features

1. **Order creation** — Catalog orders auto-price from `selling_price × quantity`; custom orders accept reference image upload  
2. **Order listing (admin)** — Search by order #, customer name/email; filter by status; highlight due/overdue orders  
3. **Order management (admin)** — Update status, expected delivery date, quoted price, internal notes  
4. **Delivery tracking** — Overdue and due-soon alerts on admin dashboard, order list, and customer view  
5. **Auto order number** — `RJ-YYYYMMDD-XXXX` generated on creation  
6. **Specifications capture** — Item type, gold quality, weight, gemstones, size, quantity, special instructions  

### Business rules

- One order belongs to one customer  
- `estimated_price` set by admin for custom designs; auto for catalog  
- Status transitions controlled by admin (technician has limited transitions on assigned jobs)  
- Cancelled orders excluded from revenue totals  

---

## Module 5 — Workshop and Technician

**Purpose:** Manage production queue, assign technicians, and track workshop progress.

| Item | Implementation |
|------|----------------|
| **Users** | Admin/Staff (assign), Technician (update assigned jobs) |
| **Database tables** | `orders` (assignment fields), `production_logs` |
| **Route prefixes** | `/admin/workshop/*`, `/technician/*` |

### Key components

| Layer | Files |
|-------|-------|
| Admin controllers | `Admin\WorkshopController`, `Admin\OrderAssignmentController` |
| Technician controllers | `Technician\DashboardController`, `Technician\JobController` |
| Model | `ProductionLog` |
| Form Requests | `AssignTechnicianRequest`, `UpdateProductionJobRequest`, `FilterProductionRequest` |
| Views | `admin/workshop/*`, `technician/dashboard`, `technician/jobs/show` |
| Layout | `TechnicianLayout` |

### Implemented features

1. **Workshop queue (admin)** — View orders in production pipeline; filter by status  
2. **Technician list** — View all technicians and their assigned workload  
3. **Assign technician** — Assign/unassign from order detail page  
4. **Technician dashboard** — Shows only jobs assigned to logged-in technician  
5. **Job update** — Technician updates status (In Production → Quality Check → Ready)  
6. **Production log** — Every status change recorded with user, timestamp, and note  
7. **Assignment rules** — Only confirmed/in-production/quality-check orders assignable  

### Business rules

- Technician can only update orders assigned to them  
- Valid status transitions enforced in `Order::isValidTechnicianStatusTransition()`  
- Production log provides audit trail for workshop activity  

---

## Module 6 — Inventory (Catalog Management)

**Purpose:** Admin CRUD for catalog designs, images, pricing, and stock status.

| Item | Implementation |
|------|----------------|
| **Users** | Administrator, Inventory Manager |
| **Permissions** | `catalog.view`, `catalog.manage` |
| **Database tables** | `catalog_designs`, `catalog_images` |

### Key components

| Layer | Files |
|-------|-------|
| Controller | `Admin\CatalogDesignController` |
| Form Requests | `StoreCatalogDesignRequest`, `UpdateCatalogDesignRequest`, `FilterCatalogDesignRequest` |
| Views | `admin/catalog/index`, `create`, `edit`, `_form` |
| Config | `config/jewellery.php` → `catalog_categories`, `catalog_gold_qualities` |

### Implemented features

1. List all catalog designs with category, price, availability filter  
2. Create new design with multiple image uploads  
3. Edit design details (name, description, category, gold quality, weight, price)  
4. Set availability: Available / Out of Stock  
5. Delete designs and individual images  
6. Set primary catalog image  
7. Categories configured in `config/jewellery.php` (ring, necklace, bracelet, etc.)  

### Business rules

- Designs marked out-of-stock hidden from public catalog browse  
- Deleting a design nullifies `catalog_design_id` on related orders (FK nullOnDelete)  
- Item code auto-generated if not provided  

---

## Module 7 — Metal Price Management

**Purpose:** Publish daily gold and silver gram prices visible to customers and admin.

| Item | Implementation |
|------|----------------|
| **Users** | Administrator |
| **Permission** | `metal-prices.manage` |
| **Database table** | `metal_prices` |

### Key components

| Layer | Files |
|-------|-------|
| Controller | `Admin\MetalPriceController` |
| Form Request | `UpdateMetalPriceRequest` |
| Model | `MetalPrice` (`current()`, `upsertCurrent()`) |
| Views | `admin/metal-prices/edit` |

### Implemented features

1. Admin form to set gold and silver price per gram  
2. Prices dated daily; same-day updates overwrite existing row  
3. `MetalPrice::current()` returns latest price for dashboards  
4. Customer dashboard displays today's metal rates  
5. Tracks who last updated prices (`updated_by`)  

---

## Module 8 — Billing (Invoicing)

**Purpose:** Generate, edit, issue, and print invoices for confirmed orders.

| Item | Implementation |
|------|----------------|
| **Users** | Staff, Administrator (manage); Customer (view issued) |
| **Permissions** | `billing.view`, `billing.manage`, `billing.settings` |
| **Database tables** | `invoices`, `invoice_items`, `billing_settings`, `category_discounts` |

### Key components

| Layer | Files |
|-------|-------|
| Controllers | `Admin\InvoiceController`, `Admin\BillingSettingsController`, `Customer\InvoiceController` |
| Services | `InvoiceService`, `InvoiceCalculator` |
| Models | `Invoice`, `InvoiceItem`, `BillingSetting`, `CategoryDiscount` |
| Form Requests | `StoreInvoiceRequest`, `UpdateInvoiceRequest`, `FilterInvoicesRequest`, `UpdateBillingSettingsRequest` |
| Enum | `InvoiceStatus` (draft, issued, partial, paid, cancelled, overdue) |
| Views | `admin/invoices/*`, `admin/billing/settings`, `customer/invoices/show` |

### Implemented features

1. **Billing settings (admin)** — Configure global tax rate (%) and per-category discount (%)  
2. **Create draft invoice** — From billable order (confirmed+, price set, no existing invoice)  
3. **Auto-calculation** — Tax and category discount applied by `InvoiceCalculator`  
4. **Edit draft** — Adjust making charge, optional discount override, due date, notes  
5. **Issue invoice** — Locks invoice; customer notified; becomes viewable  
6. **Print invoice** — Printable tax invoice layout  
7. **Invoice list** — Search and filter by status  
8. **Customer view** — Read-only issued invoice on order page and notifications  
9. **One order → one invoice** — Enforced by unique `order_id` on invoices table  

### Business rules

```
grand_total = subtotal + making_charge + tax − discount
tax = (subtotal + making_charge − discount) × tax_rate / 100
discount = subtotal × category_discount_percent / 100
```

- Draft invoices editable; issued invoices locked  
- Invoice number format: `INV-YYYYMMDD-XXXX`  

---

## Module 9 — Payment

**Purpose:** Record partial and full customer payments against issued invoices.

| Item | Implementation |
|------|----------------|
| **Users** | Staff, Administrator (record); Customer (view history) |
| **Permission** | `billing.manage` |
| **Database tables** | `payments`, `payment_methods` |

### Key components

| Layer | Files |
|-------|-------|
| Controller | `Admin\PaymentController` |
| Service | `PaymentService` |
| Models | `Payment`, `PaymentMethod` |
| Form Request | `StorePaymentRequest` |
| Enum | `PaymentStatus` (completed, pending, failed, refunded) |
| Seeder | `PaymentMethodSeeder` (cash, card, bank_transfer) |

### Implemented features

1. Record payment on issued/partial/overdue invoices  
2. Support **partial payments** — multiple payment rows until balance cleared  
3. Payment methods: Cash, Card, Bank Transfer  
4. Transaction reference required for card and bank transfer  
5. Auto-update invoice status: Issued → **Partial** → **Paid**  
6. Payment history on admin and customer invoice views  
7. Customer notified on each payment via database notification  
8. `amount_paid` and `balance_due` computed from completed payments  

### Business rules

- Payment amount must be > 0 and ≤ balance due  
- Payment date cannot be before invoice issue date  
- Cannot cancel invoice once partial or paid  
- Only `completed` payments count toward balance  

---

## Module 10 — Notification

**Purpose:** Inform customers when billing events occur (invoice issued, payment received).

| Item | Implementation |
|------|----------------|
| **Users** | Customer (receive and view) |
| **Database table** | `notifications` (Laravel standard) |
| **Channel** | Database (in-app) |

### Key components

| Layer | Files |
|-------|-------|
| Notification classes | `InvoiceIssuedNotification`, `PaymentReceivedNotification` |
| Controller | `Customer\NotificationController` |
| View | `customer/notifications/index` |
| Provider | `AppServiceProvider` — unread count in navigation |

### Implemented features

1. Notification sent when staff **issues** an invoice  
2. Notification sent when staff **records** a payment  
3. Customer notification page with read/unread indicators  
4. Bell badge in navigation showing unread count  
5. Click notification → mark read and open invoice  
6. "Mark all as read" action  

### Notification data stored

- Message text, invoice number, order number, amounts, link URL  

---

## Module 11 — Role-Based Access Control (RBAC)

**Purpose:** Control which features each user role can access across all modules.

| Item | Implementation |
|------|----------------|
| **Config** | `config/rbac.php` |
| **Support class** | `App\Support\Rbac` |
| **Enums** | `UserRole`, `Permission` |

### Roles and access

| Role | Panel | Key access |
|------|-------|------------|
| **Customer** | Customer portal | Own orders, invoices, notifications, profile |
| **Sales Staff** | Admin panel | Orders, customers, billing, catalog view, dashboard |
| **Inventory Manager** | Admin panel | Catalog/inventory CRUD only |
| **Administrator** | Admin panel | Full access including staff accounts and billing settings |
| **Technician** | Workshop panel | Assigned production jobs only |
| **Guest** | Public site | Catalog browse, register, login |

### Middleware chain

```
HTTP Request → auth → verified → EnsureAdmin/EnsureCustomer/EnsureTechnician → EnsurePermission → Controller
```

| Middleware | File |
|------------|------|
| `EnsureCustomer` | Customer routes only |
| `EnsureAdmin` | Admin panel (Staff, Manager, Admin roles) |
| `EnsureTechnician` | Workshop panel |
| `EnsurePermission` | Fine-grained permission check per route |

### Permission examples

| Permission | Module |
|------------|--------|
| `orders.view`, `orders.manage` | Order Management |
| `catalog.view`, `catalog.manage` | Inventory |
| `billing.view`, `billing.manage`, `billing.settings` | Billing & Payment |
| `production.view`, `production.assign`, `production.manage` | Workshop |
| `users.manage` | Staff account management |
| `metal-prices.manage` | Metal Price |

### Blade authorization

Views use `@can('permission', 'billing.manage')` to show/hide action buttons.

---

## Module implementation summary table

| Module | Status | Models | Controllers | Views | Services |
|--------|--------|--------|-------------|-------|----------|
| M1 Authentication | ✅ | User | Auth/*, ProfileController | auth/*, profile/* | — |
| M2 Customer Portal | ✅ | User, Order | Customer/* | customer/* | — |
| M3 Public Catalogue | ✅ | CatalogDesign, CatalogImage | CatalogController | catalog/*, welcome | — |
| M4 Order Management | ✅ | Order | Customer/Admin OrderController | orders/* | — |
| M5 Workshop | ✅ | Order, ProductionLog | WorkshopController, Technician/* | workshop/*, technician/* | — |
| M6 Inventory | ✅ | CatalogDesign, CatalogImage | CatalogDesignController | admin/catalog/* | — |
| M7 Metal Price | ✅ | MetalPrice | MetalPriceController | admin/metal-prices/* | — |
| M8 Billing | ✅ | Invoice, InvoiceItem, BillingSetting, CategoryDiscount | InvoiceController, BillingSettingsController | admin/invoices/*, billing/settings | InvoiceService, InvoiceCalculator |
| M9 Payment | ✅ | Payment, PaymentMethod | PaymentController | (on invoice show) | PaymentService |
| M10 Notification | ✅ | notifications table | NotificationController | customer/notifications/* | InvoiceIssuedNotification, PaymentReceivedNotification |
| M11 RBAC | ✅ | User | Middleware | — | Rbac |

---

## Database tables by module

| Module | Tables |
|--------|--------|
| M1 Auth | users, password_reset_tokens, sessions |
| M3/M6 Catalog | catalog_designs, catalog_images |
| M4/M5 Orders | orders, production_logs |
| M7 Metal Price | metal_prices |
| M8 Billing | invoices, invoice_items, billing_settings, category_discounts |
| M9 Payment | payments, payment_methods |
| M10 Notification | notifications |

**Total: 15 tables**

---

## Report paragraph (copy-paste)

> The program was implemented module-by-module using Laravel MVC architecture. The **Authentication module** (Laravel Breeze) handles registration, login, and profile management. The **Customer Portal** provides dashboard, order placement, and invoice viewing. The **Public Catalogue module** displays jewellery designs to guests. **Order Management** covers the full order lifecycle with admin status and pricing controls. The **Workshop module** manages technician assignment and production logging. **Inventory Management** provides admin CRUD for catalog designs and images. **Metal Price Management** publishes daily gold and silver rates. The **Billing module** generates invoices with automatic tax and category discount calculation via a Service Layer. The **Payment module** supports partial and full payments with status synchronization. The **Notification module** alerts customers on billing events. All modules are protected by **Role-Based Access Control** using middleware and a permission configuration file. Each module follows a consistent structure of Models, Controllers, Form Requests, and Blade views, ensuring maintainability and clear separation of concerns.

---

*Section 6.3 · Implementation of the Program · Rajabharana Jewellery Management System*
