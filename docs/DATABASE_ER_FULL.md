# Full Database ER Diagram — All Modules (Billing + Reports)

**Rajabharana Jewellery Management System**

| Resource | File |
|----------|------|
| **Visual (open in browser)** | [`DATABASE_ER_FULL.html`](DATABASE_ER_FULL.html) |
| **Chen notation (database)** | [`DATABASE_CHEN_ER_FULL.html`](DATABASE_CHEN_ER_FULL.html) · [`DATABASE_CHEN_ER_FULL.md`](DATABASE_CHEN_ER_FULL.md) |
| **Master reference** | [`COMPLETE_ER_DIAGRAM.md`](COMPLETE_ER_DIAGRAM.md) |
| **Per-module detail** | [`MODULE_ER_DIAGRAMS/`](MODULE_ER_DIAGRAMS/) |

**Legend:** ✅ Implemented in MySQL · 🔜 Planned migration

**Totals:** **15 tables** · **19 relationships** (R-AUTH-1 … R15)

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
| M12 Reports | **Report Engine** (logical) + `report_exports` 🔜 |

---

## PART A — ALL ENTITIES & ATTRIBUTES

### E1. users ✅

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | name | VARCHAR(255) | | No |
| 3 | email | VARCHAR(255) | UK | No |
| 4 | email_verified_at | TIMESTAMP | | Yes |
| 5 | password | VARCHAR(255) | | No |
| 6 | role | VARCHAR(255) | | No |
| 7 | phone | VARCHAR(25) | | No |
| 8 | address | TEXT | | No |
| 9 | city | VARCHAR(100) | | No |
| 10 | profile_photo_path | VARCHAR(255) | | Yes |
| 11 | remember_token | VARCHAR(100) | | Yes |
| 12 | created_at | TIMESTAMP | | Yes |
| 13 | updated_at | TIMESTAMP | | Yes |

---

### E2. password_reset_tokens ✅

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | email | VARCHAR(255) | PK | No |
| 2 | token | VARCHAR(255) | | No |
| 3 | created_at | TIMESTAMP | | Yes |

---

### E3. sessions ✅

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | VARCHAR(255) | PK | No |
| 2 | user_id | BIGINT | FK → users | Yes |
| 3 | ip_address | VARCHAR(45) | | Yes |
| 4 | user_agent | TEXT | | Yes |
| 5 | payload | LONGTEXT | | No |
| 6 | last_activity | INT | | No |

---

### E4. categories 🔜

| # | Attribute | Type | Key | Null |
|---|-----------|------|-----|:----:|
| 1 | id | BIGINT | PK | No |
| 2 | name | VARCHAR(100) | UK | No |
| 3 | slug | VARCHAR(100) | UK | No |
| 4 | description | TEXT | | Yes |
| 5 | is_active | BOOLEAN | | No |
| 6 | created_at | TIMESTAMP | | Yes |
| 7 | updated_at | TIMESTAMP | | Yes |

---

### E5. catalog_designs ✅

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

### E6. catalog_images ✅

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

### E7. orders ✅

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

### E8. metal_prices ✅

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

### E9. production_logs ✅

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

*Note: Billing docs use `customer_id`; physical column is `user_id` (same FK to users).*

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
| 5 | sort_order | SMALLINT | | No |
| 6 | created_at | TIMESTAMP | | Yes |

*Seed: `cash`, `card`, `bank_transfer`*

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
| 1 | id | UUID | PK | No |
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

Stores metadata for exported PDF/CSV reports (files in `storage/app/exports`).

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

**report_type values:** `order_summary`, `sales_revenue`, `customer`, `production`, `delivery`, `inventory`, `billing_collection`, `daily_summary`

---

## PART B — REPORT ENGINE (Logical — No Table)

The Report Engine runs read-only SQL across existing tables:

| Report | Primary data sources |
|--------|---------------------|
| Order Summary | `orders`, `users`, `catalog_designs` |
| Sales & Revenue | `orders`, `invoices`, `payments` |
| Customer Report | `users`, `orders` |
| Production Report | `orders`, `production_logs`, `users` |
| Delivery Report | `orders` |
| Inventory Report | `catalog_designs`, `categories` |
| Billing Collection | `invoices`, `payments`, `users` |
| Daily Summary | all tables |

---

## PART C — ALL RELATIONSHIPS (FK Mapping)

| ID | Parent | Child | FK Column | Card. | Module |
|----|--------|-------|-----------|-------|--------|
| R-AUTH-1 | users | sessions | user_id | 1:N | Auth |
| R-AUTH-2 | users | password_reset_tokens | email | 1:0..1 | Auth |
| R1 | users | orders | user_id | 1:N | Customer/Orders |
| R2 | users | orders | assigned_technician_id | 1:N | Workshop |
| R3 | users | production_logs | user_id | 1:N | Workshop |
| R4 | users | metal_prices | updated_by | 1:N | Metal Price |
| R5 | users | invoices | user_id | 1:N | Billing |
| R6 | users | invoices | created_by | 1:N | Billing |
| R7 | users | payments | recorded_by | 1:N | Payment |
| R8 | catalog_designs | catalog_images | catalog_design_id | 1:N | Catalogue |
| R9 | catalog_designs | orders | catalog_design_id | 1:N | Orders |
| R10 | orders | production_logs | order_id | 1:N | Workshop |
| R11 | orders | invoices | order_id | 1:1 | Billing |
| R11a | invoices | invoice_items | invoice_id | 1:N | Billing |
| R11b | orders | invoice_items | order_id | 1:N | Billing |
| R12 | invoices | payments | invoice_id | 1:N | Payment |
| R12a | payment_methods | payments | payment_method_id | 1:N | Payment |
| R13 | users | notifications | user_id | 1:N | Notification |
| R14 | categories | catalog_designs | category_id | 1:N | Inventory |
| R15 | users | report_exports | generated_by | 1:N | Reports |

---

## PART D — Billing Submodule ER (M8 + M9)

```
users ──R5──► invoices ◄──R11── orders (1:1)
users ──R6──► invoices
invoices ──R11a──► invoice_items ◄──R11b── orders
invoices ──R12──► payments ◄──R12a── payment_methods
users ──R7──► payments
```

**Billing totals:** `grand_total = subtotal + making_charge + tax − discount`

---

## PART E — How to View

1. Open [`DATABASE_ER_FULL.html`](DATABASE_ER_FULL.html) in Chrome/Edge.
2. Figure 1 — full Mermaid ER with all attributes.
3. Figures 2–3 — entity attribute boxes (implemented + billing + reports).
4. Figure 4 — relationship table.
5. **Print → Save as PDF** for Word/project report.

---

*15 entities · 19 relationships · Maps to MySQL · Updated for Billing (Sprint 9) + Reports (Sprint 10)*
