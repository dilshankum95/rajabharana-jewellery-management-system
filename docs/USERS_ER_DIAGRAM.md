# ER Diagram — All Users + All Modules

**Rajabharana Jewellery Management System**

**Recommended visual:** [`docs/WHOLE_SYSTEM_ER_DIAGRAM.html`](WHOLE_SYSTEM_ER_DIAGRAM.html) — all users, all modules, R1–R12.

Also: [`USERS_ER_DIAGRAM.html`](USERS_ER_DIAGRAM.html) · [`DATABASE_ER_DIAGRAM.md`](DATABASE_ER_DIAGRAM.md)

---

## 1. All User Entities (Conceptual ER)

Five user types in the system:

| # | User Entity | `role` value | Panel | Description |
|---|-------------|--------------|-------|-------------|
| U1 | **CUSTOMER** | `customer` | `/dashboard`, `/orders` | Places and tracks jewellery orders |
| U2 | **SALES STAFF** | `staff` | `/admin/*` | Processes orders, views customers & catalog |
| U3 | **INVENTORY MANAGER** | `manager` | `/admin/catalog/*` | Manages catalogue items and images |
| U4 | **ADMINISTRATOR** | `admin` | `/admin/*` | Full system access, staff management |
| U5 | **TECHNICIAN** | `technician` | `/technician/*` | Workshop jobs (no customer phone/address) |

---

## 2. User Entity Attributes

| Attribute | CUSTOMER | STAFF | MANAGER | ADMIN | TECHNICIAN |
|-----------|:--------:|:-----:|:-------:|:-----:|:----------:|
| id (PK) | ✓ | ✓ | ✓ | ✓ | ✓ |
| name | ✓ | ✓ | ✓ | ✓ | ✓ |
| email (UK) | ✓ | ✓ | ✓ | ✓ | ✓ |
| password | ✓ | ✓ | ✓ | ✓ | ✓ |
| role | ✓ | ✓ | ✓ | ✓ | ✓ |
| phone | ✓ | — | — | — | — |
| address | ✓ | — | — | — | — |
| city | ✓ | — | — | — | — |
| profile_photo_path | ✓ | ✓ | ✓ | ✓ | ✓ |

**Physical DB:** All attributes stored in one `users` table; `role` distinguishes user type.

---

## 3. User-to-User Relationships (Account Management)

```mermaid
flowchart TB
    ADMIN["ADMINISTRATOR"]
    STAFF["SALES STAFF"]
    MGR["INVENTORY MANAGER"]
    TECH["TECHNICIAN"]
    CUST["CUSTOMER"]

    CUST -->|"registers (self)"| CUST
    ADMIN -->|"creates M:N"| STAFF
    ADMIN -->|"creates M:N"| MGR
    ADMIN -->|"creates M:N"| TECH
    ADMIN -->|"creates M:N"| ADMIN
    ADMIN -->|"supervises 1:M"| STAFF
    ADMIN -->|"supervises 1:M"| MGR
    ADMIN -->|"supervises 1:M"| TECH
    ADMIN -->|"assigns 1:M"| TECH
```

| Relationship | From | To | Cardinality | Meaning |
|--------------|------|-----|-------------|---------|
| **registers** | — | CUSTOMER | N | Self-registration via `/register` |
| **creates** | ADMINISTRATOR | STAFF, MANAGER, TECHNICIAN, ADMIN | 1 : M | Admin creates staff accounts |
| **supervises** | ADMINISTRATOR | STAFF, MANAGER, TECHNICIAN | 1 : M | Admin manages all staff |
| **assigns** | ADMINISTRATOR | TECHNICIAN → ORDERS | 1 : M | Admin assigns technician to production job |

---

## 4. All Users → System Operations (Conceptual ER)

```mermaid
flowchart LR
    subgraph Users
        C[CUSTOMER]
        S[SALES STAFF]
        M[INVENTORY MANAGER]
        A[ADMINISTRATOR]
        T[TECHNICIAN]
    end

    subgraph Entities
        O[ORDERS]
        CD[CATALOG_DESIGNS]
        CI[CATALOG_IMAGES]
        MP[METAL_PRICES]
        PL[PRODUCTION_LOGS]
        INV[INVOICES]
        PAY[PAYMENTS]
        REP[REPORTS]
    end

    C -->|places| O
    S -->|processes| O
    S -->|records| PAY
    M -->|creates| CD
    M -->|uploads| CI
    A -->|manages| O
    A -->|assigns| O
    A -->|updates| MP
    A -->|records| PL
    A -->|creates| INV
    A -->|generates| REP
    T -->|updates| O
    T -->|records| PL
```

### Operations by user

| User | Relationship | Target Entity | FK / Link | Status |
|------|--------------|---------------|-----------|--------|
| CUSTOMER | **places** | ORDERS | `orders.user_id` | ✅ |
| SALES STAFF | **processes** | ORDERS | status, price updates | ✅ |
| SALES STAFF | **records** | PAYMENTS | `payments.recorded_by` | 🔜 Sprint 9 |
| INVENTORY MANAGER | **creates** | CATALOG_DESIGNS | — | ✅ |
| INVENTORY MANAGER | **uploads** | CATALOG_IMAGES | — | ✅ |
| ADMINISTRATOR | **manages** | ORDERS | all order fields | ✅ |
| ADMINISTRATOR | **assigns** | ORDERS → TECHNICIAN | `assigned_technician_id` | ✅ |
| ADMINISTRATOR | **updates** | METAL_PRICES | `updated_by` | ✅ |
| ADMINISTRATOR | **records** | PRODUCTION_LOGS | `user_id` | ✅ |
| ADMINISTRATOR | **creates** | INVOICES | `created_by` | 🔜 Sprint 9 |
| ADMINISTRATOR | **generates** | REPORTS | `generated_by` | 🔜 Sprint 10 |
| TECHNICIAN | **updates** | ORDERS | production status | ✅ |
| TECHNICIAN | **records** | PRODUCTION_LOGS | `user_id` | ✅ |

---

## 5. Physical ER — users Table Relationships

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "R1 customer user_id"
    USERS ||--o{ ORDERS : "R2 technician assigned_technician_id"
    USERS ||--o{ PRODUCTION_LOGS : "R3 user_id"
    USERS ||--o{ METAL_PRICES : "R4 updated_by admin"
    USERS ||--o{ INVOICES : "R5 customer user_id"
    USERS ||--o{ INVOICES : "R6 created_by"
    USERS ||--o{ PAYMENTS : "R7 recorded_by staff"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        string phone
        text address
        string city
        string profile_photo_path
        timestamp created_at
        timestamp updated_at
    }
```

| Role in `users.role` | Relationships used |
|----------------------|-------------------|
| `customer` | R1 (places orders) |
| `staff` | R3 (logs), R6 (invoices), R7 (payments) |
| `manager` | No direct FK — operates via catalog tables |
| `admin` | R2, R3, R4, R6 + staff account creation |
| `technician` | R2 (assigned), R3 (production logs) |

---

## 6. Test Accounts (Seeder)

| User Entity | Email | Password |
|-------------|-------|----------|
| CUSTOMER | customer@rajabharana.com | password |
| SALES STAFF | staff@rajabharana.com | Password1 |
| INVENTORY MANAGER | manager@rajabharana.com | Password1 |
| ADMINISTRATOR | admin@rajabharana.com | password |
| TECHNICIAN | technician@rajabharana.com | Password1 |

---

## 7. All Modules + Relationships (R1–R12)

| Module | Status | Entities | Relationships |
|--------|--------|----------|---------------|
| **M1 Auth & RBAC** | ✅ | users | Parent of R1–R7 |
| **M2 Catalogue** | ✅ | catalog_designs, catalog_images | R8, R9 |
| **M3 Customer Orders** | ✅ | users, orders, catalog_designs | R1, R9 |
| **M4 Metal Prices** | ✅ | metal_prices, users | R4 |
| **M5 Workshop** | ✅ | orders, production_logs, users | R2, R3, R10 |
| **M6 Billing** | 🔜 Sprint 9 | invoices, payments | R5, R6, R7, R11, R12 |
| **M7 Reports** | 🔜 Sprint 10 | reads all tables | uses R1–R12 |

```mermaid
flowchart LR
    E1[(users)] -->|"R1 R2"| E2[(orders)]
    E1 -->|"R3"| E6[(production_logs)]
    E1 -->|"R4"| E5[(metal_prices)]
    E1 -->|"R5 R6"| E7[(invoices)]
    E1 -->|"R7"| E8[(payments)]
    E3[(catalog_designs)] -->|"R8"| E4[(catalog_images)]
    E3 -->|"R9"| E2
    E2 -->|"R10"| E6
    E2 -->|"R11"| E7
    E7 -->|"R12"| E8
```

---

## Related Diagrams

| File | Purpose |
|------|---------|
| [`WHOLE_SYSTEM_ER_DIAGRAM.html`](WHOLE_SYSTEM_ER_DIAGRAM.html) | **Complete** — users + modules + R1–R12 |
| [`USERS_ER_DIAGRAM.html`](USERS_ER_DIAGRAM.html) | Users + modules (4 figures) |
| [`CHEN_ER_DIAGRAM.html`](CHEN_ER_DIAGRAM.html) | Chen notation — full system |
| [`ER_DIAGRAM.html`](ER_DIAGRAM.html) | Database ER — all attributes |
| [`DATABASE_ER_DIAGRAM.md`](DATABASE_ER_DIAGRAM.md) | Full attribute & relationship tables |
