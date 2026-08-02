# All Users — Functions, Attributes & Related Entities

**Rajabharana Jewellery Management System**

No diagrams — text reference for project report.  
✅ = implemented · 🔜 = planned (not in codebase yet)

---

## Shared: All logged-in users

| Function | Route | Related entities |
|----------|-------|------------------|
| Edit profile (name, email, phone, address, city, photo) | `/profile` | `users` |
| Update password | `/password` | `users` |
| Confirm password (sensitive actions) | `/confirm-password` | `users` |
| Verify email | `/verify-email` | `users` |
| Resend verification email | POST `/email/verification-notification` | `users` |
| Logout | POST `/logout` | `sessions` |

**Profile attributes editable:** name, email, phone, address, city, profile_photo_path

---

## 1. Guest (Public — not registered)

**Description:** Visitor without login. Cannot place orders until registered.

### User attributes
None — no `users` record.

### Related entities (read only)

| Entity | Access |
|--------|--------|
| `catalog_designs` | Browse, search, filter |
| `catalog_images` | View design photos |

### Functions

| # | Function | Route |
|---|----------|-------|
| 1 | View home page | `/` |
| 2 | Browse catalogue | `/catalog` |
| 3 | View design details | `/catalog/{id}` |
| 4 | Start purchase (redirects to register/login) | `/catalog/{id}/purchase` |
| 5 | Register new account | `/register` |
| 6 | Login | `/login` |
| 7 | Request password reset | `/forgot-password` |
| 8 | Reset password with token | `/reset-password/{token}` |

---

## 2. Customer

**Role value:** `customer`  
**Panel:** `/dashboard`, `/orders`  
**Description:** Registered buyer who places and tracks jewellery orders.

### User entity attributes (`users`)

| Attribute | Required | Description |
|-----------|:--------:|-------------|
| id | Yes | Primary key |
| name | Yes | Full name |
| email | Yes | Login email (unique) |
| email_verified_at | No | Email verification timestamp |
| password | Yes | Hashed password |
| role | Yes | Always `customer` |
| phone | Yes | Contact phone |
| address | Yes | Street address |
| city | Yes | City |
| profile_photo_path | No | Profile image |
| remember_token | No | Stay logged in |
| created_at / updated_at | No | Timestamps |

### Related entities

| Entity | Relationship | Operations |
|--------|--------------|------------|
| `users` | Self (1) | Read/update own profile |
| `orders` | places (R1) — `orders.user_id` | Create, read own, cancel own |
| `catalog_designs` | selects (R9) — `orders.catalog_design_id` | Read when placing catalogue order |
| `catalog_images` | via catalogue design | Read design photos |
| `metal_prices` | read only | View current gold/silver rates on dashboard |
| `sessions` | has_session (R-AUTH-1) | Active login sessions |
| `password_reset_tokens` | requests_reset (R-AUTH-2) | Password recovery |
| `invoices` 🔜 | billed (R5) | View own invoices (planned) |
| `payments` 🔜 | — | View payment history via invoice (planned) |
| `notifications` 🔜 | receives (R13) | Order status alerts (planned) |

### Functions ✅

| # | Function | Route | Entities touched |
|---|----------|-------|------------------|
| 1 | View customer dashboard (order stats, metal rates) | `/dashboard` | `orders`, `metal_prices` |
| 2 | List own orders | `/orders` | `orders` |
| 3 | Place new order (catalog or custom) | `/orders/create`, POST `/orders` | `orders`, `catalog_designs` |
| 4 | View own order detail | `/orders/{id}` | `orders`, `catalog_designs` |
| 5 | Cancel own order | PATCH `/orders/{id}/cancel` | `orders` |
| 6 | Browse public catalogue | `/catalog` | `catalog_designs`, `catalog_images` |
| 7 | View design before ordering | `/catalog/{id}` | `catalog_designs`, `catalog_images` |
| 8 | Edit profile | `/profile` | `users` |
| 9 | Update password | `/password` | `users` |
| 10 | Verify email | `/verify-email` | `users` |

### Functions 🔜 planned

| Function | Entities |
|----------|----------|
| View invoice for order | `invoices` |
| Download invoice PDF | `invoices` |
| Receive email/SMS order notifications | `notifications` |

### Cannot do
- Access `/admin/*` or `/technician/*`
- View other customers' orders
- Manage catalogue, metal prices, or staff
- Assign technicians

---

## 3. Sales Staff

**Role value:** `staff`  
**Panel:** `/admin/*` (permission-filtered)  
**Permissions:** `dashboard.view`, `orders.view`, `orders.manage`, `customers.view`, `catalog.view`

### User entity attributes (`users`)

| Attribute | Required | Description |
|-----------|:--------:|-------------|
| id | Yes | Primary key |
| name | Yes | Staff name |
| email | Yes | Login email (unique) |
| password | Yes | Hashed password |
| role | Yes | Always `staff` |
| profile_photo_path | No | Optional photo |
| remember_token | No | Stay logged in |
| created_at / updated_at | No | Timestamps |

*Staff accounts do not require phone/address on the user record (customer fields exist on table but are not used for staff workflow).*

### Related entities

| Entity | Relationship | Operations |
|--------|--------------|------------|
| `users` | Self | Read/update own profile |
| `users` (customers) | read via CustomersView | List/view customer accounts |
| `orders` | processes | List, view, update status/price/notes |
| `catalog_designs` | read | View catalogue (read-only) |
| `catalog_images` | read | View design images |
| `production_logs` | indirect | Visible on order detail in admin |
| `invoices` 🔜 | creates (R6 partial) | Generate invoice (planned) |
| `payments` 🔜 | records (R7) | Record customer payment (planned) |

### Functions ✅

| # | Function | Route | Entities touched |
|---|----------|-------|------------------|
| 1 | View admin dashboard (KPIs) | `/admin` | `orders`, `users` |
| 2 | List/filter/search orders | `/admin/orders` | `orders` |
| 3 | View order detail | `/admin/orders/{id}` | `orders`, `users`, `catalog_designs`, `production_logs` |
| 4 | Update order status | PATCH `/admin/orders/{id}` | `orders` |
| 5 | Set estimated price | PATCH `/admin/orders/{id}` | `orders` |
| 6 | Set expected delivery date | PATCH `/admin/orders/{id}` | `orders` |
| 7 | Add admin notes | PATCH `/admin/orders/{id}` | `orders` |
| 8 | List customers | `/admin/customers` | `users` (role=customer) |
| 9 | View customer profile & orders | `/admin/customers/{id}` | `users`, `orders` |
| 10 | View catalogue (read-only) | `/admin/catalog` | `catalog_designs`, `catalog_images` |
| 11 | Edit own profile | `/profile` | `users` |

### Functions 🔜 planned

| Function | Entities |
|----------|----------|
| Generate invoice from order | `invoices`, `invoice_items` |
| Record payment (Cash/Card/Bank) | `payments`, `invoices` |
| View billing reports | read-only queries |

### Cannot do
- Create/edit/delete catalogue designs or images
- Update metal prices
- Assign technicians / workshop queue
- Create/delete staff accounts
- Access `/technician/*`

---

## 4. Inventory Manager

**Role value:** `manager`  
**Panel:** `/admin/catalog/*`  
**Permissions:** `catalog.view`, `catalog.manage`

### User entity attributes (`users`)

| Attribute | Required | Description |
|-----------|:--------:|-------------|
| id | Yes | Primary key |
| name | Yes | Manager name |
| email | Yes | Login email (unique) |
| password | Yes | Hashed password |
| role | Yes | Always `manager` |
| profile_photo_path | No | Optional photo |
| created_at / updated_at | No | Timestamps |

### Related entities

| Entity | Relationship | Operations |
|--------|--------------|------------|
| `users` | Self | Read/update own profile |
| `catalog_designs` | creates/edits | Full CRUD |
| `catalog_images` | uploads (R8) | Upload, delete, set primary |
| `categories` 🔜 | proposed normalized table | Manage categories (doc only; currently `category` column on designs) |

### Functions ✅

| # | Function | Route | Entities touched |
|---|----------|-------|------------------|
| 1 | List catalogue designs | `/admin/catalog` | `catalog_designs`, `catalog_images` |
| 2 | Create new design | `/admin/catalog/create`, POST | `catalog_designs` |
| 3 | Edit design | `/admin/catalog/{id}/edit`, PATCH | `catalog_designs` |
| 4 | Delete design | DELETE `/admin/catalog/{id}` | `catalog_designs`, `catalog_images` |
| 5 | Upload design images | via create/edit forms | `catalog_images` |
| 6 | Delete design image | DELETE `.../images/{id}` | `catalog_images` |
| 7 | Set primary image | PATCH `.../images/{id}/primary` | `catalog_images` |
| 8 | Set availability status (in stock / out of stock) | PATCH design | `catalog_designs` |
| 9 | Edit own profile | `/profile` | `users` |

**Catalogue design attributes managed:** name, code, description, category, gold_quality, weight_grams, selling_price, availability_status

**Catalogue image attributes managed:** image_path, sort_order, is_primary

### Cannot do
- View/manage orders or customers
- Update metal prices
- Workshop / technician assignment
- Staff account management
- Access dashboard KPIs (redirects to catalogue if no dashboard permission)

---

## 5. Administrator

**Role value:** `admin`  
**Panel:** `/admin/*` (full access — permission `*`)  
**Description:** Full system control.

### User entity attributes (`users`)

Same structure as staff; `role = admin`. Has all permissions via wildcard `*`.

### Related entities

| Entity | Relationship | Operations |
|--------|--------------|------------|
| `users` | Self + staff management | CRUD staff accounts (admin, manager, staff, technician) |
| `users` (customers) | supervises | View all customers |
| `orders` | manages (R1 context) | Full order management |
| `orders` | assigns (R2) | Assign/unassign technician |
| `catalog_designs` | full | Same as Inventory Manager |
| `catalog_images` | full | Same as Inventory Manager |
| `metal_prices` | updates (R4) | Set daily gold/silver rates |
| `production_logs` | records (R3) | Via assignment & order updates |
| `invoices` 🔜 | creates (R6) | Billing |
| `payments` 🔜 | — | Oversight |
| `reports` 🔜 | generates | Analytics (read all tables) |

### Functions ✅ — All Sales Staff functions, plus:

| # | Function | Route | Entities touched |
|---|----------|-------|------------------|
| 1 | All Sales Staff order/customer functions | `/admin/*` | `orders`, `users` |
| 2 | All Inventory Manager catalogue functions | `/admin/catalog/*` | `catalog_designs`, `catalog_images` |
| 3 | Assign technician to order | PATCH `/admin/orders/{id}/assign-technician` | `orders`, `users` (technician), `production_logs` |
| 4 | View workshop production queue | `/admin/workshop` | `orders`, `production_logs` |
| 5 | View technician roster | `/admin/workshop/technicians` | `users` (technician) |
| 6 | View technician workload | `/admin/workshop/technicians/{id}` | `orders`, `users` |
| 7 | Update gold & silver prices | `/admin/metal-prices`, PATCH | `metal_prices`, `users` (updated_by) |
| 8 | List staff accounts | `/admin/users` | `users` |
| 9 | Create staff account | `/admin/users/create`, POST | `users` |
| 10 | Edit staff account | `/admin/users/{id}/edit`, PATCH | `users` |
| 11 | Delete staff account | DELETE `/admin/users/{id}` | `users` |

**Metal price attributes managed:** gold_price_per_gram, silver_price_per_gram, price_date, updated_by

**Staff user attributes managed:** name, email, password, role (admin/manager/staff/technician)

### Functions 🔜 planned

| Function | Entities |
|----------|----------|
| Generate invoice | `invoices`, `invoice_items` |
| Record/view all payments | `payments` |
| Generate system reports | all tables (read-only) |
| Configure notification templates | `notifications` |

---

## 6. Workshop Technician

**Role value:** `technician`  
**Panel:** `/technician/*`  
**Permission:** `production.manage`  
**Description:** Works assigned jobs only; **no customer PII** (name, email, phone, address hidden).

### User entity attributes (`users`)

| Attribute | Required | Description |
|-----------|:--------:|-------------|
| id | Yes | Primary key |
| name | Yes | Technician name |
| email | Yes | Login email (unique) |
| password | Yes | Hashed password |
| role | Yes | Always `technician` |
| profile_photo_path | No | Optional photo |
| created_at / updated_at | No | Timestamps |

### Related entities

| Entity | Relationship | Operations |
|--------|--------------|------------|
| `users` | Self | Read/update own profile |
| `orders` | assigned (R2) — `assigned_technician_id` | Read/update **assigned jobs only** |
| `production_logs` | records (R3) | Create log on status change |
| `catalog_designs` | read (specs only) | Item specs on job — no customer link shown |
| `catalog_images` | read | Reference images on job |

**Order fields visible to technician:** specifications, item_type, gold_quality, weight_grams, reference_image_path, catalog design specs, status — **NOT** contact_phone, delivery_address, customer name/email

### Functions ✅

| # | Function | Route | Entities touched |
|---|----------|-------|------------------|
| 1 | View assigned jobs dashboard | `/technician` | `orders` (assigned only) |
| 2 | View job detail (specifications) | `/technician/jobs/{id}` | `orders`, `catalog_designs`, `production_logs` |
| 3 | Update production status | PATCH `/technician/jobs/{id}` | `orders`, `production_logs` |
| 4 | Set status to Ready (when valid transition) | PATCH `/technician/jobs/{id}` | `orders` |
| 5 | Add workshop note | PATCH `/technician/jobs/{id}` | `production_logs` |
| 6 | View production log history | on job detail page | `production_logs` |
| 7 | Edit own profile | `/profile` | `users` |

**Valid status transitions:** In Production → Quality Check → Ready (system-enforced)

**Production log attributes written:** order_id, user_id, from_status, to_status, note, created_at

### Cannot do
- See customer phone, address, name, or email
- Assign self to jobs (admin only)
- Access `/admin/*`
- Place or cancel orders
- Manage catalogue or metal prices

---

## Entity summary by user type

| Entity | Guest | Customer | Staff | Manager | Admin | Technician |
|--------|:-----:|:--------:|:-----:|:-------:|:-----:|:----------:|
| users | — | own | own + read customers | own | all | own |
| sessions | — | ✓ | ✓ | ✓ | ✓ | ✓ |
| password_reset_tokens | request | ✓ | ✓ | ✓ | ✓ | ✓ |
| catalog_designs | read | read | read | CRUD | CRUD | read (job) |
| catalog_images | read | read | read | CRUD | CRUD | read (job) |
| orders | — | own CRUD | manage all | — | manage all | assigned update |
| metal_prices | — | read | — | — | CRUD | — |
| production_logs | — | — | read | — | read/create | create |
| invoices 🔜 | — | read own | create | — | full | — |
| payments 🔜 | — | — | record | — | full | — |
| notifications 🔜 | — | receive | — | — | configure | receive |

---

## Permission matrix (RBAC)

| Permission | Customer | Staff | Manager | Admin | Technician |
|------------|:--------:|:-----:|:-------:|:-----:|:----------:|
| dashboard.view | — | ✓ | — | ✓ | — |
| orders.view | own | ✓ | — | ✓ | assigned |
| orders.manage | own cancel | ✓ | — | ✓ | status only |
| customers.view | — | ✓ | — | ✓ | — |
| catalog.view | public | ✓ | ✓ | ✓ | job only |
| catalog.manage | — | — | ✓ | ✓ | — |
| metal-prices.manage | — | — | — | ✓ | — |
| users.manage | — | — | — | ✓ | — |
| production.view | — | — | — | ✓ | — |
| production.assign | — | — | — | ✓ | — |
| production.manage | — | — | — | ✓ | ✓ |

---

*Test accounts (seeder): admin@rajabharana.com · staff@rajabharana.com · manager@rajabharana.com · technician@rajabharana.com · customer@rajabharana.com*
