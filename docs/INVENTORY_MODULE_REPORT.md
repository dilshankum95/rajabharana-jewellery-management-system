# Chapter / Section — Inventory Management Module

## Rajabharana Jewellery Management System

---

**Module Name:** Inventory & Stock Management Module  
**System:** Rajabharana Jewellery Management System  
**Implementation Status:** Fully Implemented  
**Technology:** Laravel 12, PHP 8.2, MySQL, Blade, Tailwind CSS  

---

## 1. Introduction

The **Inventory Management Module** is a core component of the Rajabharana Jewellery Management System. It enables the business to track both **finished catalogue products** and **workshop raw materials** within a single integrated platform. In a jewellery business, inventory is not limited to display items on the shop floor; it also includes gold, gemstones, findings, and other consumables held in the workshop for manufacturing.

Prior to this module, catalogue availability was managed using a simple status flag (`available` / `out_of_stock`) without numeric stock tracking or linkage to raw materials. This approach could not support accurate stock control, material planning, or automatic deduction when orders were fulfilled. The Inventory Module addresses these limitations by introducing quantity-based stock management, a Bill of Materials (BOM) structure, stock movement auditing, and integration with the order acceptance workflow.

---

## 2. Objectives of the Module

The primary objectives of the Inventory Management Module are as follows:

1. **Track finished catalogue stock** — Maintain a numeric `stock_quantity` for each catalogue design and automatically update availability status when stock reaches zero.

2. **Manage workshop raw materials** — Provide CRUD operations for raw materials including type, unit of measure, current stock, reorder level, and unit cost.

3. **Define Bill of Materials (BOM)** — Link each catalogue item to the raw materials required to produce one unit, with a defined `quantity_required` per material.

4. **Automate stock deduction** — When an administrator accepts a catalogue order, deduct both finished-item stock and linked raw material stock automatically.

5. **Support stock restoration** — If an accepted order is subsequently rejected, restore previously deducted stock to maintain data integrity.

6. **Maintain an audit trail** — Record every stock change in a `stock_movements` table with before/delta/after quantities, reason, user, and optional order reference.

7. **Generate inventory reports** — Provide category-wise catalogue stock summaries and raw material status reports including low-stock alerts.

8. **Enforce role-based access** — Restrict inventory management functions to authorised roles (Administrator and Inventory Manager).

---

## 3. Scope

### 3.1 In Scope

| Area | Description |
|------|-------------|
| Catalogue stock | Numeric stock quantity per design; auto availability sync |
| Raw materials | Full lifecycle management (create, read, update, delete, adjust stock) |
| Bill of Materials | Many-to-many link between catalogue designs and raw materials |
| Stock movements | Polymorphic audit log for catalogue and material changes |
| Order integration | Stock validation at order placement; deduction on order accept |
| Inventory reporting | KPI dashboard and exportable category/material tables |
| Access control | RBAC permissions: `raw-materials.view`, `raw-materials.manage` |

### 3.2 Out of Scope

| Area | Reason |
|------|--------|
| Custom order material deduction | Custom orders do not reference a fixed BOM |
| Automatic workshop consumption on production start | Materials deduct only on order acceptance |
| Purchase order / supplier management | Not required in current project phase |
| Barcode / RFID scanning | Manual entry via web forms |
| Multi-branch / multi-warehouse | Single-location business model |

---

## 4. Problem Statement

Jewellery businesses face unique inventory challenges:

- **Dual inventory types** — Both finished products and raw materials must be tracked simultaneously.
- **Material-intensive production** — Accepting an order without verifying workshop material availability can cause production delays.
- **Manual record keeping** — Paper-based stock books are error-prone and do not integrate with online ordering.
- **No audit history** — Without movement logs, it is difficult to trace why stock levels changed or who made adjustments.

The Inventory Module solves these problems by digitising stock records, linking catalogue items to their material requirements, validating stock at order time, and maintaining a complete audit trail of all stock transactions.

---

## 5. Functional Requirements

### 5.1 Raw Material Management

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-INV-01 | System shall allow authorised users to add raw materials with name, type, unit, reorder level, and unit cost | High |
| FR-INV-02 | System shall auto-generate a unique material code (format: RM-YYYYMMDD-XXXX) | Medium |
| FR-INV-03 | System shall allow editing of material details except stock quantity (adjusted separately) | High |
| FR-INV-04 | System shall allow manual stock adjustment (+/−) with optional note | High |
| FR-INV-05 | System shall prevent deletion of materials with existing stock movement history | Medium |
| FR-INV-06 | System shall flag materials as low stock when quantity ≤ reorder level | High |
| FR-INV-07 | System shall support filtering materials by type and low-stock status | Medium |

### 5.2 Catalogue Stock Management

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-INV-08 | System shall store numeric stock quantity on each catalogue design | High |
| FR-INV-09 | System shall set availability to Out of Stock when stock quantity = 0 | High |
| FR-INV-10 | System shall restore availability to Available when stock is replenished from zero | High |
| FR-INV-11 | System shall allow linking multiple raw materials to a catalogue item with quantity per unit | High |

### 5.3 Order Integration

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-INV-12 | System shall validate catalogue stock before customer places an order | High |
| FR-INV-13 | System shall validate linked raw material stock before customer places an order | High |
| FR-INV-14 | System shall deduct catalogue stock when admin accepts a pending catalogue order | High |
| FR-INV-15 | System shall deduct linked raw materials (qty_required × order_qty) on order accept | High |
| FR-INV-16 | System shall restore stock if an accepted order is rejected | High |
| FR-INV-17 | System shall prevent duplicate deductions for the same order | High |

### 5.4 Reporting and Audit

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-INV-18 | System shall log every stock change with before, delta, after, reason, user, and order | High |
| FR-INV-19 | System shall generate inventory report with category summary and material listing | High |
| FR-INV-20 | System shall display recent stock movement history on material edit page | Medium |

---

## 6. Database Design

### 6.1 Entities

The Inventory Module introduces three new database tables and extends one existing table:

#### Table: `raw_materials`

Stores workshop raw material master data and current stock levels.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| name | VARCHAR(255) | Material name |
| code | VARCHAR(255) UNIQUE | Auto-generated identifier |
| material_type | VARCHAR(255) | gold, silver, gemstone, finding, alloy, other |
| unit | VARCHAR(255) | grams, pieces, carats |
| stock_quantity | DECIMAL(12,3) | Current on-hand quantity |
| reorder_level | DECIMAL(12,3) NULL | Low-stock threshold |
| unit_cost | DECIMAL(12,2) NULL | Cost per unit |
| notes | TEXT NULL | Additional notes |
| is_active | BOOLEAN | Active/inactive flag |

#### Table: `catalog_design_raw_material`

Associative (bridge) table implementing the Bill of Materials.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| catalog_design_id | FK | Reference to catalogue design |
| raw_material_id | FK | Reference to raw material |
| quantity_required | DECIMAL(12,3) | Material needed per 1 catalogue unit |

**Constraint:** Unique combination of `(catalog_design_id, raw_material_id)`.

#### Table: `stock_movements`

Polymorphic audit log for all stock changes.

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT (PK) | Primary key |
| stockable_type | VARCHAR | Model class (CatalogDesign or RawMaterial) |
| stockable_id | BIGINT | Polymorphic foreign key |
| quantity_before | DECIMAL(12,3) | Stock before change |
| quantity_delta | DECIMAL(12,3) | Change amount (+/−) |
| quantity_after | DECIMAL(12,3) | Stock after change |
| reason | VARCHAR | Movement reason code |
| order_id | FK NULL | Related order (if applicable) |
| user_id | FK NULL | User who performed action |
| note | VARCHAR NULL | Optional description |

#### Extended: `catalog_designs.stock_quantity`

| Column | Type | Description |
|--------|------|-------------|
| stock_quantity | INT UNSIGNED | Finished-item units in stock (default: 0) |

### 6.2 Relationships and Cardinality

| Relationship | Cardinality | Description |
|--------------|-------------|-------------|
| catalog_designs ↔ raw_materials | M : N | Via `catalog_design_raw_material` (BOM) |
| catalog_designs → stock_movements | 1 : M | Catalogue stock audit history |
| raw_materials → stock_movements | 1 : M | Material stock audit history |
| orders → stock_movements | 1 : M | Order-triggered movements (optional) |
| users → stock_movements | 1 : M | User-performed adjustments (optional) |

### 6.3 Normalisation

All inventory tables satisfy **Third Normal Form (3NF)**:

- **raw_materials** — All attributes depend solely on `id`; no partial or transitive dependencies.
- **catalog_design_raw_material** — Resolves the many-to-many relationship between designs and materials without redundancy.
- **stock_movements** — Each movement record is independent; polymorphic reference avoids duplicate tables for catalogue and material movements.

---

## 7. System Architecture and Design

### 7.1 Architectural Pattern

The module follows the **Model–View–Controller (MVC)** pattern used throughout the Laravel application, with business logic centralised in a dedicated **Service Layer**:

```
Presentation Layer    →  Blade views (admin/raw-materials, admin/catalog)
Controller Layer      →  RawMaterialController, CatalogDesignController, OrderController
Service Layer         →  InventoryService (stock logic, BOM sync, deduct/restore)
Model Layer           →  RawMaterial, StockMovement, CatalogDesign
Database Layer        →  MySQL (raw_materials, stock_movements, pivot table)
```

### 7.2 Key Design Decisions

| Decision | Rationale |
|----------|-----------|
| **Service class (`InventoryService`)** | Centralises all stock logic; avoids duplication across controllers |
| **Polymorphic `stock_movements`** | Single audit table for both catalogue and material changes |
| **Deferred deduction (on accept, not on order)** | Prevents stock lock-up for pending orders that may be rejected |
| **Database transactions with row locking** | Prevents race conditions during concurrent stock updates |
| **BOM pivot table** | Standard relational approach for M:N with attribute (`quantity_required`) |
| **Config-driven types/units** | Material types and units defined in `config/jewellery.php` for maintainability |

### 7.3 Stock Movement Reasons

| Reason Code | Trigger |
|-------------|---------|
| `manual_adjustment` | Manual correction by staff |
| `catalog_restock` | Catalogue item restocked |
| `order_accepted` | Stock deducted on order acceptance |
| `order_rejected` | Stock restored after rejection |
| `order_cancelled` | Stock restored on cancellation |
| `workshop_usage` | Material consumed (manual decrease) |
| `material_received` | Material received (manual increase) |

---

## 8. Module Components

### 8.1 Backend Components

| Component | Path | Responsibility |
|-----------|------|----------------|
| InventoryService | `app/Services/InventoryService.php` | Core stock operations |
| RawMaterialController | `app/Http/Controllers/Admin/RawMaterialController.php` | Raw material CRUD and stock adjust |
| CatalogDesignController | `app/Http/Controllers/Admin/CatalogDesignController.php` | Catalogue stock and BOM sync |
| OrderController | `app/Http/Controllers/Admin/OrderController.php` | Triggers deduct/restore on status change |
| RawMaterial Model | `app/Models/RawMaterial.php` | Material entity and relationships |
| StockMovement Model | `app/Models/StockMovement.php` | Audit log entity |
| StockMovementReason Enum | `app/Enums/StockMovementReason.php` | Typed movement reason codes |
| ReportEngine | `app/Services/ReportEngine.php` | Inventory report generation |

### 8.2 Frontend Components

| View | Path | Purpose |
|------|------|---------|
| Raw materials list | `resources/views/admin/raw-materials/index.blade.php` | List, search, filter, low-stock alert |
| Add material | `resources/views/admin/raw-materials/create.blade.php` | Create form |
| Edit material | `resources/views/admin/raw-materials/edit.blade.php` | Edit form + stock adjust + movement history |
| Catalog materials partial | `resources/views/admin/catalog/_materials.blade.php` | BOM linking on catalogue create/edit |

### 8.3 API Routes

| Method | Route | Function |
|--------|-------|----------|
| GET | `/admin/raw-materials` | List materials |
| POST | `/admin/raw-materials` | Create material |
| PATCH | `/admin/raw-materials/{id}` | Update material |
| DELETE | `/admin/raw-materials/{id}` | Delete material |
| POST | `/admin/raw-materials/{id}/adjust-stock` | Adjust stock |
| PATCH | `/admin/catalog/{id}` | Update catalogue stock and BOM |
| PATCH | `/admin/orders/{id}` | Accept/reject (triggers stock change) |

---

## 9. Process Workflows

### 9.1 Raw Material Lifecycle

1. Inventory Manager adds a new raw material (e.g. "22K Gold Wire — 50g stock").
2. System auto-generates code `RM-YYYYMMDD-XXXX`.
3. Manager can adjust stock when materials are received (+) or used in workshop (−).
4. Each adjustment creates a `stock_movements` record.
5. If stock falls to or below reorder level, material is flagged as **Low Stock**.

### 9.2 Bill of Materials Setup

1. Manager edits a catalogue item (e.g. "Gold Ring — Classic").
2. Sets `stock_quantity` (e.g. 5 finished units).
3. Links raw materials with required quantities per unit:
   - 22K Gold: 8.500 grams
   - Diamond Stone: 0.250 carats
   - Ring Finding: 1.000 piece
4. System saves links to `catalog_design_raw_material`.

### 9.3 Order Stock Flow

```
Customer places order
        ↓
System validates catalogue stock ≥ order quantity
        ↓
System validates each linked material stock ≥ (qty_required × order_qty)
        ↓
Order saved as PENDING (no stock deducted)
        ↓
Admin accepts order
        ↓
InventoryService deducts catalogue stock (−order_qty)
InventoryService deducts each material (−qty_required × order_qty)
        ↓
Stock movements logged; availability auto-updated
        ↓
[If later rejected] → Stock restored with reverse movements
```

---

## 10. Role-Based Access Control

| Role | Permissions | Inventory Functions |
|------|-------------|---------------------|
| **Administrator** | Full access (`*`) | All inventory operations |
| **Inventory Manager** | `catalog.view`, `catalog.manage`, `raw-materials.view`, `raw-materials.manage`, `reports.view`, `reports.export` | Manage catalogue stock, raw materials, BOM, and reports |
| **Sales Staff** | `catalog.view`, `orders.manage` | View catalogue; accept orders (triggers stock deduction) |
| **Technician** | `production.manage` | No direct inventory access |
| **Customer** | Customer portal | Places orders; stock validated automatically |

Access is enforced through Laravel middleware using the `permission` gate defined in `config/rbac.php`.

---

## 11. Inventory Reporting

The Inventory Report (`ReportEngine::inventoryReport()`) provides:

**Key Performance Indicators (KPIs):**
- Total catalogue designs and stock units
- Available vs out-of-stock counts
- Total stock value (selling price × quantity)
- Raw material count and low-stock count
- Category coverage

**Category Summary Table:**
- Items per category, stock units, available/out-of-stock counts
- Category stock value, average unit value, total weight, linked orders

**Catalogue Detail Table:**
- Per-item code, name, category, stock quantity, weight, price, stock value, status

**Raw Materials Table:**
- Per-material code, name, type, stock quantity, unit, reorder level, unit cost, stock value, low-stock flag

Reports are accessible to Administrator and Inventory Manager roles and support export functionality.

---

## 12. Business Rules

| # | Rule |
|---|------|
| BR-01 | Stock is validated when customer places order but deducted only when admin accepts |
| BR-02 | Custom orders do not trigger material or catalogue stock deduction |
| BR-03 | `quantity_required` in BOM represents material needed for **one** catalogue unit |
| BR-04 | Material deduction on accept = `quantity_required × order quantity` |
| BR-05 | When `stock_quantity = 0`, catalogue availability automatically becomes Out of Stock |
| BR-06 | When stock is replenished from zero, availability automatically becomes Available |
| BR-07 | Duplicate stock deductions for the same order are prevented |
| BR-08 | Raw materials with movement history cannot be deleted (deactivate instead) |
| BR-09 | Stock adjustments use database transactions with pessimistic row locking |
| BR-10 | Negative stock quantities are not permitted |

---

## 13. Benefits of the Module

1. **Accurate stock visibility** — Real-time numeric stock for both finished items and raw materials.
2. **Production planning** — BOM links ensure material availability is checked before orders are accepted.
3. **Audit compliance** — Complete movement history with user and order references.
4. **Reduced manual errors** — Automatic deduction and restoration eliminates manual stock book updates.
5. **Low-stock awareness** — Reorder level alerts help prevent production delays.
6. **Integrated reporting** — Category-wise inventory analysis supports business decision-making.
7. **Scalable design** — Service layer and polymorphic audit table support future extensions.

---

## 14. Limitations and Future Enhancements

### 14.1 Current Limitations

- No supplier or purchase order management
- No multi-location warehouse support
- Workshop production start does not auto-consume materials (only order accept does)
- Stock adjustment is manual; no integration with weighing scales or barcode scanners

### 14.2 Future Enhancements

- Purchase order module linked to material stock increases
- Automatic low-stock email notifications
- Material consumption tracking per production job in workshop module
- Historical stock trend charts and forecasting
- Batch/lot tracking for gold and gemstones (assay certificate linkage)

---

## 15. Conclusion

The Inventory Management Module transforms Rajabharana Jewellery's stock control from a manual, status-only approach to a comprehensive, quantity-based system with full material traceability. By integrating catalogue stock, raw material management, Bill of Materials, order-driven automatic deduction, and audit logging into a single module, the system ensures that inventory data remains accurate, traceable, and aligned with business operations.

The module demonstrates practical application of relational database design (3NF, bridge tables, polymorphic associations), service-oriented architecture, role-based access control, and transactional integrity — all essential principles of modern web application development for domain-specific business systems.

---

## References to Supporting Documentation

| Document | Location |
|----------|----------|
| ER Diagram (Materials) | `docs/MODULE_ER_DIAGRAMS/12-raw-materials-inventory.md` |
| Full Diagram Document | `docs/MATERIALS_INVENTORY_DIAGRAMS.md` |
| Sequence Diagrams (PDF) | `docs/MATERIALS_SEQUENCE_DIAGRAM.pdf` |
| Inventory & Category ER | `docs/MODULE_ER_DIAGRAMS/06-inventory-category.md` |
| Order Management Module | `docs/MODULE_ER_DIAGRAMS/04-order-management.md` |

---

*Rajabharana Jewellery Management System — Inventory Module Academic Report Section*
