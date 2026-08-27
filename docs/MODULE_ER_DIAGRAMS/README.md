# Module ER Diagrams — Rajabharana Jewellery System

Separate Entity-Relationship documentation for **12 modules**.

| # | Module | File | Status |
|---|--------|------|--------|
| 1 | Authentication & User Management | [01-authentication.md](01-authentication.md) | ✅ Implemented |
| 2 | Customer Module | [02-customer.md](02-customer.md) | ✅ Implemented |
| 3 | Catalogue & Jewellery Design | [03-catalogue-design.md](03-catalogue-design.md) | ✅ Implemented |
| 4 | Order Management | [04-order-management.md](04-order-management.md) | ✅ Implemented |
| 5 | Workshop & Technician | [05-workshop-technician.md](05-workshop-technician.md) | ✅ Implemented |
| 6 | Inventory & Category | [06-inventory-category.md](06-inventory-category.md) | ✅ Partial / categories normalized in doc |
| 7 | Metal Price | [07-metal-price.md](07-metal-price.md) | ✅ Implemented |
| 8 | Billing | [08-billing.md](08-billing.md) | 🔜 Sprint 9 |
| 9 | Payment | [09-payment.md](09-payment.md) | 🔜 Sprint 9 |
| 10 | Notification | [10-notification.md](10-notification.md) | 🔜 Planned |
| 11 | Role-Based Access Control (RBAC) | [11-rbac.md](11-rbac.md) | ✅ Implemented (config + role column) |
| 12 | Raw Materials & Stock | [12-raw-materials-inventory.md](12-raw-materials-inventory.md) | ✅ Implemented |

**Materials full diagram doc (ER + Use Case + Sequence):** [../MATERIALS_INVENTORY_DIAGRAMS.md](../MATERIALS_INVENTORY_DIAGRAMS.md)

**Visual browser index:** [index.html](index.html)

**Whole-system ER (relationship attributes):** [../ER_DIAGRAM_RELATIONSHIPS.html](../ER_DIAGRAM_RELATIONSHIPS.html)

---

## ER Notation Legend

### Chen Notation (Conceptual)

| Symbol | Meaning |
|--------|---------|
| Rectangle | Entity |
| Double Rectangle | Weak Entity |
| Diamond | Relationship |
| Double Diamond | Identifying Relationship |
| Oval | Attribute |
| Underlined Oval | Primary Key |
| Double Oval | Multivalued Attribute |
| Dashed Oval | Derived Attribute |
| Double Line | Total Participation |
| Single Line | Partial Participation |

### Crow's Foot (Logical / Physical)

| Notation | Meaning |
|----------|---------|
| `(1) ——< (M)` | One-to-Many |
| `(1) —— (1)` | One-to-One |
| `(M) ——< (M)` | Many-to-Many (via bridge table) |
| Mandatory | FK NOT NULL |
| Optional | FK NULL allowed |

### Implementation Note

Conceptual diagrams may show **customers**, **technicians**, and **roles** as separate entities. The physical database consolidates staff and customers into **`users`** with a **`role`** column. Module docs note both views.
