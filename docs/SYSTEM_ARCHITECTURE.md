# Rajabharana Jewellery Management System — Architecture Diagrams

Use these diagrams in your project report (Word). Export from [Mermaid Live Editor](https://mermaid.live) or screenshot from VS Code/Cursor preview.

**Whole system class diagram:** [`SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.html`](SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.html) · [`SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.md`](SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.md)

**Sequence diagrams:** [`SEQUENCE_DIAGRAM.html`](SEQUENCE_DIAGRAM.html) · [`SEQUENCE_DIAGRAM.md`](SEQUENCE_DIAGRAM.md)

**Use case diagrams (whole system + generalization):** [`USE_CASE_DIAGRAM.html`](USE_CASE_DIAGRAM.html) · [`USE_CASE_DIAGRAM.md`](USE_CASE_DIAGRAM.md)

**Use case diagrams (by user role):** [`USE_CASE_DIAGRAM_USERS.html`](USE_CASE_DIAGRAM_USERS.html) · [`USE_CASE_DIAGRAM_USERS.md`](USE_CASE_DIAGRAM_USERS.md)

**Use case diagram (Administrator):** [`USE_CASE_DIAGRAM_ADMIN.html`](USE_CASE_DIAGRAM_ADMIN.html) · [`USE_CASE_DIAGRAM_ADMIN.md`](USE_CASE_DIAGRAM_ADMIN.md)

**Activity diagram (Administrator):** [`ACTIVITY_DIAGRAM_ADMIN.html`](ACTIVITY_DIAGRAM_ADMIN.html) · [`ACTIVITY_DIAGRAM_ADMIN.md`](ACTIVITY_DIAGRAM_ADMIN.md)

**Wireframe diagrams:** [`WIREFRAME.html`](WIREFRAME.html) · [`WIREFRAME.md`](WIREFRAME.md)

**Technology stack (for report):** [`TECHNOLOGY_STACK.html`](TECHNOLOGY_STACK.html) · [`TECHNOLOGY_STACK.md`](TECHNOLOGY_STACK.md)

**Design patterns (for report):** [`DESIGN_PATTERNS.html`](DESIGN_PATTERNS.html) · [`DESIGN_PATTERNS.md`](DESIGN_PATTERNS.md)

**Implementation (Section 6.3):** [`IMPLEMENTATION.html`](IMPLEMENTATION.html) · [`IMPLEMENTATION.md`](IMPLEMENTATION.md)

**Registration module (detailed):** [`IMPLEMENTATION_REGISTER.html`](IMPLEMENTATION_REGISTER.html) · [`IMPLEMENTATION_REGISTER.md`](IMPLEMENTATION_REGISTER.md)

**Project setup (installation):** [`PROJECT_SETUP.html`](PROJECT_SETUP.html) · [`PROJECT_SETUP.md`](PROJECT_SETUP.md)

**Planned modules included:** Reports (Sprint 10)

---

## 1. High-Level System Architecture (3-Tier)

```mermaid
flowchart TB
    subgraph Presentation["Presentation Layer (Client)"]
        Browser["Web Browser\n(Chrome / Edge / Mobile)"]
        UI["Blade Views + Tailwind CSS + Alpine.js"]
    end

    subgraph Application["Application Layer (Server)"]
        Laravel["Laravel 12 Application"]
        Auth["Authentication\n(Laravel Breeze)"]
        RBAC["RBAC Middleware\n& Permissions"]
        Controllers["Controllers\nCustomer | Admin | Technician\nReport | Billing"]
        Validation["Form Requests\n& ValidationRules"]
        Models["Eloquent Models"]
        ReportEngine["Report Engine\n(aggregate & export)"]
    end

    subgraph Data["Data Layer"]
        MySQL["MySQL Database"]
        Storage["File Storage\n(catalog, reference images,\nexported PDF/Excel)"]
    end

    Browser --> UI
    UI --> Laravel
    Laravel --> Auth
    Laravel --> RBAC
    Laravel --> Controllers
    Controllers --> Validation
    Controllers --> Models
    Controllers --> ReportEngine
    ReportEngine --> Models
    Models --> MySQL
    Controllers --> Storage
    ReportEngine --> Storage
```

---

## 2. User & Panel Architecture

```mermaid
flowchart LR
    subgraph Users["Users"]
        Guest["Guest / Public"]
        Customer["Customer"]
        Staff["Sales Staff"]
        Manager["Inventory Manager"]
        Admin["Administrator"]
        Tech["Workshop Technician"]
    end

    subgraph Panels["System Panels"]
        Public["Public Site\n/  /catalog"]
        CP["Customer Portal\n/dashboard  /orders"]
        AP["Admin Panel\n/admin/*"]
        WP["Workshop Panel\n/technician/*"]
        RP["Reports Module\n/admin/reports/*"]
    end

    Guest --> Public
    Customer --> CP
    Staff --> AP
    Staff --> RP
    Manager --> AP
    Manager --> RP
    Admin --> AP
    Admin --> RP
    Tech --> WP
```

---

## 3. Application Module Architecture

```mermaid
flowchart TB
    subgraph PublicModule["Public Module"]
        P1["Home / Welcome"]
        P2["Catalog Browse"]
        P3["Design Details"]
    end

    subgraph CustomerModule["Customer Module"]
        C1["Registration & Login"]
        C2["Profile Management"]
        C3["Place Orders"]
        C4["Track Orders"]
        C5["View Metal Rates"]
    end

    subgraph AdminModule["Admin Module"]
        A1["Dashboard & KPIs"]
        A2["Order Management"]
        A3["Customer Management"]
        A4["Catalog / Inventory"]
        A5["Metal Prices"]
        A6["Staff Accounts"]
        A7["Workshop Queue"]
        A8["Technician Assignment"]
    end

    subgraph WorkshopModule["Workshop Module"]
        W1["My Jobs Dashboard"]
        W2["Job Specifications"]
        W3["Status Updates"]
        W4["Production Log"]
    end

    subgraph BillingModule["Billing Module — Sprint 9"]
        B1["Invoice Generation"]
        B2["Payment Recording"]
        B3["Bill View / Print"]
    end

    subgraph ReportsModule["Reports Module — Sprint 10"]
        R1["Order Reports"]
        R2["Sales & Revenue Reports"]
        R3["Customer Reports"]
        R4["Production / Workshop Reports"]
        R5["Delivery Reports"]
        R6["Inventory Reports"]
        R7["Billing & Collection Reports"]
        R8["Export PDF / Print"]
    end

    PublicModule --> CustomerModule
    CustomerModule --> AdminModule
    AdminModule --> WorkshopModule
    AdminModule --> BillingModule
    AdminModule --> ReportsModule
    BillingModule --> ReportsModule
    WorkshopModule --> ReportsModule
    CustomerModule -.-> BillingModule
```

---

## 4. MVC Architecture (Laravel)

```mermaid
flowchart LR
    Request["HTTP Request"] --> Routes["routes/web.php"]
    Routes --> Middleware["Middleware\nauth | verified\ncustomer | admin\ntechnician | permission"]
    Middleware --> Controller["Controller\nOrder | Catalog | Workshop\nBilling | Report"]
    Controller --> FormRequest["Form Request\n(Validation & Filters)"]
    FormRequest --> Controller
    Controller --> Model["Eloquent Model"]
    Model --> DB["MySQL"]
    Controller --> View["Blade View"]
    Controller --> Export["PDF / Print Export\n(Reports & Billing)"]
    View --> Response["HTML Response"]
    Export --> Response
```

---

## 5. Security & RBAC Architecture

```mermaid
flowchart TB
    Login["User Login"] --> AuthCheck{"Authenticated?"}
    AuthCheck -->|No| Deny1["Redirect to Login"]
    AuthCheck -->|Yes| RoleCheck{"Role / Permission?"}

    RoleCheck -->|Customer| CustomerMW["EnsureCustomer"]
    RoleCheck -->|Admin/Staff/Manager| AdminMW["EnsureAdmin\n+ EnsurePermission"]
    RoleCheck -->|Technician| TechMW["EnsureTechnician"]

    CustomerMW --> CustomerRoutes["Customer Routes\n/orders, /dashboard"]
    AdminMW --> AdminRoutes["Admin Routes\n/admin/*"]
    AdminMW --> ReportRoutes["Report Routes\n/admin/reports/*\n(reports.view)"]
    TechMW --> TechRoutes["Technician Routes\n/technician/*"]

    RoleCheck -->|Unauthorized| Deny2["403 Forbidden"]
```

---

## 6. Database Architecture (Entity Relationship — Full Attributes)

**Legend:** ✅ Implemented · 🔜 Planned (Sprint 9 Billing)

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "places (user_id)"
    USERS ||--o{ ORDERS : "assigned (assigned_technician_id)"
    USERS ||--o{ PRODUCTION_LOGS : records
    USERS ||--o{ METAL_PRICES : updates
    USERS ||--o{ INVOICES : "customer (user_id)"
    USERS ||--o{ INVOICES : "created_by"
    USERS ||--o{ PAYMENTS : recorded_by

    CATALOG_DESIGNS ||--o{ CATALOG_IMAGES : has
    CATALOG_DESIGNS ||--o{ ORDERS : references

    ORDERS ||--o{ PRODUCTION_LOGS : has
    ORDERS ||--o| INVOICES : generates

    INVOICES ||--o{ PAYMENTS : receives

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

    CATALOG_DESIGNS {
        bigint id PK
        string name
        string code UK
        text description
        string category
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
        bigint order_id FK
        bigint user_id FK
        decimal subtotal
        decimal making_charge
        decimal discount
        decimal total_amount
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

    PAYMENTS {
        bigint id PK
        bigint invoice_id FK
        decimal amount
        string payment_method
        date payment_date
        string reference_number
        text notes
        bigint recorded_by FK
        timestamp created_at
        timestamp updated_at
    }
```

**Note:** Reports module (Sprint 10) reads from all tables above — no separate report table; PDF exports stored in file storage.

### Relationship Summary

| Relationship | Type | Description |
|--------------|------|-------------|
| users → orders (user_id) | 1:N | Customer places many orders |
| users → orders (assigned_technician_id) | 1:N | Technician assigned to many orders |
| users → production_logs | 1:N | Staff/technician records log entries |
| users → metal_prices (updated_by) | 1:N | Admin updates metal rates |
| catalog_designs → catalog_images | 1:N | One design has many images |
| catalog_designs → orders | 1:N | Catalogue design linked to orders |
| orders → production_logs | 1:N | One order has many log entries |
| orders → invoices | 1:1 | One order generates one invoice |
| invoices → payments | 1:N | One invoice has many payments |
| users → invoices (user_id) | 1:N | Customer billed on many invoices |
| users → payments (recorded_by) | 1:N | Staff records many payments |

---

## 7. Order Processing Architecture

```mermaid
stateDiagram-v2
    [*] --> Pending: Customer places order
    Pending --> Confirmed: Admin confirms & sets price
    Confirmed --> InProduction: Technician starts work
    InProduction --> QualityCheck: Production complete
    QualityCheck --> Ready: QC passed
    QualityCheck --> InProduction: Rework needed
    Ready --> Delivered: Customer collects
    Pending --> Cancelled: Cancelled
    Confirmed --> Cancelled: Cancelled

    note right of Confirmed
        Admin assigns technician
        Invoice generated (Billing)
        Reports updated
    end note

    note right of InProduction
        Technician updates status
        Production log recorded
        Workshop reports updated
    end note

    note right of Delivered
        Sales & delivery reports
        Revenue reports generated
    end note
```

---

## 8. Deployment Architecture

```mermaid
flowchart TB
    subgraph ClientDevices["Client Devices"]
        PC["Office PC\n(reports & admin)"]
        Mobile["Customer Mobile"]
        Tablet["Workshop Tablet"]
    end

    subgraph Internet["Internet"]
        HTTPS["HTTPS"]
    end

    subgraph Server["Web Server (Production)"]
        Nginx["Nginx / Apache"]
        PHP["PHP 8.2+"]
        LaravelApp["Laravel Application\n+ Report Engine"]
    end

    subgraph DatabaseServer["Database"]
        MySQLDB["MySQL 8.x"]
    end

    subgraph FileStore["Storage"]
        PublicDisk["storage/app/public\n(images)"]
        ExportDisk["storage/app/exports\n(PDF reports)"]
    end

    PC --> HTTPS
    Mobile --> HTTPS
    Tablet --> HTTPS
    HTTPS --> Nginx
    Nginx --> PHP
    PHP --> LaravelApp
    LaravelApp --> MySQLDB
    LaravelApp --> PublicDisk
    LaravelApp --> ExportDisk
```

---

## 9. Technology Stack Diagram

```mermaid
flowchart TB
    subgraph Frontend["Frontend"]
        Blade["Blade Templates"]
        Tailwind["Tailwind CSS"]
        Alpine["Alpine.js"]
        Vite["Vite Build Tool"]
        Charts["Chart.js / Tables\n(Reports UI)"]
    end

    subgraph Backend["Backend"]
        Laravel["Laravel 12"]
        PHP["PHP 8.2+"]
        Breeze["Laravel Breeze"]
        ReportCtrl["ReportController\n& Query Services"]
        PDF["DomPDF\n(PDF export — planned)"]
    end

    subgraph Database["Database & Storage"]
        MySQL["MySQL"]
        Eloquent["Eloquent ORM"]
        Migrations["Migrations"]
    end

    Frontend --> Backend
    ReportCtrl --> MySQL
    PDF --> Backend
    Backend --> Database
```

---

## 10. Billing Module Architecture (Sprint 9 — Planned)

```mermaid
flowchart LR
    Order["Order\n(confirmed)"] --> Invoice["Invoice\nINV-YYYYMMDD-XXXX"]
    Invoice --> Payment1["Payment 1\n(partial)"]
    Invoice --> Payment2["Payment 2\n(balance)"]
    Payment1 --> Status{"Balance = 0?"}
    Payment2 --> Status
    Status -->|No| Partial["Status: Partial"]
    Status -->|Yes| Paid["Status: Paid"]
    Invoice --> CustomerView["Customer\nBill View"]
    Invoice --> PrintView["Print Invoice"]
    Invoice --> Reports["Billing Reports\n(Sprint 10)"]
```

---

## 11. Reports Module Architecture (Sprint 10 — Planned)

```mermaid
flowchart TB
    subgraph DataSources["Data Sources"]
        DS1["Orders"]
        DS2["Users / Customers"]
        DS3["Catalog Designs"]
        DS4["Production Logs"]
        DS5["Invoices & Payments"]
        DS6["Metal Prices"]
    end

    subgraph ReportEngine["Report Engine (Admin)"]
        RE["ReportController\n+ Filter Requests"]
        Q1["Order Report Queries"]
        Q2["Sales & Revenue Queries"]
        Q3["Customer Queries"]
        Q4["Workshop Queries"]
        Q5["Delivery Queries"]
        Q6["Inventory Queries"]
        Q7["Billing Collection Queries"]
    end

    subgraph Output["Report Output"]
        O1["On-screen Tables & KPIs"]
        O2["Date / Status Filters"]
        O3["Print View"]
        O4["PDF Export"]
    end

    DS1 --> RE
    DS2 --> RE
    DS3 --> RE
    DS4 --> RE
    DS5 --> RE
    DS6 --> RE

    RE --> Q1 & Q2 & Q3 & Q4 & Q5 & Q6 & Q7
    Q1 & Q2 & Q3 & Q4 & Q5 & Q6 & Q7 --> O1
    O1 --> O2
    O1 --> O3
    O1 --> O4
```

### Reports Module — Report Types

| Report | Description | Data source | Access |
|--------|-------------|-------------|--------|
| Order Summary | Orders by status, date range | `orders` | Admin, Sales Staff |
| Sales & Revenue | Total quoted/delivered value | `orders`, `invoices` | Admin |
| Customer Report | Customers and order counts | `users`, `orders` | Admin, Sales Staff |
| Production Report | Jobs per technician, stages | `orders`, `production_logs` | Admin |
| Delivery Report | Overdue, due soon, on-time | `orders` | Admin, Sales Staff |
| Inventory Report | Stock status, catalogue items | `catalog_designs` | Admin, Manager |
| Billing Collection | Paid, unpaid, partial balances | `invoices`, `payments` | Admin |
| Daily Summary | Combined KPIs for management | All modules | Admin |

### Reports Module — Routes (Planned)

| Route | Description |
|-------|-------------|
| `/admin/reports` | Reports dashboard / index |
| `/admin/reports/orders` | Order reports |
| `/admin/reports/sales` | Sales & revenue reports |
| `/admin/reports/customers` | Customer reports |
| `/admin/reports/production` | Workshop / technician reports |
| `/admin/reports/delivery` | Delivery performance reports |
| `/admin/reports/inventory` | Inventory reports |
| `/admin/reports/billing` | Billing & collection reports |

### Reports Module — RBAC (Planned)

| Permission | Roles |
|------------|-------|
| `reports.view` | Administrator, Sales Staff, Inventory Manager (inventory only) |
| `reports.export` | Administrator |

---

## 12. Complete System Overview (All Modules)

```mermaid
flowchart TB
    subgraph Implemented["Implemented Modules"]
        M1["Authentication & RBAC"]
        M2["Customer Portal"]
        M3["Order Management"]
        M4["Admin Dashboard"]
        M5["Catalog / Inventory"]
        M6["Metal Prices"]
        M7["Staff Accounts"]
        M8["Workshop & Technician Panel"]
    end

    subgraph Planned["Planned Modules"]
        M9["Billing — Sprint 9"]
        M10["Reports — Sprint 10"]
    end

    M2 --> M3 --> M4
    M4 --> M5 & M6 & M7 & M8
    M4 --> M9
    M9 --> M10
    M8 --> M10
    M5 --> M10
    M3 --> M10
```

---

## Diagram Usage in Report

| Diagram | Report section |
|---------|----------------|
| 1. High-Level 3-Tier | System Architecture |
| 2. User & Panel | System Overview |
| 3. Application Modules | System Design |
| 4. MVC | Implementation / Design |
| 5. Security & RBAC | Security Design |
| 6. Database ER | Database Design |
| 7. Order Processing | Workflow / Process Design |
| 8. Deployment | Deployment Architecture |
| 9. Technology Stack | Technology Stack |
| 10. Billing | Sprint 9 — Billing Module |
| 11. Reports | Sprint 10 — Reports Module |
| 12. Complete System Overview | Full System Architecture Summary |
