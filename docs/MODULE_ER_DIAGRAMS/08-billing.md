# Module ER Diagram — M8 Billing

**Rajabharana Jewellery Management System**

| Field | Value |
|-------|-------|
| **Module** | M8 — Billing |
| **Status** | 🔜 Sprint 9 |
| **Physical tables** | `invoices`, `invoice_items`, `payment_methods` (lookup) |
| **Cross-module FKs** | `orders`, `users` |
| **Cross-module ref** | M9 Payment (`payments.invoice_id`) |
| **Relationship IDs** | R5, R6, R11 (+ internal R11a, R11b) |

---

## 1. Overview

The **Billing** module generates formal tax invoices for completed or confirmed jewellery orders. It captures line-level charges, applies making charges, discounts, and tax, and produces a printable bill for the customer. In the current system each order is **single-line** (one jewellery item per order); the `invoice_items` table is normalised for future multi-line orders but initially links each line to the parent `order_id`.

**Actors:**

| Actor | Action |
|-------|--------|
| **Sales Staff / Administrator** | Generate invoice from order, adjust charges, issue bill |
| **Customer** | View and print own invoices (read-only) |

**Business rules (user requirements):**

1. **One order → one invoice** — an order may have at most one invoice (`order_id` UNIQUE on `invoices`).
2. **Invoice number** — system-generated, unique, format `INV-YYYYMMDD-XXXX` (sequential per day).
3. **Customer consistency** — `invoices.customer_id` must equal `orders.user_id` for the linked order.
4. **Totals** — `grand_total = subtotal + making_charge + tax − discount` (all amounts ≥ 0; discount ≤ subtotal + making_charge + tax).
5. **Line items** — `line_total = quantity × unit_price`; sum of line totals should equal `subtotal` (or subtotal is derived from lines at issue time).
6. **Issue gate** — invoice generated when order reaches billable status (typically `confirmed`, `ready`, or `delivered`); not for `cancelled` orders.
7. **Status lifecycle** — `draft` → `issued` → `partial` / `paid` (updated by M9 Payment when payments recorded); `cancelled` voids the bill.
8. **Immutability** — once `issued`, header amounts and lines are locked; corrections require credit note / new invoice (future).
9. **Payment cross-ref** — invoice balance tracked via M9; see [09-payment.md](09-payment.md) for `Invoice 1-M Payment`.

---

## 2. Entities

| # | Entity | Type | Physical table | Description |
|---|--------|------|----------------|-------------|
| E1 | **INVOICE** | Strong | `invoices` | Billing document header |
| E2 | **INVOICE_ITEM** | Strong / weak* | `invoice_items` | Line item on an invoice |
| E3 | **PAYMENT_METHOD** | Lookup | `payment_methods` | Allowed payment channels (Cash, Card, Bank Transfer) |
| E4 | **ORDER** | Strong (external) | `orders` | Source order — 1:1 with invoice |
| E5 | **CUSTOMER** | Strong (external) | `users` (`role=customer`) | Billed party — maps to `customer_id` |
| E6 | **STAFF** | Strong (external) | `users` | Creator — `created_by` (optional) |

\*In Chen notation, INVOICE_ITEM can be modelled as a **weak entity** identified by INVOICE; physically it uses surrogate `invoice_item_id`.

**Lookup — PAYMENT_METHOD seed values:**

| code | label | active |
|------|-------|--------|
| `cash` | Cash | true |
| `card` | Card | true |
| `bank_transfer` | Bank Transfer | true |

---

## 3. Attributes

### 3.1 INVOICE (`invoices`)

| # | Attribute | Data type | Key | Null | Description |
|---|-----------|-----------|-----|------|-------------|
| 1 | invoice_id | BIGINT UNSIGNED | PK | No | Invoice primary key (physical: `id`) |
| 2 | invoice_number | VARCHAR(255) | UK | No | Human-readable number `INV-YYYYMMDD-XXXX` |
| 3 | order_id | BIGINT UNSIGNED | FK → orders.id, UK | No | Source order (R11) — one invoice per order |
| 4 | customer_id | BIGINT UNSIGNED | FK → users.id | No | Billed customer (R5); maps to order's `user_id` |
| 5 | subtotal | DECIMAL(12,2) | | No | Sum of line totals before charges |
| 6 | making_charge | DECIMAL(12,2) | | No | Labour / craftsmanship charge (default 0) |
| 7 | discount | DECIMAL(12,2) | | No | Discount amount (default 0) |
| 8 | tax | DECIMAL(12,2) | | No | Tax / VAT amount (default 0) |
| 9 | grand_total | DECIMAL(12,2) | | No | Final amount due |
| 10 | invoice_status | VARCHAR(255) | | No | `draft`, `issued`, `partial`, `paid`, `cancelled`, `overdue` |
| 11 | issue_date | DATE | | Yes | Date invoice was issued (NULL while draft) |
| 12 | due_date | DATE | | Yes | Payment due date |
| 13 | notes | TEXT | | Yes | Billing notes |
| 14 | created_by | BIGINT UNSIGNED | FK → users.id | Yes | Staff who created invoice (R6) |
| 15 | created_at | TIMESTAMP | | Yes | Created datetime |
| 16 | updated_at | TIMESTAMP | | Yes | Updated datetime |

**Derived (application layer, not stored):**

| Attribute | Formula |
|-----------|---------|
| amount_paid | SUM(`payments.payment_amount`) WHERE `payment_status = completed` |
| balance_due | `grand_total − amount_paid` |

### 3.2 INVOICE_ITEM (`invoice_items`)

| # | Attribute | Data type | Key | Null | Description |
|---|-----------|-----------|-----|------|-------------|
| 1 | invoice_item_id | BIGINT UNSIGNED | PK | No | Line item ID (physical: `id`) |
| 2 | invoice_id | BIGINT UNSIGNED | FK → invoices.id | No | Parent invoice (R11a) |
| 3 | order_item_id | BIGINT UNSIGNED | FK → orders.id | No | Order line reference* |
| 4 | description | VARCHAR(255) | | No | Item description snapshot |
| 5 | quantity | INT UNSIGNED | | No | Quantity (default 1) |
| 6 | unit_price | DECIMAL(12,2) | | No | Price per unit LKR |
| 7 | line_total | DECIMAL(12,2) | | No | `quantity × unit_price` |
| 8 | created_at | TIMESTAMP | | Yes | Created datetime |
| 9 | updated_at | TIMESTAMP | | Yes | Updated datetime |

\*Current system: orders are single-line — `order_item_id` references `orders.id` directly. Future: separate `order_items` table.

### 3.3 PAYMENT_METHOD (`payment_methods`)

| # | Attribute | Data type | Key | Null | Description |
|---|-----------|-----------|-----|------|-------------|
| 1 | id | BIGINT UNSIGNED | PK | No | Lookup ID |
| 2 | code | VARCHAR(50) | UK | No | `cash`, `card`, `bank_transfer` |
| 3 | label | VARCHAR(100) | | No | Display label |
| 4 | is_active | BOOLEAN | | No | Whether selectable |
| 5 | sort_order | SMALLINT | | No | UI ordering |

---

## 4. Relationships

| ID | Relationship | Entity A | Entity B | FK / bridge | Description |
|----|--------------|----------|----------|-------------|-------------|
| **R5** | **billed_to** | CUSTOMER (users) | INVOICE | `invoices.customer_id` | Customer receives invoices |
| **R6** | **creates** | STAFF (users) | INVOICE | `invoices.created_by` | Staff generates invoice |
| **R11** | **generates** | ORDER | INVOICE | `invoices.order_id` UNIQUE | One order produces one invoice |
| **R11a** | **contains** | INVOICE | INVOICE_ITEM | `invoice_items.invoice_id` | Invoice has line items |
| **R11b** | **snapshots** | ORDER | INVOICE_ITEM | `invoice_items.order_item_id` | Line copied from order |
| **R12** | **receives** *(M9)* | INVOICE | PAYMENT | `payments.invoice_id` | Invoice receives payments — see [09-payment.md](09-payment.md) |

---

## 5. Cardinality

| Relationship | From | Card. | To | Notes |
|--------------|------|-------|-----|-------|
| R5 billed_to | CUSTOMER | **1 : M** | INVOICE | Repeat customers have many invoices |
| R6 creates | STAFF | **1 : M** | INVOICE | One staff member creates many invoices |
| R11 generates | ORDER | **1 : 1** | INVOICE | Strict one-to-one |
| R11a contains | INVOICE | **1 : M** | INVOICE_ITEM | At least one line when issued |
| R11b snapshots | ORDER | **1 : M** | INVOICE_ITEM | Currently 1 line per order |
| R12 receives | INVOICE | **1 : M** | PAYMENT | Partial payments allowed |

---

## 6. Participation

| Entity | Relationship | Participation | Reason |
|--------|--------------|---------------|--------|
| INVOICE | R11 generates | **Total** | Every invoice must link to exactly one order |
| ORDER | R11 generates | **Partial** | Not every order is invoiced (cancelled, pending) |
| INVOICE | R11a contains | **Total** | Issued invoice must have ≥ 1 line item |
| INVOICE_ITEM | R11a contains | **Total** | Every line belongs to one invoice |
| INVOICE | R5 billed_to | **Total** | `customer_id` NOT NULL |
| CUSTOMER | R5 billed_to | **Partial** | Customer may exist without invoices yet |
| INVOICE | R6 creates | **Partial** | `created_by` nullable for system-generated |
| INVOICE | R12 receives | **Partial** | Unpaid issued invoices may have zero payments |

---

## 7. Constraints

| Type | Rule |
|------|------|
| **PK** | `invoices.invoice_id`, `invoice_items.invoice_item_id` |
| **UK** | `invoices.invoice_number`, `invoices.order_id` |
| **FK** | `customer_id` → `users.id` ON DELETE RESTRICT |
| **FK** | `order_id` → `orders.id` ON DELETE RESTRICT |
| **FK** | `created_by` → `users.id` ON DELETE SET NULL |
| **FK** | `invoice_items.invoice_id` → `invoices.id` ON DELETE CASCADE |
| **FK** | `invoice_items.order_item_id` → `orders.id` ON DELETE RESTRICT |
| **CHECK** | `grand_total = subtotal + making_charge + tax - discount` |
| **CHECK** | `line_total = quantity * unit_price` |
| **CHECK** | All monetary fields ≥ 0 |
| **CHECK** | `invoice_status` IN allowed enum values |
| **TRIGGER / app** | On issue: set `issue_date`, lock amounts |
| **TRIGGER / app** | `customer_id` must match `orders.user_id` for `order_id` |

---

## 8. Normalization (3NF)

| Check | Assessment |
|-------|------------|
| **1NF** | Atomic attributes; line items in separate table |
| **2NF** | No partial dependencies on composite keys |
| **3NF** | Customer name/phone not duplicated on invoice — only `customer_id` FK; item description snapshotted on `invoice_items` intentionally (order may change after issue) |
| **Payment methods** | Extracted to `payment_methods` lookup — avoids enum duplication across modules |
| **Derived totals** | `amount_paid` / `balance_due` computed from payments (M9) — not stored on invoice to avoid update anomalies |

**Snapshot rationale:** `invoice_items.description`, `unit_price` copied at issue time — 3NF exception for **intentional denormalisation** preserving legal billing record even if order is later edited.

---

## 9. Mermaid `erDiagram`

```mermaid
erDiagram
    USERS ||--o{ INVOICES : "R5 customer_id"
    USERS ||--o{ INVOICES : "R6 created_by"
    ORDERS ||--o| INVOICES : "R11 order_id"
    INVOICES ||--|{ INVOICE_ITEMS : "R11a invoice_id"
    ORDERS ||--o{ INVOICE_ITEMS : "R11b order_item_id"
    INVOICES ||--o{ PAYMENTS : "R12 see M9"
    PAYMENT_METHODS ||--o{ PAYMENTS : "method lookup M9"

    USERS {
        bigint id PK
        string name
        string email UK
        string role
    }

    ORDERS {
        bigint id PK
        string order_number UK
        bigint user_id FK
        decimal estimated_price
        string status
    }

    INVOICES {
        bigint invoice_id PK
        string invoice_number UK
        bigint order_id FK UK
        bigint customer_id FK
        decimal subtotal
        decimal making_charge
        decimal discount
        decimal tax
        decimal grand_total
        string invoice_status
        date issue_date
        date due_date
        bigint created_by FK
        timestamp created_at
        timestamp updated_at
    }

    INVOICE_ITEMS {
        bigint invoice_item_id PK
        bigint invoice_id FK
        bigint order_item_id FK
        string description
        int quantity
        decimal unit_price
        decimal line_total
    }

    PAYMENT_METHODS {
        bigint id PK
        string code UK
        string label
        boolean is_active
    }

    PAYMENTS {
        bigint payment_id PK
        bigint invoice_id FK
        string payment_method
        decimal payment_amount
        string payment_status
    }
```

---

## 10. Chen ASCII Notation

```
  ┌──────────┐                              ┌──────────┐
  │ CUSTOMER │                              │  ORDER   │
  │  (USER)  │                              └────┬─────┘
  └────┬─────┘                                   │
       │                                         │ generates (R11) 1:1
       │ billed_to (R5) 1:N                      │
       │                                         ▼
       │                              ┌──────────────────┐
       └─────────────────────────────►│     INVOICE      │
                                      └────────┬─────────┘
                                               │
                    ┌──────────────────────────┼──────────────────┐
                    │ contains (R11a) 1:N      │                  │
                    ▼                          │ receives (R12)   │
           ┌────────────────┐                  │ 1:N              ▼
           │ INVOICE_ITEM   │◄── snapshots ────┘           ┌──────────┐
           │  (weak entity) │     (R11b) from ORDER        │ PAYMENT  │  (M9)
           └────────────────┘                              └──────────┘

  ┌───────────────┐
  │ PAYMENT_METHOD│  (lookup — referenced by M9 Payment)
  └───────────────┘

INVOICE attributes (selected):
  (_invoice_id_)  invoice_id PK
  invoice_number UK
  order_id FK
  customer_id FK
  subtotal, making_charge, discount, tax, grand_total
  invoice_status, issue_date

INVOICE_ITEM attributes:
  (_invoice_item_id_) invoice_item_id PK
  invoice_id FK  ═══ total participation
  order_item_id FK
  quantity, unit_price, line_total
```

---

*Module M8 · Billing · 🔜 Sprint 9*
