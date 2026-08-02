# Chen Notation ER Diagram — Rajabharana Jewellery System

**Style:** Rectangle = Entity · Ellipse = Attribute · Diamond = Relationship (Operation)

**Visual diagram:** Open `docs/CHEN_ER_DIAGRAM.html` in browser → screenshot for Word.

---

## Entities (Rectangles)

| # | Entity | Description |
|---|--------|-------------|
| E1 | CUSTOMER | Online registered customer |
| E2 | SALES STAFF | Processes orders |
| E3 | INVENTORY MANAGER | Manages catalogue |
| E4 | ADMINISTRATOR | Full system control |
| E5 | TECHNICIAN | Workshop production |
| E6 | ORDERS | Customer jewellery orders |
| E7 | CATALOG_DESIGNS | Inventory items |
| E8 | CATALOG_IMAGES | Design photos |
| E9 | METAL_PRICES | Daily gold/silver rates |
| E10 | PRODUCTION_LOGS | Workshop audit trail |
| E11 | INVOICES 🔜 | Billing documents |
| E12 | PAYMENTS 🔜 | Payment records |
| E13 | REPORTS 🔜 | Generated reports |

---

## Relationships (Diamonds) — All Operations

| # | Relationship | Entity 1 | Card. | Entity 2 | Card. | Operation |
|---|--------------|----------|-------|----------|-------|-----------|
| R1 | **places** | CUSTOMER | M | ORDERS | N | Customer places order online |
| R2 | **processes** | SALES STAFF | M | ORDERS | N | Staff updates order status/price |
| R3 | **manages** | ADMINISTRATOR | 1 | ORDERS | N | Admin manages all orders |
| R4 | **assigns** | ADMINISTRATOR | 1 | TECHNICIAN | M | Admin assigns technician to job |
| R4b | **assigns** | ADMINISTRATOR | 1 | ORDERS | N | Technician linked to order |
| R5 | **updates** | TECHNICIAN | M | ORDERS | N | Update production status |
| R6 | **creates** | INVENTORY MANAGER | M | CATALOG_DESIGNS | N | Create/edit catalogue |
| R7 | **uploads** | INVENTORY MANAGER | M | CATALOG_IMAGES | N | Upload design images |
| R8 | **has** | CATALOG_DESIGNS | 1 | CATALOG_IMAGES | M | One design, many images |
| R9 | **selects** | CATALOG_DESIGNS | 1 | ORDERS | M | Catalogue order references design |
| R10 | **updates** | ADMINISTRATOR | M | METAL_PRICES | N | Set daily gold/silver rates |
| R11 | **records** | TECHNICIAN | M | PRODUCTION_LOGS | N | Log status changes |
| R12 | **records** | ADMINISTRATOR | M | PRODUCTION_LOGS | N | Log assignment notes |
| R13 | **generates** | ORDERS | 1 | INVOICES | 1 | Order creates invoice |
| R14 | **creates** | ADMINISTRATOR | M | INVOICES | N | Admin generates bill |
| R15 | **records** | SALES STAFF | M | PAYMENTS | N | Record customer payment |
| R16 | **receives** | INVOICES | 1 | PAYMENTS | M | Invoice receives payments |
| R17 | **generates** | ADMINISTRATOR | M | REPORTS | N | Admin generates reports |
| R18 | **summarizes** | REPORTS | M | ORDERS/INVOICES/LOGS | N | Reports read all data |

---

## Entity Attributes (Ellipses)

### CUSTOMER
user_id (PK), name, email, password, phone, address, city

### SALES STAFF
user_id (PK), name, email, password, role

### INVENTORY MANAGER
user_id (PK), name, email, password, role

### ADMINISTRATOR
user_id (PK), name, email, password, role

### TECHNICIAN
user_id (PK), name, email, password, role

### ORDERS
order_id (PK), order_number (UK), user_id (FK), catalog_design_id (FK), design_type, item_type, gold_quality, quantity, status, estimated_price, expected_delivery_date, assigned_technician_id (FK), assigned_at, reference_image_path, specifications, contact_phone, delivery_address, admin_notes, created_at

### CATALOG_DESIGNS
id (PK), name, code (UK), description, category, gold_quality, weight_grams, selling_price, availability_status

### CATALOG_IMAGES
id (PK), catalog_design_id (FK), image_path, sort_order, is_primary

### METAL_PRICES
id (PK), gold_price_per_gram, silver_price_per_gram, price_date, updated_by (FK)

### PRODUCTION_LOGS
id (PK), order_id (FK), user_id (FK), from_status, to_status, note, created_at

### INVOICES 🔜
invoice_id (PK), invoice_number (UK), order_id (FK), user_id (FK), subtotal, making_charge, discount, total_amount, amount_paid, balance_due, status, issued_at, created_by (FK)

### PAYMENTS 🔜
payment_id (PK), invoice_id (FK), amount, payment_method, payment_date, reference_number, recorded_by (FK)

### REPORTS 🔜
report_id (PK), report_type, date_from, date_to, generated_by (FK), generated_at

---

## User → Operations Summary

| User | Operations (Relationships) |
|------|---------------------------|
| Customer | places → ORDERS |
| Sales Staff | processes → ORDERS; records → PAYMENTS |
| Inventory Manager | creates → CATALOG_DESIGNS; uploads → CATALOG_IMAGES |
| Administrator | manages → ORDERS; assigns → TECHNICIAN; updates → METAL_PRICES; records → PRODUCTION_LOGS; creates → INVOICES; generates → REPORTS |
| Technician | updates → ORDERS; records → PRODUCTION_LOGS |

---

## Physical Database Mapping

In the implemented system, E1–E5 consolidate into one **`users`** table with `role` attribute. All other entities map directly to MySQL tables.
