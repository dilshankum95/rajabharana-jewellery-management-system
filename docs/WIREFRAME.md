# Wireframe Diagrams — Rajabharana Jewellery System

| Resource | File |
|----------|------|
| **Visual (browser / PDF)** | [`WIREFRAME.html`](WIREFRAME.html) |
| **Architecture** | [`SYSTEM_ARCHITECTURE.md`](SYSTEM_ARCHITECTURE.md) |

**Legend:** ✅ Implemented · 🔜 Planned · ▢ placeholder · ▣ button · ▤ input/table

**Stack:** Laravel 12 · Blade · Tailwind · 4 panels + public site

---

## Figure 0 — Site map

```
Rajabharana Jewellery System
├── Public (Guest)
│   ├── /              Home
│   ├── /catalog       Browse designs
│   └── /catalog/{id}  Design detail
├── Auth
│   ├── /login         Login
│   ├── /register      Register
│   └── /forgot-password
├── Customer Portal (/dashboard, /orders)
│   ├── Dashboard
│   ├── My Orders
│   ├── Place Order
│   └── Order Detail
├── Admin Panel (/admin/*)
│   ├── Dashboard
│   ├── Orders → Order Detail
│   ├── Workshop → Technicians → Workload
│   ├── Customers → Customer Detail
│   ├── Inventory (Catalog)
│   ├── Metal Prices
│   └── Staff Accounts
├── Workshop Panel (/technician/*)
│   ├── My Jobs
│   └── Job Detail
└── Planned 🔜
    ├── Billing / Invoice
    └── Reports
```

---

## Wireframe index (22 screens)

| # | Screen | Route | Panel | Status |
|---|--------|-------|-------|--------|
| W1 | Home | `/` | Public | ✅ |
| W2 | Catalog browse | `/catalog` | Public | ✅ |
| W3 | Design detail | `/catalog/{id}` | Public | ✅ |
| W4 | Login | `/login` | Auth | ✅ |
| W5 | Register | `/register` | Auth | ✅ |
| W6 | Customer dashboard | `/dashboard` | Customer | ✅ |
| W7 | My orders | `/orders` | Customer | ✅ |
| W8 | Place order | `/orders/create` | Customer | ✅ |
| W9 | Order detail | `/orders/{id}` | Customer | ✅ |
| W10 | Admin dashboard | `/admin` | Admin | ✅ |
| W11 | Orders list | `/admin/orders` | Admin | ✅ |
| W12 | Order detail (admin) | `/admin/orders/{id}` | Admin | ✅ |
| W13 | Customers | `/admin/customers` | Admin | ✅ |
| W14 | Inventory / catalog | `/admin/catalog` | Admin | ✅ |
| W15 | Workshop queue | `/admin/workshop` | Admin | ✅ |
| W16 | Technicians roster | `/admin/workshop/technicians` | Admin | ✅ |
| W17 | Metal prices | `/admin/metal-prices` | Admin | ✅ |
| W18 | Staff accounts | `/admin/users` | Admin | ✅ |
| W19 | Technician dashboard | `/technician` | Technician | ✅ |
| W20 | Job detail | `/technician/jobs/{id}` | Technician | ✅ |
| W21 | Generate invoice | `/admin/invoices/*` | Admin | 🔜 |
| W22 | Reports | `/admin/reports` | Admin | 🔜 |

Open [`WIREFRAME.html`](WIREFRAME.html) for visual wireframes · Print → PDF
