# Chen Notation ER Diagram — Full Database Mapping

**Rajabharana Jewellery Management System**

| Resource | File |
|----------|------|
| **Visual (open in browser)** | [`DATABASE_CHEN_ER_FULL.html`](DATABASE_CHEN_ER_FULL.html) |
| **Crow's Foot / Mermaid** | [`DATABASE_ER_FULL.html`](DATABASE_ER_FULL.html) |
| **User-role conceptual Chen** | [`CHEN_ER_DIAGRAM.html`](CHEN_ER_DIAGRAM.html) |

**Notation:** ▭ Rectangle = Entity · ○ Ellipse = Attribute · ◆ Diamond = Relationship

**Totals:** 15 entities · 20 relationships (19 physical FK + 1 logical reads)

---

## Chen Entities (Rectangles = MySQL Tables)

| # | Entity | Table | Status |
|---|--------|-------|--------|
| E1 | USERS | `users` | ✅ |
| E2 | PASSWORD_RESET_TOKENS | `password_reset_tokens` | ✅ |
| E3 | SESSIONS | `sessions` | ✅ |
| E4 | CATEGORIES | `categories` | 🔜 |
| E5 | CATALOG_DESIGNS | `catalog_designs` | ✅ |
| E6 | CATALOG_IMAGES | `catalog_images` | ✅ |
| E7 | ORDERS | `orders` | ✅ |
| E8 | METAL_PRICES | `metal_prices` | ✅ |
| E9 | PRODUCTION_LOGS | `production_logs` | ✅ |
| E10 | INVOICES | `invoices` | 🔜 Billing |
| E11 | INVOICE_ITEMS | `invoice_items` | 🔜 Billing |
| E12 | PAYMENT_METHODS | `payment_methods` | 🔜 Billing |
| E13 | PAYMENTS | `payments` | 🔜 Payment |
| E14 | NOTIFICATIONS | `notifications` | 🔜 |
| E15 | REPORT_EXPORTS | `report_exports` | 🔜 Reports |
| — | REPORT_ENGINE | (logical) | 🔜 Reports |

---

## Relationships (Diamonds)

| Diamond | Entity A | Card. | Entity B | FK Column |
|---------|----------|-------|----------|-----------|
| has_session | USERS | 1:N | SESSIONS | user_id |
| requests_reset | USERS | 1:0..1 | PASSWORD_RESET_TOKENS | email |
| places | USERS | 1:N | ORDERS | user_id |
| assigned_to | USERS | 1:N | ORDERS | assigned_technician_id |
| records | USERS | 1:N | PRODUCTION_LOGS | user_id |
| updates | USERS | 1:N | METAL_PRICES | updated_by |
| billed_to | USERS | 1:N | INVOICES | user_id |
| creates | USERS | 1:N | INVOICES | created_by |
| records_payment | USERS | 1:N | PAYMENTS | recorded_by |
| receives_alert | USERS | 1:N | NOTIFICATIONS | user_id |
| generates_export | USERS | 1:N | REPORT_EXPORTS | generated_by |
| categorizes | CATEGORIES | 1:N | CATALOG_DESIGNS | category_id |
| has_images | CATALOG_DESIGNS | 1:N | CATALOG_IMAGES | catalog_design_id |
| referenced_by | CATALOG_DESIGNS | 1:N | ORDERS | catalog_design_id |
| has_logs | ORDERS | 1:N | PRODUCTION_LOGS | order_id |
| generates | ORDERS | 1:1 | INVOICES | order_id |
| contains | INVOICES | 1:N | INVOICE_ITEMS | invoice_id |
| line_from | ORDERS | 1:N | INVOICE_ITEMS | order_id |
| receives | INVOICES | 1:N | PAYMENTS | invoice_id |
| uses_method | PAYMENT_METHODS | 1:N | PAYMENTS | payment_method_id |
| reads | REPORT_ENGINE | M:N | ALL TABLES | — (logical) |

---

## Attributes (Ellipses) — All Entities

### E1. USERS ✅
`id` PK · `name` · `email` UK · `email_verified_at` · `password` · `role` · `phone` · `address` · `city` · `profile_photo_path` · `remember_token` · `created_at` · `updated_at`

### E2. PASSWORD_RESET_TOKENS ✅
`email` PK · `token` · `created_at`

### E3. SESSIONS ✅
`id` PK · `user_id` FK · `ip_address` · `user_agent` · `payload` · `last_activity`

### E4. CATEGORIES 🔜
`id` PK · `name` UK · `slug` UK · `description` · `is_active` · `created_at` · `updated_at`

### E5. CATALOG_DESIGNS ✅
`id` PK · `name` · `code` UK · `description` · `category` · `category_id` FK · `gold_quality` · `weight_grams` · `selling_price` · `availability_status` · `created_at` · `updated_at`

### E6. CATALOG_IMAGES ✅
`id` PK · `catalog_design_id` FK · `image_path` · `sort_order` · `is_primary` · `created_at` · `updated_at`

### E7. ORDERS ✅
`id` PK · `order_number` UK · `user_id` FK · `design_type` · `catalog_design_id` FK · `reference_image_path` · `item_type` · `item_name` · `size` · `weight_grams` · `specifications` · `gold_quality` · `gemstone_type` · `gemstone_details` · `quantity` · `special_instructions` · `expected_delivery_date` · `contact_phone` · `delivery_address` · `status` · `estimated_price` · `admin_notes` · `assigned_technician_id` FK · `assigned_at` · `created_at` · `updated_at`

### E8. METAL_PRICES ✅
`id` PK · `gold_price_per_gram` · `silver_price_per_gram` · `price_date` · `updated_by` FK · `created_at` · `updated_at`

### E9. PRODUCTION_LOGS ✅
`id` PK · `order_id` FK · `user_id` FK · `from_status` · `to_status` · `note` · `created_at` · `updated_at`

### E10. INVOICES 🔜 — M8 Billing
`id` PK · `invoice_number` UK · `order_id` FK UK · `user_id` FK · `subtotal` · `making_charge` · `discount` · `tax` · `grand_total` · `amount_paid` · `balance_due` · `status` · `issued_at` · `due_date` · `notes` · `created_by` FK · `created_at` · `updated_at`

### E11. INVOICE_ITEMS 🔜
`id` PK · `invoice_id` FK · `order_id` FK · `description` · `quantity` · `unit_price` · `line_total` · `created_at` · `updated_at`

### E12. PAYMENT_METHODS 🔜
`id` PK · `code` UK · `label` · `is_active` · `sort_order` · `created_at`

### E13. PAYMENTS 🔜 — M9
`id` PK · `invoice_id` FK · `payment_method_id` FK · `amount` · `payment_status` · `payment_date` · `reference_number` · `notes` · `recorded_by` FK · `created_at` · `updated_at`

### E14. NOTIFICATIONS 🔜
`id` PK · `user_id` FK · `type` · `title` · `message` · `channel` · `read_at` · `data` · `created_at` · `updated_at`

### E15. REPORT_EXPORTS 🔜 — M12
`id` PK · `report_type` · `date_from` · `date_to` · `generated_by` FK · `file_path` · `format` · `parameters` · `created_at` · `updated_at`

---

## Billing Submodule (Chen ASCII)

```
     USERS                          ORDERS
       │                              │
       │ billed_to (1:N)              │ generates (1:1)
       └──────────────┐    ┌─────────┘
                      ▼    ▼
                   INVOICES ◄── contains (1:N) ── INVOICE_ITEMS
                      │                              ▲
                      │ receives (1:N)               │ line_from (1:N)
                      ▼                              │
                   PAYMENTS ◄── uses_method ── PAYMENT_METHODS
                      ▲
                      │ records_payment (1:N)
                    USERS
```

---

## Reports Submodule (Chen ASCII)

```
USERS ──generates_export (1:N)──► REPORT_EXPORTS
                                        │
REPORT_ENGINE ──reads (dashed M:N)──────┼──► orders, invoices, payments,
                                        │    catalog_designs, production_logs, users
```

---

## How to Use in Project Report

1. Open [`DATABASE_CHEN_ER_FULL.html`](DATABASE_CHEN_ER_FULL.html) in Chrome/Edge
2. Screenshot Figure 1 (relationship diagram) + Figures 2–3 (attributes)
3. Print → Save as PDF → insert in Word

---

*Chen notation · Database-mapped · Billing + Reports included*
