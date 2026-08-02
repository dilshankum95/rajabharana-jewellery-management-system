# Whole System Class Diagram — Rajabharana Jewellery System

**Stack:** Laravel 12 · Eloquent ORM · Blade

| Resource | File |
|----------|------|
| **Visual (browser)** | [`CLASS_DIAGRAM.html`](CLASS_DIAGRAM.html) |
| **Architecture overview** | [`SYSTEM_ARCHITECTURE.md`](SYSTEM_ARCHITECTURE.md) |

**Legend:** ✅ Implemented · 🔜 Planned (Sprint 9–10)

---

## Layer overview

```
┌─────────────────────────────────────────────────────────────┐
│  Presentation — Controllers + Form Requests + Middleware    │
├─────────────────────────────────────────────────────────────┤
│  Domain — Eloquent Models + Enums                           │
├─────────────────────────────────────────────────────────────┤
│  Support — Rbac, ValidationRules, Rules                     │
├─────────────────────────────────────────────────────────────┤
│  Database — MySQL tables (8 implemented, 7 planned)         │
└─────────────────────────────────────────────────────────────┘
```

---

## Figure 1 — Domain model (Models + relationships) ✅

```mermaid
classDiagram
    direction TB

    class User {
        <<Model ✅>>
        +bigint id
        +string name
        +string email
        +UserRole role
        +string phone
        +string address
        +string city
        +orders() HasMany
        +assignedOrders() HasMany
        +hasPermission(string) bool
        +isCustomer() bool
        +isTechnician() bool
    }

    class CatalogDesign {
        <<Model ✅>>
        +bigint id
        +string name
        +string code
        +string category
        +AvailabilityStatus availability_status
        +decimal selling_price
        +images() HasMany
        +orders() HasMany
        +isAvailable() bool
    }

    class CatalogImage {
        <<Model ✅>>
        +bigint id
        +bigint catalog_design_id
        +string image_path
        +bool is_primary
        +catalogDesign() BelongsTo
    }

    class Order {
        <<Model ✅>>
        +bigint id
        +string order_number
        +bigint user_id
        +bigint catalog_design_id
        +DesignType design_type
        +OrderStatus status
        +bigint assigned_technician_id
        +user() BelongsTo
        +catalogDesign() BelongsTo
        +assignedTechnician() BelongsTo
        +productionLogs() HasMany
        +generateOrderNumber() string
    }

    class ProductionLog {
        <<Model ✅>>
        +bigint id
        +bigint order_id
        +bigint user_id
        +OrderStatus from_status
        +OrderStatus to_status
        +order() BelongsTo
        +user() BelongsTo
    }

    class MetalPrice {
        <<Model ✅>>
        +bigint id
        +decimal gold_price_per_gram
        +decimal silver_price_per_gram
        +date price_date
        +bigint updated_by
        +updatedBy() BelongsTo
        +current() MetalPrice
        +upsertCurrent() MetalPrice
    }

    User "1" --> "*" Order : places
    User "1" --> "*" Order : assigned_to
    User "1" --> "*" ProductionLog : records
    User "1" --> "*" MetalPrice : updates
    CatalogDesign "1" --> "*" CatalogImage : has
    CatalogDesign "1" --> "*" Order : referenced_by
    Order "1" --> "*" ProductionLog : has_logs
```

---

## Figure 2 — Enums ✅

```mermaid
classDiagram
    direction LR

    class UserRole {
        <<enum ✅>>
        Customer
        Admin
        Manager
        Staff
        Technician
        +label() string
        +isPanelRole() bool
    }

    class Permission {
        <<enum ✅>>
        DashboardView
        OrdersView
        OrdersManage
        CustomersView
        CatalogView
        CatalogManage
        MetalPricesManage
        UsersManage
        ProductionView
        ProductionAssign
        ProductionManage
        +label() string
    }

    class OrderStatus {
        <<enum ✅>>
        Pending
        Confirmed
        InProduction
        QualityCheck
        Ready
        Delivered
        Cancelled
        +label() string
    }

    class DesignType {
        <<enum ✅>>
        Catalog
        Custom
    }

    class AvailabilityStatus {
        <<enum ✅>>
        Available
        OutOfStock
    }

    User --> UserRole : role
    Order --> OrderStatus : status
    Order --> DesignType : design_type
    CatalogDesign --> AvailabilityStatus : availability_status
    User ..> Permission : checked via Rbac
```

---

## Figure 3 — Controllers by module ✅

```mermaid
classDiagram
    direction TB

    class Controller {
        <<abstract>>
    }

    class AuthenticatedSessionController {
        <<Auth ✅>>
        +create() View
        +store() RedirectResponse
        +destroy() RedirectResponse
    }

    class RegisteredUserController {
        <<Auth ✅>>
        +create() View
        +store() RedirectResponse
    }

    class CatalogController {
        <<Public ✅>>
        +index() View
        +show() View
        +purchase() View
    }

    class OrderController {
        <<Customer ✅>>
        +index() View
        +create() View
        +store() RedirectResponse
        +show() View
        +cancel() RedirectResponse
    }

    class DashboardController {
        <<Customer ✅>>
        +__invoke() View
    }

    class AdminDashboardController {
        <<Admin ✅>>
        +__invoke() View
    }

    class AdminOrderController {
        <<Admin ✅>>
        +index() View
        +show() View
        +update() RedirectResponse
    }

    class OrderAssignmentController {
        <<Admin ✅>>
        +update() RedirectResponse
    }

    class CatalogDesignController {
        <<Admin ✅>>
        +index() create() store()
        +edit() update() destroy()
    }

    class CustomerController {
        <<Admin ✅>>
        +index() View
        +show() View
    }

    class WorkshopController {
        <<Admin ✅>>
        +index() View
        +technicians() View
    }

    class MetalPriceController {
        <<Admin ✅>>
        +edit() View
        +update() RedirectResponse
    }

    class StaffUserController {
        <<Admin ✅>>
        +index() create() store()
        +edit() update() destroy()
    }

    class TechnicianDashboardController {
        <<Technician ✅>>
        +__invoke() View
    }

    class TechnicianJobController {
        <<Technician ✅>>
        +show() View
        +update() RedirectResponse
    }

    class ProfileController {
        <<Shared ✅>>
        +edit() update() destroy()
    }

    Controller <|-- AuthenticatedSessionController
    Controller <|-- RegisteredUserController
    Controller <|-- CatalogController
    Controller <|-- OrderController
    Controller <|-- DashboardController
    Controller <|-- AdminDashboardController
    Controller <|-- AdminOrderController
    Controller <|-- OrderAssignmentController
    Controller <|-- CatalogDesignController
    Controller <|-- CustomerController
    Controller <|-- WorkshopController
    Controller <|-- MetalPriceController
    Controller <|-- StaffUserController
    Controller <|-- TechnicianDashboardController
    Controller <|-- TechnicianJobController
    Controller <|-- ProfileController

    OrderController ..> Order : uses
    OrderController ..> CatalogDesign : uses
    AdminOrderController ..> Order : uses
    OrderAssignmentController ..> Order : uses
    OrderAssignmentController ..> ProductionLog : creates
    TechnicianJobController ..> Order : uses
    TechnicianJobController ..> ProductionLog : creates
    CatalogDesignController ..> CatalogDesign : uses
    CatalogDesignController ..> CatalogImage : uses
    MetalPriceController ..> MetalPrice : uses
    StaffUserController ..> User : uses
```

---

## Figure 4 — Security & RBAC ✅

```mermaid
classDiagram
    direction TB

    class EnsureCustomer {
        <<Middleware ✅>>
        +handle(Request, Closure) Response
    }

    class EnsureAdmin {
        <<Middleware ✅>>
        +handle(Request, Closure) Response
    }

    class EnsureTechnician {
        <<Middleware ✅>>
        +handle(Request, Closure) Response
    }

    class EnsurePermission {
        <<Middleware ✅>>
        +handle(Request, Closure, permissions) Response
    }

    class Rbac {
        <<Support ✅>>
        +permissionsForRole(UserRole) array
        +userHasPermission(User, string) bool
        +userHasAnyPermission(User, array) bool
    }

    class User {
        <<Model>>
        +hasPermission(string) bool
        +hasAnyPermission(array) bool
    }

    EnsurePermission ..> User : checks
    EnsurePermission ..> Rbac : uses
    User ..> Rbac : delegates
    Rbac ..> Permission : maps via config/rbac.php
    User --> UserRole : role
```

---

## Figure 5 — Planned domain classes 🔜

```mermaid
classDiagram
    direction TB

    class Invoice {
        <<Model 🔜>>
        +bigint id
        +string invoice_number
        +bigint order_id
        +bigint user_id
        +decimal grand_total
        +order() BelongsTo
        +user() BelongsTo
        +items() HasMany
        +payments() HasMany
    }

    class InvoiceItem {
        <<Model 🔜>>
        +bigint id
        +bigint invoice_id
        +bigint order_id
        +decimal line_total
        +invoice() BelongsTo
    }

    class Payment {
        <<Model 🔜>>
        +bigint id
        +bigint invoice_id
        +bigint payment_method_id
        +decimal amount
        +invoice() BelongsTo
        +paymentMethod() BelongsTo
    }

    class PaymentMethod {
        <<Model 🔜>>
        +bigint id
        +string code
        +string label
        +payments() HasMany
    }

    class Notification {
        <<Model 🔜>>
        +uuid id
        +bigint user_id
        +string type
        +json data
        +user() BelongsTo
    }

    class Category {
        <<Model 🔜>>
        +bigint id
        +string name
        +string slug
        +catalogDesigns() HasMany
    }

    class ReportExport {
        <<Model 🔜>>
        +bigint id
        +string report_type
        +bigint generated_by
        +generatedBy() BelongsTo
    }

    Order "1" --> "0..1" Invoice : generates
    User "1" --> "*" Invoice : billed_to
    Invoice "1" --> "*" InvoiceItem : contains
    Invoice "1" --> "*" Payment : receives
    PaymentMethod "1" --> "*" Payment : uses
    User "1" --> "*" Notification : receives
    Category "1" --> "*" CatalogDesign : categorizes
    User "1" --> "*" ReportExport : generates
```

---

## Module → classes map

| Module | Models ✅ | Controllers ✅ | Planned 🔜 |
|--------|-----------|----------------|------------|
| M1 Auth | User | Auth/*, ProfileController | — |
| M2 Customer | User, Order | Customer/* | — |
| M3 Catalogue | CatalogDesign, CatalogImage | CatalogController, Admin\CatalogDesignController | Category |
| M4 Orders | Order | Customer\OrderController, Admin\OrderController | — |
| M5 Workshop | Order, ProductionLog | WorkshopController, OrderAssignmentController, Technician/* | — |
| M6 Inventory | CatalogDesign | CatalogDesignController | Category |
| M7 Metal Price | MetalPrice | MetalPriceController | — |
| M8 Billing | — | — | Invoice, InvoiceItem |
| M9 Payment | — | — | Payment, PaymentMethod |
| M10 Notification | — | — | Notification |
| M11 RBAC | User + Rbac + Enums | Middleware | — |
| M12 Reports | — | — | ReportExport, ReportService |

---

## Form Request classes (validation layer) ✅

| Request | Used by |
|---------|---------|
| `LoginRequest`, `RegisterRequest` | Auth |
| `StoreOrderRequest` | Customer orders |
| `UpdateOrderRequest`, `AssignTechnicianRequest` | Admin orders |
| `StoreCatalogDesignRequest`, `UpdateCatalogDesignRequest` | Admin catalog |
| `UpdateMetalPriceRequest` | Metal prices |
| `StoreStaffUserRequest`, `UpdateStaffUserRequest` | Staff users |
| `UpdateProductionJobRequest` | Technician jobs |
| `ProfileUpdateRequest`, `DeleteAccountRequest` | Profile |

---

## Viva one-liner

> **The system uses a layered Laravel architecture: Controllers call Form Requests for validation, Eloquent Models represent 6 implemented domain entities (User, Order, CatalogDesign, CatalogImage, ProductionLog, MetalPrice), Enums define statuses and roles, and Rbac + Middleware enforce permissions. Billing, Payment, Notification, and Reports classes are planned but not yet coded.**

---

*Open [`CLASS_DIAGRAM.html`](CLASS_DIAGRAM.html) for printable diagrams.*
