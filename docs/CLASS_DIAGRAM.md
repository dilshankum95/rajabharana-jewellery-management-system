# Whole System Class Diagram — Rajabharana Jewellery System

**Stack:** Laravel 12 · Eloquent ORM · Blade · MySQL  
**Mermaid:** 10.9.8 compatible syntax

| Resource | File |
|----------|------|
| **Visual (browser)** | [`CLASS_DIAGRAM.html`](CLASS_DIAGRAM.html) |
| **Architecture overview** | [`SYSTEM_ARCHITECTURE.md`](SYSTEM_ARCHITECTURE.md) |

**Legend:** `#field` = computed accessor (not stored in DB)

---

## Figure 1 — User and Authentication

```mermaid
classDiagram
    direction TB
    class User {
        <<Model>>
        +id bigint
        +name string
        +email string
        +email_verified_at timestamp
        +password string
        +remember_token string
        +role UserRole
        +phone string
        +address string
        +city string
        +profile_photo_path string
        +created_at timestamp
        +updated_at timestamp
        #profile_photo_url string
        #initials string
        +orders() HasMany
        +invoices() HasMany
        +assignedOrders() HasMany
        +hasPermission() bool
    }
    class UserRole {
        <<enum>>
        +Customer
        +Admin
        +Manager
        +Staff
        +Technician
    }
    User --> UserRole : role
```

---

## Figure 2 — Catalog and Inventory

```mermaid
classDiagram
    direction TB
    class CatalogDesign {
        <<Model>>
        +id bigint
        +name string
        +code string
        +description text
        +category string
        +gold_quality string
        +weight_grams decimal
        +selling_price decimal
        +availability_status AvailabilityStatus
        +created_at timestamp
        +updated_at timestamp
        #image_url string
        +images() HasMany
        +orders() HasMany
    }
    class CatalogImage {
        <<Model>>
        +id bigint
        +catalog_design_id bigint
        +image_path string
        +sort_order int
        +is_primary bool
        +created_at timestamp
        +updated_at timestamp
        #url string
        +catalogDesign() BelongsTo
    }
    class AvailabilityStatus {
        <<enum>>
        +Available
        +OutOfStock
    }
    CatalogDesign "1" --> "*" CatalogImage
    CatalogDesign --> AvailabilityStatus
```

---

## Figure 3 — Order and Production

```mermaid
classDiagram
    direction TB
    class Order {
        <<Model>>
        +id bigint
        +order_number string
        +user_id bigint
        +design_type DesignType
        +catalog_design_id bigint
        +reference_image_path string
        +item_type string
        +item_name string
        +size string
        +weight_grams decimal
        +specifications text
        +gold_quality string
        +gemstone_type string
        +gemstone_details text
        +quantity int
        +special_instructions text
        +expected_delivery_date date
        +contact_phone string
        +delivery_address text
        +status OrderStatus
        +estimated_price decimal
        +admin_notes text
        +assigned_technician_id bigint
        +assigned_at datetime
        +created_at timestamp
        +updated_at timestamp
        +user() BelongsTo
        +catalogDesign() BelongsTo
        +assignedTechnician() BelongsTo
        +productionLogs() HasMany
        +invoice() HasOne
    }
    class ProductionLog {
        <<Model>>
        +id bigint
        +order_id bigint
        +user_id bigint
        +from_status OrderStatus
        +to_status OrderStatus
        +note text
        +created_at timestamp
        +updated_at timestamp
        +order() BelongsTo
        +user() BelongsTo
    }
    class DesignType {
        <<enum>>
        +Catalog
        +Custom
    }
    class OrderStatus {
        <<enum>>
        +Pending
        +Confirmed
        +InProduction
        +QualityCheck
        +Ready
        +Delivered
        +Cancelled
    }
    User "1" --> "*" Order : places
    User "1" --> "*" Order : assigned_to
    CatalogDesign "1" --> "*" Order
    Order "1" --> "*" ProductionLog
    Order --> DesignType
    Order --> OrderStatus
```

---

## Figure 4 — Billing and Payment

```mermaid
classDiagram
    direction TB
    class Invoice {
        <<Model>>
        +id bigint
        +invoice_number string
        +order_id bigint
        +customer_id bigint
        +subtotal decimal
        +making_charge decimal
        +discount decimal
        +discount_percent decimal
        +tax decimal
        +tax_rate_percent decimal
        +grand_total decimal
        +invoice_status InvoiceStatus
        +issue_date date
        +due_date date
        +notes text
        +created_by bigint
        +created_at timestamp
        +updated_at timestamp
        #amount_paid float
        #balance_due float
        +items() HasMany
        +payments() HasMany
    }
    class InvoiceItem {
        <<Model>>
        +id bigint
        +invoice_id bigint
        +order_id bigint
        +description string
        +quantity int
        +unit_price decimal
        +line_total decimal
        +created_at timestamp
        +updated_at timestamp
    }
    class Payment {
        <<Model>>
        +id bigint
        +invoice_id bigint
        +payment_method string
        +payment_amount decimal
        +payment_status PaymentStatus
        +payment_date date
        +transaction_reference string
        +notes text
        +recorded_by bigint
        +created_at timestamp
        +updated_at timestamp
    }
    class PaymentMethod {
        <<Model>>
        +id bigint
        +code string
        +label string
        +is_active bool
        +requires_reference bool
        +sort_order int
        +created_at timestamp
        +updated_at timestamp
    }
    class InvoiceStatus {
        <<enum>>
        +Draft
        +Issued
        +Partial
        +Paid
        +Cancelled
        +Overdue
    }
    class PaymentStatus {
        <<enum>>
        +Completed
        +Pending
        +Failed
        +Refunded
    }
    Order "1" --> "0..1" Invoice
    Invoice "1" --> "*" InvoiceItem
    Invoice "1" --> "*" Payment
    PaymentMethod "1" --> "*" Payment
    User "1" --> "*" Invoice : customer
    User "1" --> "*" Payment : recorded_by
    Invoice --> InvoiceStatus
    Payment --> PaymentStatus
```

---

## Figure 5 — Settings and Services

```mermaid
classDiagram
    direction TB
    class MetalPrice {
        <<Model>>
        +id bigint
        +gold_price_per_gram decimal
        +silver_price_per_gram decimal
        +price_date date
        +updated_by bigint
        +created_at timestamp
        +updated_at timestamp
    }
    class BillingSetting {
        <<Model>>
        +id bigint
        +tax_rate_percent decimal
        +updated_by bigint
        +created_at timestamp
        +updated_at timestamp
    }
    class CategoryDiscount {
        <<Model>>
        +id bigint
        +category_code string
        +discount_percent decimal
        +is_active bool
        +updated_by bigint
        +created_at timestamp
        +updated_at timestamp
    }
    class InvoiceService {
        <<Service>>
        +createDraftFromOrder()
        +updateDraft()
        +issue()
        +cancel()
    }
    class InvoiceCalculator {
        <<Service>>
        +calculate()
        +applyToInvoice()
    }
    class PaymentService {
        <<Service>>
        +recordPayment()
        +syncInvoiceStatus()
    }
    class Rbac {
        <<Support>>
        +userHasPermission() bool
        +permissionsForRole() array
    }
    User "1" --> "*" MetalPrice
    User "1" --> "*" BillingSetting
    User "1" --> "*" CategoryDiscount
    InvoiceService ..> InvoiceCalculator
    InvoiceCalculator ..> BillingSetting
    InvoiceCalculator ..> CategoryDiscount
    PaymentService ..> Payment
```

---

## Figure 6 — Notifications

```mermaid
classDiagram
    direction TB
    class DatabaseNotification {
        <<Table>>
        +id uuid
        +type string
        +notifiable_type string
        +notifiable_id bigint
        +data json
        +read_at timestamp
        +created_at timestamp
        +updated_at timestamp
    }
    class InvoiceIssuedNotification {
        <<Notification>>
        +invoice Invoice
        +toArray() array
    }
    class PaymentReceivedNotification {
        <<Notification>>
        +invoice Invoice
        +payment Payment
        +toArray() array
    }
    User "1" --> "*" DatabaseNotification
    InvoiceIssuedNotification ..> Invoice
    PaymentReceivedNotification ..> Payment
```

---

## Figure 7 — RBAC Permission Enum

```mermaid
classDiagram
    direction LR
    class Permission {
        <<enum>>
        +DashboardView
        +OrdersView
        +OrdersManage
        +CustomersView
        +CatalogView
        +CatalogManage
        +MetalPricesManage
        +UsersManage
        +ProductionView
        +ProductionAssign
        +ProductionManage
        +BillingView
        +BillingManage
        +BillingSettings
    }
    User ..> Permission
    Rbac ..> Permission
```

---

## Figure 8 — Domain Overview

```mermaid
classDiagram
    direction TB
    User --> Order
    User --> Invoice
    User --> Payment
    User --> ProductionLog
    User --> MetalPrice
    User --> BillingSetting
    User --> CategoryDiscount
    CatalogDesign --> CatalogImage
    CatalogDesign --> Order
    Order --> ProductionLog
    Order --> Invoice
    Invoice --> InvoiceItem
    Invoice --> Payment
    PaymentMethod --> Payment
```

---

## Entity attribute summary (all tables)

| Entity | Attributes |
|--------|------------|
| **users** | id, name, email, email_verified_at, password, remember_token, role, phone, address, city, profile_photo_path, created_at, updated_at |
| **catalog_designs** | id, name, code, description, category, gold_quality, weight_grams, selling_price, availability_status, created_at, updated_at |
| **catalog_images** | id, catalog_design_id, image_path, sort_order, is_primary, created_at, updated_at |
| **orders** | id, order_number, user_id, design_type, catalog_design_id, reference_image_path, item_type, item_name, size, weight_grams, specifications, gold_quality, gemstone_type, gemstone_details, quantity, special_instructions, expected_delivery_date, contact_phone, delivery_address, status, estimated_price, admin_notes, assigned_technician_id, assigned_at, created_at, updated_at |
| **production_logs** | id, order_id, user_id, from_status, to_status, note, created_at, updated_at |
| **metal_prices** | id, gold_price_per_gram, silver_price_per_gram, price_date, updated_by, created_at, updated_at |
| **invoices** | id, invoice_number, order_id, customer_id, subtotal, making_charge, discount, discount_percent, tax, tax_rate_percent, grand_total, invoice_status, issue_date, due_date, notes, created_by, created_at, updated_at |
| **invoice_items** | id, invoice_id, order_id, description, quantity, unit_price, line_total, created_at, updated_at |
| **payments** | id, invoice_id, payment_method, payment_amount, payment_status, payment_date, transaction_reference, notes, recorded_by, created_at, updated_at |
| **payment_methods** | id, code, label, is_active, requires_reference, sort_order, created_at, updated_at |
| **billing_settings** | id, tax_rate_percent, updated_by, created_at, updated_at |
| **category_discounts** | id, category_code, discount_percent, is_active, updated_by, created_at, updated_at |
| **notifications** | id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at |

---

*Open [`CLASS_DIAGRAM.html`](CLASS_DIAGRAM.html) for printable browser view.*
