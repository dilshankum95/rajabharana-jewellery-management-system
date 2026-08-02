# Complete ER Diagram — All Modules (One Reference)

**Rajabharana Jewellery Management System**

**Visual (one diagram):** [`COMPLETE_ER_DIAGRAM.html`](COMPLETE_ER_DIAGRAM.html)  
**Full database map (Billing + Reports):** [`DATABASE_ER_FULL.html`](DATABASE_ER_FULL.html) · [`DATABASE_ER_FULL.md`](DATABASE_ER_FULL.md)  
**Legend:** ✅ Implemented · 🔜 Planned

**Total entities:** 15 tables · **Total relationships:** 19

---

## Module → Entity Map

| Module | Entities |
|--------|----------|
| M1 Auth & User Management | `users`, `password_reset_tokens`, `sessions` |
| M2 Customer | `users` (role=customer), `orders` |
| M3 Catalogue & Design | `catalog_designs`, `catalog_images` |
| M4 Order Management | `orders`, `users`, `catalog_designs` |
| M5 Workshop & Technician | `orders`, `production_logs`, `users` |
| M6 Inventory & Category | `catalog_designs`, `categories` 🔜 |
| M7 Metal Price | `metal_prices`, `users` |
| M8 Billing | `invoices`, `invoice_items`, `payment_methods` 🔜 |
| M9 Payment | `payments`, `payment_methods` 🔜 |
| M10 Notification | `notifications` 🔜 |
| M11 RBAC | `users.role` (no separate tables) |
| M12 Reports | Report Engine (logical) + `report_exports` 🔜 |

---

## PART A — ALL ENTITIES & ATTRIBUTES

### E1. users ✅ — M1 Auth · M2 Customer · M11 RBAC

| # | Attribute | Type | Key | Null | Module |
|---|-----------|------|-----|:----:|--------|
| 1 | id | BIGINT | PK | No | All |
| 2 | name | VARCHAR(255) | | No | All |
| 3 | email | VARCHAR(255) | UK | No | All |
| 4 | email_verified_at | TIMESTAMP | | Yes | Auth |
| 5 | password | VARCHAR(255) | | No | Auth |
| 6 | role | VARCHAR(255) | | No | RBAC |
| 7 | phone | VARCHAR(25) | | No | Customer |
| 8 | address | TEXT | | No | Customer |
| 9 | city | VARCHAR(100) | | No | Customer |
| 10 | profile_photo_path | VARCHAR(255) | | Yes | All |
| 11 | remember_token | VARCHAR(100) | | Yes | Auth |
| 12 | created_at | TIMESTAMP | | Yes | All |
| 13 | updated_at | TIMESTAMP | | Yes | All |

---

### E2. password_reset_tokens ✅ — M1 Auth

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | email | VARCHAR(255) | PK | No |
| 2 | token | VARCHAR(255) | | No |
| 3 | created_at | TIMESTAMP | | Yes |

---

### E3. sessions ✅ — M1 Auth

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | VARCHAR(255) | PK | No |
| 2 | user_id | BIGINT | FK → users | Yes |
| 3 | ip_address | VARCHAR(45) | | Yes |
| 4 | user_agent | TEXT | | Yes |
| 5 | payload | LONGTEXT | | No |
| 6 | last_activity | INT | | No |

---

### E4. categories 🔜 — M6 Inventory (proposed 3NF)

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | name | VARCHAR(100) | UK | No |
| 3 | slug | VARCHAR(100) | UK | No |
| 4 | description | TEXT | | Yes |
| 5 | is_active | BOOLEAN | | No |
| 6 | created_at | TIMESTAMP | | Yes |
| 7 | updated_at | TIMESTAMP | | Yes |

*Current implementation: `category` string on `catalog_designs` (not yet normalized).*

---

### E5. catalog_designs ✅ — M3 Catalogue · M6 Inventory

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | name | VARCHAR(255) | | No |
| 3 | code | VARCHAR(255) | UK | No |
| 4 | description | TEXT | | Yes |
| 5 | category | VARCHAR(255) | | No |
| 6 | category_id | BIGINT | FK → categories | Yes 🔜 |
| 7 | gold_quality | VARCHAR(255) | | No |
| 8 | weight_grams | DECIMAL(8,2) | | Yes |
| 9 | selling_price | DECIMAL(12,2) | | Yes |
| 10 | availability_status | VARCHAR(255) | | No |
| 11 | created_at | TIMESTAMP | | Yes |
| 12 | updated_at | TIMESTAMP | | Yes |

---

### E6. catalog_images ✅ — M3 Catalogue

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | catalog_design_id | BIGINT | FK → catalog_designs | No |
| 3 | image_path | VARCHAR(255) | | No |
| 4 | sort_order | SMALLINT | | No |
| 5 | is_primary | BOOLEAN | | No |
| 6 | created_at | TIMESTAMP | | Yes |
| 7 | updated_at | TIMESTAMP | | Yes |

---

### E7. orders ✅ — M2 Customer · M4 Orders · M5 Workshop

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | order_number | VARCHAR(255) | UK | No |
| 3 | user_id | BIGINT | FK → users (R1) | No |
| 4 | design_type | VARCHAR(255) | | No |
| 5 | catalog_design_id | BIGINT | FK → catalog_designs (R9) | Yes |
| 6 | reference_image_path | VARCHAR(255) | | Yes |
| 7 | item_type | VARCHAR(255) | | No |
| 8 | item_name | VARCHAR(255) | | Yes |
| 9 | size | VARCHAR(255) | | Yes |
| 10 | weight_grams | DECIMAL(8,2) | | Yes |
| 11 | specifications | TEXT | | Yes |
| 12 | gold_quality | VARCHAR(255) | | No |
| 13 | gemstone_type | VARCHAR(255) | | Yes |
| 14 | gemstone_details | TEXT | | Yes |
| 15 | quantity | SMALLINT | | No |
| 16 | special_instructions | TEXT | | Yes |
| 17 | expected_delivery_date | DATE | | No |
| 18 | contact_phone | VARCHAR(20) | | No |
| 19 | delivery_address | TEXT | | Yes |
| 20 | status | VARCHAR(255) | | No |
| 21 | estimated_price | DECIMAL(12,2) | | Yes |
| 22 | admin_notes | TEXT | | Yes |
| 23 | assigned_technician_id | BIGINT | FK → users (R2) | Yes |
| 24 | assigned_at | TIMESTAMP | | Yes |
| 25 | created_at | TIMESTAMP | | Yes |
| 26 | updated_at | TIMESTAMP | | Yes |

---

### E8. metal_prices ✅ — M7 Metal Price

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | gold_price_per_gram | DECIMAL(12,2) | | No |
| 3 | silver_price_per_gram | DECIMAL(12,2) | | No |
| 4 | price_date | DATE | | No |
| 5 | updated_by | BIGINT | FK → users (R4) | Yes |
| 6 | created_at | TIMESTAMP | | Yes |
| 7 | updated_at | TIMESTAMP | | Yes |

---

### E9. production_logs ✅ — M5 Workshop

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | order_id | BIGINT | FK → orders (R10) | No |
| 3 | user_id | BIGINT | FK → users (R3) | No |
| 4 | from_status | VARCHAR(255) | | Yes |
| 5 | to_status | VARCHAR(255) | | Yes |
| 6 | note | TEXT | | Yes |
| 7 | created_at | TIMESTAMP | | Yes |
| 8 | updated_at | TIMESTAMP | | Yes |

---

### E10. invoices 🔜 — M8 Billing

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | invoice_number | VARCHAR(255) | UK | No |
| 3 | order_id | BIGINT | FK → orders (R11) UK | No |
| 4 | user_id | BIGINT | FK → users (R5) | No |
| 5 | subtotal | DECIMAL(12,2) | | No |
| 6 | making_charge | DECIMAL(12,2) | | No |
| 7 | discount | DECIMAL(12,2) | | No |
| 8 | tax | DECIMAL(12,2) | | No |
| 9 | grand_total | DECIMAL(12,2) | | No |
| 10 | amount_paid | DECIMAL(12,2) | | No |
| 11 | balance_due | DECIMAL(12,2) | | No |
| 12 | status | VARCHAR(255) | | No |
| 13 | issued_at | TIMESTAMP | | No |
| 14 | due_date | DATE | | Yes |
| 15 | notes | TEXT | | Yes |
| 16 | created_by | BIGINT | FK → users (R6) | Yes |
| 17 | created_at | TIMESTAMP | | Yes |
| 18 | updated_at | TIMESTAMP | | Yes |

---

### E11. invoice_items 🔜 — M8 Billing

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | invoice_id | BIGINT | FK → invoices (R11a) | No |
| 3 | order_id | BIGINT | FK → orders (R11b) | No |
| 4 | description | VARCHAR(255) | | No |
| 5 | quantity | SMALLINT | | No |
| 6 | unit_price | DECIMAL(12,2) | | No |
| 7 | line_total | DECIMAL(12,2) | | No |
| 8 | created_at | TIMESTAMP | | Yes |
| 9 | updated_at | TIMESTAMP | | Yes |

---

### E12. payment_methods 🔜 — M8/M9 Lookup

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | code | VARCHAR(50) | UK | No |
| 3 | label | VARCHAR(100) | | No |
| 4 | is_active | BOOLEAN | | No |
| 5 | created_at | TIMESTAMP | | Yes |

*Seed: cash, card, bank_transfer*

---

### E13. payments 🔜 — M9 Payment

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | invoice_id | BIGINT | FK → invoices (R12) | No |
| 3 | payment_method_id | BIGINT | FK → payment_methods (R12a) | No |
| 4 | amount | DECIMAL(12,2) | | No |
| 5 | payment_status | VARCHAR(50) | | No |
| 6 | payment_date | DATE | | No |
| 7 | reference_number | VARCHAR(255) | | Yes |
| 8 | notes | TEXT | | Yes |
| 9 | recorded_by | BIGINT | FK → users (R7) | No |
| 10 | created_at | TIMESTAMP | | Yes |
| 11 | updated_at | TIMESTAMP | | Yes |

---

### E14. notifications 🔜 — M10 Notification

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | UUID/BIGINT | PK | No |
| 2 | user_id | BIGINT | FK → users (R13) | No |
| 3 | type | VARCHAR(255) | | No |
| 4 | title | VARCHAR(255) | | No |
| 5 | message | TEXT | | No |
| 6 | channel | VARCHAR(50) | | No |
| 7 | read_at | TIMESTAMP | | Yes |
| 8 | data | JSON | | Yes |
| 9 | created_at | TIMESTAMP | | Yes |
| 10 | updated_at | TIMESTAMP | | Yes |

---

### E15. report_exports 🔜 — M12 Reports

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | report_type | VARCHAR(100) | | No |
| 3 | date_from | DATE | | Yes |
| 4 | date_to | DATE | | Yes |
| 5 | generated_by | BIGINT | FK → users (R15) | No |
| 6 | file_path | VARCHAR(500) | | No |
| 7 | format | VARCHAR(10) | | No |
| 8 | parameters | JSON | | Yes |
| 9 | created_at | TIMESTAMP | | Yes |
| 10 | updated_at | TIMESTAMP | | Yes |

*Report Engine reads all tables via SQL — no separate engine table. PDF/CSV files stored in `storage/app/exports`.*

---

## PART B — ALL RELATIONSHIPS WITH ATTRIBUTES

| ID | Name | Parent | Child | FK Column | Card. | Null | On Delete | Module |
|----|------|--------|-------|-----------|-------|:----:|-----------|--------|
| R-AUTH-1 | has_session | users | sessions | user_id | 1:N | Yes | — | M1 |
| R-AUTH-2 | requests_reset | users | password_reset_tokens | email | 1:0..1 | No | — | M1 |
| R1 | places | users | orders | user_id | 1:N | No | CASCADE | M2/M4 |
| R2 | assigned_to | users | orders | assigned_technician_id | 1:N | Yes | SET NULL | M5 |
| R3 | records | users | production_logs | user_id | 1:N | No | CASCADE | M5 |
| R4 | updates | users | metal_prices | updated_by | 1:N | Yes | SET NULL | M7 |
| R5 | billed_customer | users | invoices | user_id | 1:N | No | CASCADE | M8 |
| R6 | invoice_creator | users | invoices | created_by | 1:N | Yes | SET NULL | M8 |
| R7 | payment_recorder | users | payments | recorded_by | 1:N | No | CASCADE | M9 |
| R8 | has_images | catalog_designs | catalog_images | catalog_design_id | 1:N | No | CASCADE | M3 |
| R9 | referenced_by | catalog_designs | orders | catalog_design_id | 1:N | Yes | SET NULL | M3/M4 |
| R10 | has_logs | orders | production_logs | order_id | 1:N | No | CASCADE | M5 |
| R11 | generates | orders | invoices | order_id | 1:1 | No | CASCADE | M8 |
| R11a | contains | invoices | invoice_items | invoice_id | 1:N | No | CASCADE | M8 |
| R11b | line_from | orders | invoice_items | order_id | 1:N | No | CASCADE | M8 |
| R12 | receives | invoices | payments | invoice_id | 1:N | No | CASCADE | M9 |
| R12a | uses_method | payment_methods | payments | payment_method_id | 1:N | No | RESTRICT | M9 |
| R13 | receives_alert | users | notifications | user_id | 1:N | No | CASCADE | M10 |
| R14 | categorizes | categories | catalog_designs | category_id | 1:N | Yes | SET NULL | M6 🔜 |
| R15 | generates_export | users | report_exports | generated_by | 1:N | No | CASCADE | M12 🔜 |

---

## PART C — ONE DIAGRAM (Mermaid)

Paste into [mermaid.live](https://mermaid.live) or open `COMPLETE_ER_DIAGRAM.html`.

```mermaid
erDiagram
    USERS ||--o{ SESSIONS : "R-AUTH-1 user_id"
    USERS ||--o| PASSWORD_RESET_TOKENS : "R-AUTH-2 email"
    USERS ||--o{ ORDERS : "R1 places user_id"
    USERS ||--o{ ORDERS : "R2 assigned_technician_id"
    USERS ||--o{ PRODUCTION_LOGS : "R3 records user_id"
    USERS ||--o{ METAL_PRICES : "R4 updates updated_by"
    USERS ||--o{ INVOICES : "R5 customer user_id"
    USERS ||--o{ INVOICES : "R6 creator created_by"
    USERS ||--o{ PAYMENTS : "R7 recorder recorded_by"
    USERS ||--o{ NOTIFICATIONS : "R13 user_id"
    USERS ||--o{ REPORT_EXPORTS : "R15 generated_by"

    CATEGORIES ||--o{ CATALOG_DESIGNS : "R14 category_id"
    CATALOG_DESIGNS ||--o{ CATALOG_IMAGES : "R8 catalog_design_id"
    CATALOG_DESIGNS ||--o{ ORDERS : "R9 catalog_design_id"

    ORDERS ||--o{ PRODUCTION_LOGS : "R10 order_id"
    ORDERS ||--o| INVOICES : "R11 generates order_id"
    ORDERS ||--o{ INVOICE_ITEMS : "R11b order_id"

    INVOICES ||--o{ INVOICE_ITEMS : "R11a invoice_id"
    INVOICES ||--o{ PAYMENTS : "R12 invoice_id"

    PAYMENT_METHODS ||--o{ PAYMENTS : "R12a payment_method_id"

    USERS {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role
        string phone
        text address
        string city
        string profile_photo_path
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    SESSIONS {
        string id PK
        bigint user_id FK
        string ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    PASSWORD_RESET_TOKENS {
        string email PK
        string token
        timestamp created_at
    }

    CATEGORIES {
        bigint id PK
        string name UK
        string slug UK
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    CATALOG_DESIGNS {
        bigint id PK
        string name
        string code UK
        text description
        string category
        bigint category_id FK
        string gold_quality
        decimal weight_grams
        decimal selling_price
        string availability_status
        timestamp created_at
        timestamp updated_at
    }

    CATALOG_IMAGES {
        bigint id PK
        bigint catalog_design_id FK
        string image_path
        int sort_order
        boolean is_primary
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        bigint id PK
        string order_number UK
        bigint user_id FK
        string design_type
        bigint catalog_design_id FK
        string reference_image_path
        string item_type
        string item_name
        string size
        decimal weight_grams
        text specifications
        string gold_quality
        string gemstone_type
        text gemstone_details
        int quantity
        text special_instructions
        date expected_delivery_date
        string contact_phone
        text delivery_address
        string status
        decimal estimated_price
        text admin_notes
        bigint assigned_technician_id FK
        timestamp assigned_at
        timestamp created_at
        timestamp updated_at
    }

    METAL_PRICES {
        bigint id PK
        decimal gold_price_per_gram
        decimal silver_price_per_gram
        date price_date
        bigint updated_by FK
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTION_LOGS {
        bigint id PK
        bigint order_id FK
        bigint user_id FK
        string from_status
        string to_status
        text note
        timestamp created_at
        timestamp updated_at
    }

    INVOICES {
        bigint id PK
        string invoice_number UK
        bigint order_id FK UK
        bigint user_id FK
        decimal subtotal
        decimal making_charge
        decimal discount
        decimal tax
        decimal grand_total
        decimal amount_paid
        decimal balance_due
        string status
        timestamp issued_at
        date due_date
        text notes
        bigint created_by FK
        timestamp created_at
        timestamp updated_at
    }

    INVOICE_ITEMS {
        bigint id PK
        bigint invoice_id FK
        bigint order_id FK
        string description
        int quantity
        decimal unit_price
        decimal line_total
        timestamp created_at
        timestamp updated_at
    }

    PAYMENT_METHODS {
        bigint id PK
        string code UK
        string label
        boolean is_active
        timestamp created_at
    }

    PAYMENTS {
        bigint id PK
        bigint invoice_id FK
        bigint payment_method_id FK
        decimal amount
        string payment_status
        date payment_date
        string reference_number
        text notes
        bigint recorded_by FK
        timestamp created_at
        timestamp updated_at
    }

    NOTIFICATIONS {
        uuid id PK
        bigint user_id FK
        string type
        string title
        text message
        string channel
        timestamp read_at
        json data
        timestamp created_at
        timestamp updated_at
    }

    REPORT_EXPORTS {
        bigint id PK
        string report_type
        date date_from
        date date_to
        bigint generated_by FK
        string file_path
        string format
        json parameters
        timestamp created_at
        timestamp updated_at
    }
```

---

## PART D — Relationship tree (text)

```
users
 ├── R-AUTH-1 user_id ──────────────► sessions
 ├── R-AUTH-2 email ────────────────► password_reset_tokens
 ├── R1 user_id ────────────────────► orders
 ├── R2 assigned_technician_id ─────► orders
 ├── R3 user_id ────────────────────► production_logs
 ├── R4 updated_by ─────────────────► metal_prices
 ├── R5 user_id ────────────────────► invoices
 ├── R6 created_by ─────────────────► invoices
 ├── R7 recorded_by ────────────────► payments
 ├── R13 user_id ───────────────────► notifications
 └── R15 generated_by ──────────────► report_exports

categories 🔜
 └── R14 category_id ───────────────► catalog_designs

catalog_designs
 ├── R8 catalog_design_id ──────────► catalog_images
 └── R9 catalog_design_id ──────────► orders

orders
 ├── R10 order_id ──────────────────► production_logs
 ├── R11 order_id (1:1) ────────────► invoices
 └── R11b order_id ─────────────────► invoice_items

invoices
 ├── R11a invoice_id ─────────────────► invoice_items
 └── R12 invoice_id ──────────────────► payments

payment_methods
 └── R12a payment_method_id ────────► payments
```

---

*M12 Reports: Report Engine reads all entities via R1–R14; `report_exports` stores export metadata (R15).*
