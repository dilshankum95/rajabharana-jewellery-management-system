# Sequence Diagrams — Rajabharana Jewellery System

**Stack:** Laravel 12 · Blade · MySQL

| Resource | File |
|----------|------|
| **Visual (browser / PDF)** | [`SEQUENCE_DIAGRAM.html`](SEQUENCE_DIAGRAM.html) |
| **Architecture reference** | [`SYSTEM_ARCHITECTURE.md`](SYSTEM_ARCHITECTURE.md) |

**Legend:** ✅ Implemented · 🔜 Planned (Sprint 9–10)

---

## Figure 1 — Customer login ✅

```mermaid
sequenceDiagram
    autonumber
    actor Customer
    participant Browser
    participant Routes as routes/web.php
    participant MW as Middleware<br/>(guest)
    participant Auth as AuthenticatedSessionController
    participant LoginReq as LoginRequest
    participant Session as sessions table
    participant User as users table

    Customer->>Browser: Enter email + password
    Browser->>Routes: POST /login
    Routes->>MW: guest middleware
    MW->>Auth: store()
    Auth->>LoginReq: validate credentials
    LoginReq->>User: find by email, verify password
    User-->>LoginReq: User record
    LoginReq-->>Auth: authenticated
    Auth->>Session: regenerate session, store user_id
    alt role = technician
        Auth-->>Browser: redirect /technician
    else role = admin/staff/manager
        Auth-->>Browser: redirect /admin
    else role = customer
        Auth-->>Browser: redirect /dashboard
    end
    Browser-->>Customer: Show dashboard
```

---

## Figure 2 — Customer registration ✅

```mermaid
sequenceDiagram
    autonumber
    actor Customer
    participant Browser
    participant Auth as RegisteredUserController
    participant RegReq as RegisterRequest
    participant User as users table
    participant Session as sessions table

    Customer->>Browser: Fill register form
    Browser->>Auth: POST /register
    Auth->>RegReq: validate name, email, phone, address, city, password
    RegReq-->>Auth: validated data
    Auth->>User: INSERT role=customer, hashed password
    User-->>Auth: new User
    Auth->>Session: Auth::login(user)
    Auth-->>Browser: redirect /dashboard
    Browser-->>Customer: Customer portal
```

---

## Figure 3 — Customer places order ✅

```mermaid
sequenceDiagram
    autonumber
    actor Customer
    participant Browser
    participant MW as Middleware<br/>auth, verified, customer
    participant OrderCtrl as Customer\OrderController
    participant StoreReq as StoreOrderRequest
    participant Catalog as catalog_designs
    participant Storage as File Storage
    participant Order as orders table
    participant User as users table

    Customer->>Browser: Submit order form
    Browser->>MW: POST /orders
    MW->>OrderCtrl: store()
    OrderCtrl->>StoreReq: validate fields
    StoreReq-->>OrderCtrl: validated data
    opt catalog order
        OrderCtrl->>Catalog: find catalog_design_id
        Catalog-->>OrderCtrl: selling_price
        OrderCtrl->>OrderCtrl: calculate estimated_price
    end
    opt custom design image
        OrderCtrl->>Storage: store reference_image
        Storage-->>OrderCtrl: image_path
    end
    OrderCtrl->>Order: INSERT status=pending, user_id
    Order->>Order: generate order_number RJ-YYYYMMDD-XXXX
    Order-->>OrderCtrl: Order created
    OrderCtrl->>User: update phone, address
    OrderCtrl-->>Browser: redirect /orders/{id}
    Browser-->>Customer: Order confirmation page
```

---

## Figure 4 — Admin confirms & updates order ✅

```mermaid
sequenceDiagram
    autonumber
    actor Staff as Sales Staff / Admin
    participant Browser
    participant MW as Middleware<br/>auth, admin, permission:orders.manage
    participant OrderCtrl as Admin\OrderController
    participant UpdateReq as UpdateOrderRequest
    participant Order as orders table

    Staff->>Browser: Update status, price, notes
    Browser->>MW: PATCH /admin/orders/{order}
    MW->>MW: EnsureAdmin + EnsurePermission
    MW->>OrderCtrl: update()
    OrderCtrl->>UpdateReq: validate status, estimated_price, admin_notes
    UpdateReq-->>OrderCtrl: validated
    OrderCtrl->>Order: UPDATE status, estimated_price, etc.
    Order-->>OrderCtrl: updated Order
    opt delivery overdue
        OrderCtrl-->>Browser: redirect + warning flash
    else success
        OrderCtrl-->>Browser: redirect + success flash
    end
    Browser-->>Staff: Updated order detail page
```

---

## Figure 5 — Admin assigns technician ✅

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant MW as Middleware<br/>permission:production.assign
    participant AssignCtrl as OrderAssignmentController
    participant AssignReq as AssignTechnicianRequest
    participant Order as orders table
    participant User as users table
    participant Log as production_logs table

    Admin->>Browser: Select technician for order
    Browser->>MW: PATCH /admin/orders/{order}/assign-technician
    MW->>AssignCtrl: update()
    AssignCtrl->>Order: check isAssignableToTechnician()
    alt not assignable
        AssignCtrl-->>Browser: error flash
    else assignable
        AssignCtrl->>AssignReq: validate assigned_technician_id
        AssignCtrl->>Order: UPDATE assigned_technician_id, assigned_at
        AssignCtrl->>User: find technician name
        AssignCtrl->>Log: INSERT note="Assigned to technician: ..."
        Log-->>AssignCtrl: ProductionLog
        AssignCtrl-->>Browser: success flash
    end
    Browser-->>Admin: Order detail page
```

---

## Figure 6 — Technician updates production status ✅

```mermaid
sequenceDiagram
    autonumber
    actor Technician
    participant Browser
    participant MW as Middleware<br/>auth, verified, technician
    participant JobCtrl as Technician\JobController
    participant UpdateReq as UpdateProductionJobRequest
    participant Order as orders table
    participant Log as production_logs table

    Technician->>Browser: Change status + add note
    Browser->>MW: PATCH /technician/jobs/{order}
    MW->>JobCtrl: update()
    JobCtrl->>Order: verify isAssignedTo(technician)
    JobCtrl->>UpdateReq: validate status transition
    UpdateReq-->>JobCtrl: new status + note
    JobCtrl->>Order: UPDATE status
    JobCtrl->>Log: INSERT from_status, to_status, user_id, note
    Log-->>JobCtrl: ProductionLog
    JobCtrl-->>Browser: redirect /technician/jobs/{order}
    Browser-->>Technician: Job detail + updated log
```

---

## Figure 7 — Inventory manager updates catalogue ✅

```mermaid
sequenceDiagram
    autonumber
    actor Manager as Inventory Manager
    participant Browser
    participant MW as Middleware<br/>permission:catalog.manage
    participant CatalogCtrl as Admin\CatalogDesignController
    participant StoreReq as StoreCatalogDesignRequest
    participant Design as catalog_designs
    participant Images as catalog_images
    participant Storage as File Storage

    Manager->>Browser: Create / edit catalogue item
    Browser->>MW: POST or PATCH /admin/catalog
    MW->>CatalogCtrl: store() or update()
    CatalogCtrl->>StoreReq: validate name, category, price, images
    StoreReq-->>CatalogCtrl: validated
    CatalogCtrl->>Design: INSERT or UPDATE
    Design->>Design: auto-generate code RJ-YYYYMMDD-XXXX
    loop each uploaded image
        CatalogCtrl->>Storage: store image file
        CatalogCtrl->>Images: INSERT catalog_design_id, sort_order
    end
    CatalogCtrl-->>Browser: redirect admin catalog list
    Browser-->>Manager: Updated inventory
```

---

## Figure 8 — Admin updates metal prices ✅

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant MW as Middleware<br/>permission:metal-prices.manage
    participant PriceCtrl as Admin\MetalPriceController
    participant PriceReq as UpdateMetalPriceRequest
    participant MetalPrice as metal_prices table

    Admin->>Browser: Enter gold/silver rates
    Browser->>MW: PATCH /admin/metal-prices
    MW->>PriceCtrl: update()
    PriceCtrl->>PriceReq: validate prices
    PriceCtrl->>MetalPrice: upsertCurrent(gold, silver, admin_id)
    alt rate exists for today
        MetalPrice->>MetalPrice: UPDATE prices, updated_by
    else new day
        MetalPrice->>MetalPrice: INSERT new row, price_date=today
    end
    MetalPrice-->>PriceCtrl: saved
    PriceCtrl-->>Browser: redirect + success
    Browser-->>Admin: Metal price form
```

---

## Figure 9 — Generate invoice (Billing) 🔜

```mermaid
sequenceDiagram
    autonumber
    actor Staff as Sales Staff
    participant Browser
    participant MW as Middleware<br/>permission:billing.manage
    participant InvCtrl as InvoiceController
    participant InvSvc as InvoiceService
    participant Order as orders table
    participant Invoice as invoices table
    participant Items as invoice_items table

    Staff->>Browser: Generate invoice from order
    Browser->>MW: POST /admin/invoices
    MW->>InvCtrl: store(order_id)
    InvCtrl->>Order: find confirmed/ready order
    alt order cancelled or already invoiced
        InvCtrl-->>Browser: error
    else billable
        InvCtrl->>InvSvc: generateFromOrder(order)
        InvSvc->>Invoice: INSERT invoice_number, grand_total, user_id
        InvSvc->>Items: INSERT line from order snapshot
        InvSvc-->>InvCtrl: Invoice
        InvCtrl-->>Browser: redirect invoice print view
    end
    Browser-->>Staff: Printable invoice
```

---

## Figure 10 — Record payment (Payment) 🔜

```mermaid
sequenceDiagram
    autonumber
    actor Staff as Sales Staff
    participant Browser
    participant MW as Middleware<br/>permission:payment.manage
    participant PayCtrl as PaymentController
    participant PayReq as StorePaymentRequest
    participant Invoice as invoices table
    participant Payment as payments table
    participant Method as payment_methods table

    Staff->>Browser: Enter amount, method, reference
    Browser->>MW: POST /admin/payments
    MW->>PayCtrl: store()
    PayCtrl->>PayReq: validate invoice_id, amount, method
    PayReq-->>PayCtrl: validated
    PayCtrl->>Method: verify payment_method_id active
    PayCtrl->>Payment: INSERT amount, recorded_by, payment_date
    PayCtrl->>Invoice: recalculate SUM(payments)
    alt balance > 0
        PayCtrl->>Invoice: UPDATE status=partial
    else balance = 0
        PayCtrl->>Invoice: UPDATE status=paid
    end
    PayCtrl-->>Browser: redirect invoice detail
    Browser-->>Staff: Updated payment history
```

---

## Figure 11 — Generate report (Reports) 🔜

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant MW as Middleware<br/>permission:reports.view
    participant ReportCtrl as ReportController
    participant FilterReq as FilterReportRequest
    participant Engine as ReportEngine
    participant Models as Eloquent Models
    participant DB as MySQL
    participant Export as report_exports / PDF storage

    Admin->>Browser: Select report type + date range
    Browser->>MW: GET /admin/reports/sales?from=&to=
    MW->>ReportCtrl: sales()
    ReportCtrl->>FilterReq: validate date_from, date_to
    ReportCtrl->>Engine: salesRevenue(filters)
    Engine->>Models: query orders, invoices, payments
    Models->>DB: SELECT aggregate data
    DB-->>Models: result sets
    Models-->>Engine: collections
    Engine-->>ReportCtrl: report data
    ReportCtrl-->>Browser: render Blade table + KPIs
    opt export PDF
        Admin->>Browser: Click Export PDF
        Browser->>ReportCtrl: exportPdf()
        ReportCtrl->>Engine: build dataset
        ReportCtrl->>Export: save PDF + INSERT report_exports
        ReportCtrl-->>Browser: download PDF
    end
    Browser-->>Admin: Report on screen / file
```

---

## Figure 12 — Complete order lifecycle ✅ + 🔜

```mermaid
sequenceDiagram
    autonumber
    actor Customer
    actor Staff as Sales Staff
    actor Admin
    actor Technician
    participant System as Laravel Application
    participant DB as MySQL

    Customer->>System: Register / Login
    Customer->>System: Place order
    System->>DB: INSERT orders (pending)

    Staff->>System: Review order
    Staff->>System: Confirm + set price
    System->>DB: UPDATE orders (confirmed)

    Admin->>System: Assign technician
    System->>DB: UPDATE assigned_technician_id
    System->>DB: INSERT production_logs

    Technician->>System: Start production
    System->>DB: UPDATE status=in_production
    System->>DB: INSERT production_logs

    Technician->>System: Quality check → Ready
    System->>DB: UPDATE status=ready

    Note over Staff,DB: 🔜 Sprint 9 Billing
    Staff->>System: Generate invoice
    System->>DB: INSERT invoices, invoice_items

    Note over Customer,DB: 🔜 Sprint 9 Payment
    Staff->>System: Record payment(s)
    System->>DB: INSERT payments
    System->>DB: UPDATE invoice status=paid

    Staff->>System: Mark delivered
    System->>DB: UPDATE orders (delivered)

    Note over Admin,DB: 🔜 Sprint 10 Reports
    Admin->>System: Run sales / delivery report
    System->>DB: Aggregate queries
```

---

## Diagram usage in report

| Figure | Report section | Actor |
|--------|----------------|-------|
| 1–2 | Authentication design | Customer |
| 3 | Customer module / Order placement | Customer |
| 4–5 | Admin order management | Staff, Admin |
| 6 | Workshop module | Technician |
| 7–8 | Inventory & metal price | Manager, Admin |
| 9–10 | Billing & payment (planned) | Staff |
| 11 | Reports module (planned) | Admin |
| 12 | End-to-end workflow | All users |

---

## Viva one-liner

> **Sequence diagrams show message flow between actors, controllers, middleware, form requests, models, and the database — from login through order placement, admin confirmation, technician assignment, production updates, and planned billing/payment/report flows.**

---

*Open [`SEQUENCE_DIAGRAM.html`](SEQUENCE_DIAGRAM.html) · Print → PDF for Word.*
