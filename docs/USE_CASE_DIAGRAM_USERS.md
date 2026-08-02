# Use Case Diagram — Users (By Role)

**Rajabharana Jewellery Management System**

| Resource | File |
|----------|------|
| **Visual (browser / PDF)** | [`USE_CASE_DIAGRAM_USERS.html`](USE_CASE_DIAGRAM_USERS.html) |
| **Whole system use cases** | [`USE_CASE_DIAGRAM.md`](USE_CASE_DIAGRAM.md) |
| **User functions detail** | [`USER_ROLES_FUNCTIONS.md`](USER_ROLES_FUNCTIONS.md) |

**Legend:** ✅ Implemented · 🔜 Planned (Sprint 9–10)

**Six user types:** Guest · Customer · Sales Staff · Inventory Manager · Administrator · Workshop Technician

All registered users are stored in the `users` table with a `role` column. RBAC is enforced via `config/rbac.php` and middleware.

---

## Figure 1 — All users & system panels

```mermaid
flowchart LR
    G((Guest))
    C((Customer))
    S((Sales Staff))
    M((Inventory Manager))
    A((Administrator))
    T((Technician))

    subgraph System["Rajabharana Jewellery System"]
        PUB["Public Site\n/ · /catalog"]
        CP["Customer Portal\n/dashboard · /orders"]
        AP["Admin Panel\n/admin/*"]
        WP["Workshop Panel\n/technician/*"]
    end

    G --> PUB
    C --> PUB & CP
    S --> AP
    M --> AP
    A --> AP
    T --> WP
```

---

## Figure 2 — Guest use cases

**Actor:** Guest (not registered) · **Panel:** Public site only

```mermaid
flowchart TB
    G((Guest))

    subgraph SYS[" «system» Public Access "]
        UC1([Browse Catalogue])
        UC2([View Design Details])
        UC3([Register Account])
        UC4([Login])
        UC5([Forgot Password])
        UC6([Reset Password])
    end

    G --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6
    UC3 -.->|«extend»| UC1
```

| Use case | Description |
|----------|-------------|
| Browse Catalogue | Search/filter jewellery designs at `/catalog` |
| View Design Details | View photos, specs, price at `/catalog/{id}` |
| Register Account | Create customer account at `/register` |
| Login | Authenticate at `/login` |
| Forgot / Reset Password | Password recovery flow |

---

## Figure 3 — Customer use cases

**Role:** `customer` · **Panel:** `/dashboard`, `/orders`, public catalogue

```mermaid
flowchart TB
    C((Customer))

    subgraph SYS[" «system» Customer Portal "]
        UC1([Browse Catalogue])
        UC2([View Dashboard])
        UC3([Place Order])
        UC4([Track Orders])
        UC5([Cancel Order])
        UC6([Manage Profile])
        UC7([View Invoice 🔜])
        UC8([Receive Notifications 🔜])
    end

    C --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6 & UC7 & UC8
    UC3 -.->|«extend»| UC1
    UC7 -.->|«extend»| UC4
```

---

## Figure 4 — Sales Staff use cases

**Role:** `staff` · **Panel:** `/admin/*` (permission-filtered)

```mermaid
flowchart TB
    S((Sales Staff))

    subgraph SYS[" «system» Order Management "]
        UC1([Login / Logout])
        UC2([View Admin Dashboard])
        UC3([Manage Orders])
        UC4([View Customers])
        UC5([View Catalogue Read-only])
        UC6([Manage Profile])
        UC7([Generate Invoice 🔜])
        UC8([Record Payment 🔜])
        UC9([View Sales Reports 🔜])
    end

    S --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6 & UC7 & UC8 & UC9
    UC7 -.->|«include»| UC3
    UC8 -.->|«include»| UC7
```

---

## Figure 5 — Inventory Manager use cases

**Role:** `manager` · **Panel:** `/admin/catalog/*`

```mermaid
flowchart TB
    M((Inventory Manager))

    subgraph SYS[" «system» Inventory Module "]
        UC1([Login / Logout])
        UC2([List Catalogue Designs])
        UC3([Create Design])
        UC4([Edit Design])
        UC5([Delete Design])
        UC6([Upload Images])
        UC7([Set Primary Image])
        UC8([Set Availability Status])
        UC9([Manage Profile])
        UC10([Inventory Report 🔜])
    end

    M --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6 & UC7 & UC8 & UC9 & UC10
    UC6 -.->|«include»| UC3
    UC6 -.->|«include»| UC4
```

---

## Figure 6 — Administrator use cases

**Role:** `admin` · **Panel:** `/admin/*` (full access — permission `*`)

```mermaid
flowchart TB
    A((Administrator))

    subgraph SYS[" «system» Full Administration "]
        direction TB
        subgraph Orders["Sales & Orders"]
            UC1([Manage Orders])
            UC2([View Customers])
            UC3([View Admin Dashboard])
        end
        subgraph Inv["Inventory"]
            UC4([Manage Catalogue])
            UC5([Upload Images])
        end
        subgraph Admin["Administration"]
            UC6([Assign Technician])
            UC7([View Workshop Queue])
            UC8([Manage Metal Prices])
            UC9([Manage Staff Accounts])
        end
        subgraph Planned["Planned 🔜"]
            UC10([Generate Invoice])
            UC11([Record Payment])
            UC12([Generate Reports])
            UC13([Export Report PDF])
        end
        UC14([Manage Profile])
    end

    A --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6 & UC7 & UC8 & UC9 & UC10 & UC11 & UC12 & UC13 & UC14
```

*Administrator inherits all Sales Staff and Inventory Manager use cases.*

---

## Figure 7 — Workshop Technician use cases

**Role:** `technician` · **Panel:** `/technician/*` · **No customer PII**

```mermaid
flowchart TB
    T((Technician))

    subgraph SYS[" «system» Workshop Panel "]
        UC1([Login / Logout])
        UC2([View Assigned Jobs])
        UC3([View Job Specifications])
        UC4([Update Production Status])
        UC5([Add Production Log])
        UC6([Manage Profile])
        UC7([Receive Job Alerts 🔜])
    end

    T --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6 & UC7
    UC4 -.->|«include»| UC5
    UC3 -.->|«extend»| UC2
```

**Status flow:** In Production → Quality Check → Ready

---

## Figure 8 — Actor × use case matrix

| Use case | Guest | Customer | Staff | Manager | Admin | Technician |
|----------|:-----:|:--------:|:-----:|:-------:|:-----:|:----------:|
| Browse catalogue | ✅ | ✅ | | | | |
| Register / Login / Reset password | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Manage profile | | ✅ | ✅ | ✅ | ✅ | ✅ |
| View customer dashboard | | ✅ | | | | |
| Place / track / cancel order | | ✅ | | | | |
| View admin dashboard | | | ✅ | | ✅ | |
| Manage orders | | | ✅ | | ✅ | |
| View customers | | | ✅ | | ✅ | |
| Manage catalogue & images | | | | ✅ | ✅ | |
| Assign technician | | | | | ✅ | |
| View workshop queue | | | | | ✅ | |
| Manage metal prices | | | | | ✅ | |
| Manage staff accounts | | | | | ✅ | |
| View / update assigned jobs | | | | | | ✅ |
| Generate invoice 🔜 | | | ✅ | | ✅ | |
| Record payment 🔜 | | | ✅ | | ✅ | |
| View invoice 🔜 | | ✅ | | | | |
| Reports / export 🔜 | | | ✅ | ✅* | ✅ | |

*Manager: inventory report only*

---

## Shared use cases (all logged-in users)

| Use case | Route |
|----------|-------|
| Edit profile | `/profile` |
| Update password | `/password` |
| Verify email | `/verify-email` |
| Logout | POST `/logout` |

---

## Viva one-liner

> **Six user types interact with the system through four panels — Public, Customer Portal, Admin Panel, and Workshop Panel — with RBAC controlling which use cases each role can perform.**

---

*Open [`USE_CASE_DIAGRAM_USERS.html`](USE_CASE_DIAGRAM_USERS.html) · Print → PDF*
