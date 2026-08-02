# System Architecture — Whole System Class Diagram

**Rajabharana Jewellery Management System**  
Aligned with [`SYSTEM_ARCHITECTURE.md`](SYSTEM_ARCHITECTURE.md)

| Resource | File |
|----------|------|
| **Visual (browser / PDF)** | [`SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.html`](SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.html) |
| **Domain-only class diagram** | [`CLASS_DIAGRAM.md`](CLASS_DIAGRAM.md) |

**Legend:** ✅ Implemented · 🔜 Planned (Sprint 9–10)

---

## 1. Three-tier architecture (class view)

Maps **Presentation → Application → Data** from System Architecture §1.

```mermaid
classDiagram
    direction TB

    namespace Presentation {
        class Browser {
            <<client>>
        }
        class BladeViews {
            <<view>>
            +welcome.blade.php
            +catalog/*
            +customer/*
            +admin/*
            +technician/*
        }
        class LayoutComponents {
            <<component>>
            AppLayout
            AdminLayout
            TechnicianLayout
            GuestLayout
            PublicLayout
        }
    }

    namespace Application {
        class LaravelApp {
            <<framework>>
            Laravel 12
        }
        class Routes {
            <<routing>>
            web.php
            auth.php
        }
        class Middleware {
            <<security>>
            EnsureCustomer
            EnsureAdmin
            EnsureTechnician
            EnsurePermission
        }
        class Controllers {
            <<controller>>
            Customer/*
            Admin/*
            Technician/*
            Auth/*
            CatalogController
        }
        class FormRequests {
            <<validation>>
            StoreOrderRequest
            UpdateOrderRequest
            AssignTechnicianRequest
        }
        class Rbac {
            <<support>>
            +userHasPermission()
        }
        class ReportEngine {
            <<planned>>
            +generateReport()
            +exportPdf()
        }
    }

    namespace Domain {
        class EloquentModels {
            <<model>>
            User Order CatalogDesign
            CatalogImage ProductionLog MetalPrice
        }
        class Enums {
            <<enum>>
            UserRole Permission OrderStatus
            DesignType AvailabilityStatus
        }
    }

    namespace Data {
        class MySQL {
            <<database>>
            8 tables implemented
        }
        class FileStorage {
            <<storage>>
            catalog images
            reference images
            PDF exports
        }
    }

    Browser --> BladeViews
    BladeViews --> LayoutComponents
    BladeViews ..> Controllers : HTTP
    LaravelApp --> Routes
    Routes --> Middleware
    Middleware --> Controllers
    Controllers --> FormRequests
    Controllers --> Rbac
    Controllers --> EloquentModels
    Controllers --> ReportEngine
    ReportEngine --> EloquentModels
    EloquentModels --> MySQL
    Controllers --> FileStorage
    EloquentModels --> Enums
```

---

## 2. Module architecture (all modules → classes)

Maps System Architecture §3 and §12.

```mermaid
classDiagram
    direction TB

    namespace M1_Auth_RBAC {
        class AuthControllers {
            AuthenticatedSessionController
            RegisteredUserController
            PasswordResetLinkController
        }
        class StaffUserController
        class Rbac
        class User
        class UserRole
        class Permission
    }

    namespace M2_Customer {
        class CustomerDashboardController
        class CustomerOrderController
        class ProfileController
    }

    namespace M3_Catalog {
        class CatalogController
        class CatalogDesignController
        class CatalogDesign
        class CatalogImage
    }

    namespace M4_Orders {
        class CustomerOrderController
        class AdminOrderController
        class Order
        class OrderStatus
        class DesignType
    }

    namespace M5_Workshop {
        class WorkshopController
        class OrderAssignmentController
        class TechnicianJobController
        class ProductionLog
    }

    namespace M6_Inventory {
        class CatalogDesignController
        class AvailabilityStatus
        class Category {
            <<planned>>
        }
    }

    namespace M7_MetalPrice {
        class MetalPriceController
        class MetalPrice
    }

    namespace M8_Billing {
        class InvoiceController {
            <<planned>>
        }
        class Invoice {
            <<planned>>
        }
        class InvoiceItem {
            <<planned>>
        }
    }

    namespace M9_Payment {
        class PaymentController {
            <<planned>>
        }
        class Payment {
            <<planned>>
        }
        class PaymentMethod {
            <<planned>>
        }
    }

    namespace M10_Notification {
        class NotificationModel {
            <<planned>>
        }
    }

    namespace M11_Reports {
        class ReportController {
            <<planned>>
        }
        class ReportExport {
            <<planned>>
        }
        class ReportEngine {
            <<planned>>
        }
    }

    M2_Customer --> M4_Orders
    M4_Orders --> M5_Workshop
    M3_Catalog --> M4_Orders
    M4_Orders --> M8_Billing
    M8_Billing --> M9_Payment
    M8_Billing --> M11_Reports
    M5_Workshop --> M11_Reports
    M1_Auth_RBAC --> M2_Customer
    M1_Auth_RBAC --> M4_Orders
```

---

## 3. MVC request flow (class interactions)

Maps System Architecture §4.

```mermaid
classDiagram
    direction LR

    class HTTPRequest {
        <<external>>
    }
    class WebRoutes {
        +web.php
        +auth.php
    }
    class MiddlewareStack {
        auth verified
        customer admin technician
        permission
    }
    class Controller {
        <<abstract>>
        +index() show() store() update()
    }
    class FormRequest {
        <<abstract>>
        +authorize() bool
        +rules() array
    }
    class EloquentModel {
        <<abstract>>
        +save() find() where()
    }
    class BladeView {
        <<view>>
        +render() HTML
    }
    class MySQLDatabase {
        <<database>>
    }

    HTTPRequest --> WebRoutes
    WebRoutes --> MiddlewareStack
    MiddlewareStack --> Controller
    Controller --> FormRequest : validates
    FormRequest --> Controller
    Controller --> EloquentModel
    EloquentModel --> MySQLDatabase
    Controller --> BladeView
    BladeView --> HTTPRequest : HTML response
```

---

## 4. Security & RBAC class diagram

Maps System Architecture §5.

```mermaid
classDiagram
    direction TB

    class AuthenticatedSessionController {
        +store() RedirectResponse
    }
    class User {
        +UserRole role
        +hasPermission(string) bool
        +isCustomer() bool
        +isStaffMember() bool
        +isTechnician() bool
    }
    class EnsureCustomer {
        +handle() Response
    }
    class EnsureAdmin {
        +handle() Response
    }
    class EnsureTechnician {
        +handle() Response
    }
    class EnsurePermission {
        +handle(permissions) Response
    }
    class Rbac {
        +userHasPermission(User, string) bool
        +permissionsForRole(UserRole) array
    }
    class UserRole {
        <<enum>>
        Customer Admin Manager Staff Technician
    }
    class Permission {
        <<enum>>
        OrdersView OrdersManage
        CatalogManage ProductionAssign
        UsersManage
    }
    class ConfigRbac {
        <<config>>
        config/rbac.php
    }

    AuthenticatedSessionController ..> User : authenticates
    EnsureCustomer ..> User : role=customer
    EnsureAdmin ..> User : panel role
    EnsureTechnician ..> User : role=technician
    EnsurePermission ..> Rbac : checks
    EnsurePermission ..> User : current user
    User ..> Rbac : hasPermission()
    Rbac ..> ConfigRbac : reads
    Rbac ..> Permission : maps
    User --> UserRole
```

---

## 5. Domain model (implemented + planned)

Maps System Architecture §6.

```mermaid
classDiagram
    direction TB

    class User {
        <<Model ✅>>
        +orders() HasMany
        +assignedOrders() HasMany
    }
    class CatalogDesign {
        <<Model ✅>>
        +images() HasMany
        +orders() HasMany
    }
    class CatalogImage {
        <<Model ✅>>
        +catalogDesign() BelongsTo
    }
    class Order {
        <<Model ✅>>
        +user() BelongsTo
        +catalogDesign() BelongsTo
        +assignedTechnician() BelongsTo
        +productionLogs() HasMany
    }
    class ProductionLog {
        <<Model ✅>>
        +order() BelongsTo
        +user() BelongsTo
    }
    class MetalPrice {
        <<Model ✅>>
        +updatedBy() BelongsTo
    }
    class Invoice {
        <<Model 🔜>>
        +order() BelongsTo
        +items() HasMany
        +payments() HasMany
    }
    class InvoiceItem {
        <<Model 🔜>>
        +invoice() BelongsTo
    }
    class Payment {
        <<Model 🔜>>
        +invoice() BelongsTo
        +paymentMethod() BelongsTo
    }
    class PaymentMethod {
        <<Model 🔜>>
        +payments() HasMany
    }
    class Category {
        <<Model 🔜>>
        +catalogDesigns() HasMany
    }
    class ReportExport {
        <<Model 🔜>>
        +generatedBy() BelongsTo
    }

    User "1" --> "*" Order
    User "1" --> "*" Order : assigned
    User "1" --> "*" ProductionLog
    User "1" --> "*" MetalPrice
    CatalogDesign "1" --> "*" CatalogImage
    CatalogDesign "1" --> "*" Order
    Order "1" --> "*" ProductionLog
    Order "1" --> "0..1" Invoice
    Invoice "1" --> "*" InvoiceItem
    Invoice "1" --> "*" Payment
    PaymentMethod "1" --> "*" Payment
    Category "1" --> "*" CatalogDesign
    User "1" --> "*" Invoice
    User "1" --> "*" ReportExport
```

---

## 6. Controller layer (full list) ✅

| Package | Class | Uses model(s) |
|---------|-------|---------------|
| `Auth` | AuthenticatedSessionController, RegisteredUserController, PasswordResetLinkController, NewPasswordController, PasswordController, VerifyEmailController, … | User |
| `Customer` | DashboardController, OrderController | Order, CatalogDesign, User |
| `Admin` | DashboardController, OrderController, OrderAssignmentController, CatalogDesignController, CustomerController, WorkshopController, MetalPriceController, StaffUserController | All models |
| `Technician` | DashboardController, JobController | Order, ProductionLog |
| — | CatalogController | CatalogDesign |
| — | ProfileController | User |

---

## 7. Billing module class diagram 🔜

Maps System Architecture §10.

```mermaid
classDiagram
    direction TB

    class InvoiceController {
        <<planned>>
        +index() View
        +create(Order) View
        +store() RedirectResponse
        +show() View
        +print() Response
    }
    class InvoiceService {
        <<planned>>
        +generateFromOrder(Order) Invoice
        +calculateTotals() void
    }
    class Invoice {
        <<planned>>
        +grand_total: decimal
        +status: string
    }
    class InvoiceItem {
        <<planned>>
        +line_total: decimal
    }
    class PaymentController {
        <<planned>>
        +store() RedirectResponse
    }
    class Payment {
        <<planned>>
        +amount: decimal
    }
    class PaymentMethod {
        <<planned>>
        cash card bank_transfer
    }
    class Order {
        <<Model ✅>>
    }
    class User {
        <<Model ✅>>
    }

    InvoiceController ..> InvoiceService
    InvoiceService ..> Invoice : creates
    InvoiceService ..> InvoiceItem : creates
    InvoiceService ..> Order : reads
    Invoice "1" --> "*" InvoiceItem
    Invoice "1" --> "*" Payment
    PaymentController ..> Payment : creates
    Payment --> PaymentMethod
    Invoice --> Order
    Invoice --> User
```

---

## 8. Reports module class diagram 🔜

Maps System Architecture §11.

```mermaid
classDiagram
    direction TB

    class ReportController {
        <<planned>>
        +index() View
        +orders() View
        +sales() View
        +customers() View
        +production() View
        +delivery() View
        +inventory() View
        +billing() View
        +exportPdf() Response
    }
    class ReportEngine {
        <<planned>>
        +orderSummary(filters) Collection
        +salesRevenue(filters) Collection
        +customerReport(filters) Collection
        +productionReport(filters) Collection
        +deliveryReport(filters) Collection
        +inventoryReport(filters) Collection
        +billingCollection(filters) Collection
    }
    class ReportExport {
        <<planned>>
        +report_type: string
        +file_path: string
    }
    class FilterReportRequest {
        <<planned>>
        +date_from date_to
    }
    class Order { <<Model ✅>> }
    class User { <<Model ✅>> }
    class CatalogDesign { <<Model ✅>> }
    class ProductionLog { <<Model ✅>> }
    class Invoice { <<planned>> }
    class Payment { <<planned>> }
    class MetalPrice { <<Model ✅>> }

    ReportController ..> ReportEngine
    ReportController ..> FilterReportRequest
    ReportController ..> ReportExport : saves
    ReportEngine ..> Order
    ReportEngine ..> User
    ReportEngine ..> CatalogDesign
    ReportEngine ..> ProductionLog
    ReportEngine ..> Invoice
    ReportEngine ..> Payment
    ReportEngine ..> MetalPrice
```

---

## 9. Complete class inventory

| Layer | Implemented ✅ | Planned 🔜 |
|-------|----------------|------------|
| **Models** | User, Order, CatalogDesign, CatalogImage, ProductionLog, MetalPrice | Invoice, InvoiceItem, Payment, PaymentMethod, Category, Notification, ReportExport |
| **Controllers** | 20+ (Auth, Customer, Admin, Technician, Catalog, Profile) | InvoiceController, PaymentController, ReportController |
| **Middleware** | EnsureCustomer, EnsureAdmin, EnsureTechnician, EnsurePermission | — |
| **Enums** | UserRole, Permission, OrderStatus, DesignType, AvailabilityStatus | InvoiceStatus, PaymentStatus (optional) |
| **Support** | Rbac, ValidationRules, ValidPhone | InvoiceService, ReportEngine |
| **Form Requests** | 22 classes | FilterReportRequest, StoreInvoiceRequest, StorePaymentRequest |
| **View Components** | AppLayout, AdminLayout, TechnicianLayout, GuestLayout, PublicLayout | — |

---

## 10. Viva one-liner

> **The system architecture class diagram shows a 3-tier Laravel MVC design: Blade views and layout components in the presentation layer; controllers grouped by module (Customer, Admin, Technician) with middleware and FormRequests in the application layer; six Eloquent models and five enums in the domain layer; and MySQL plus file storage in the data layer. Billing and Reports add Invoice, Payment, ReportController, and ReportEngine classes in Sprint 9–10.**

---

*Open [`SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.html`](SYSTEM_ARCHITECTURE_CLASS_DIAGRAM.html) for all figures · Print → PDF for report.*
