# Activity Diagram — Administrator (Include & Extend)

**Rajabharana Jewellery Management System**

| Resource | File |
|----------|------|
| **Visual (browser / PDF)** | [`ACTIVITY_DIAGRAM_ADMIN.html`](ACTIVITY_DIAGRAM_ADMIN.html) |
| **Admin tasks reference** | [`USER_ROLES_FUNCTIONS.md`](USER_ROLES_FUNCTIONS.md) |
| **Use cases** | [`USE_CASE_DIAGRAM.md`](USE_CASE_DIAGRAM.md) |

**Actor:** Administrator (`role = admin`, permission `*`)  
**Panel:** `/admin/*`

**Legend:** ✅ Implemented · 🔜 Planned

**UML on activity diagrams:**
- **«include»** — mandatory sub-activity always executed by the base activity
- **«extend»** — optional sub-activity executed only when a **condition** is true at an extension point

---

## Figure 1 — Admin session overview

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    A1[Open Admin Panel]
    A2[Select Module]

    subgraph INC1[" «include» Authenticate "]
        I1[Enter email & password]
        I2[Verify credentials]
        I3[Check role = admin]
        I1 --> I2 --> I3
    end

    subgraph INC2[" «include» Verify RBAC "]
        R1[Load permission map]
        R2[Grant wildcard access *]
        R1 --> R2
    end

    M1[View Dashboard]
    M2[Manage Orders]
    M3[Manage Catalogue]
    M4[Manage Workshop]
    M5[Manage Staff]
    M6[Update Metal Prices]
    M7[Generate Reports 🔜]

    START --> A1
    A1 -.->|«include»| INC1
    INC1 --> INC2
    INC2 --> A2
    A2 --> M1 & M2 & M3 & M4 & M5 & M6 & M7
    M1 & M2 & M3 & M4 & M5 & M6 & M7 --> END
```

---

## Figure 2 — Manage order (main workflow)

**Route:** `/admin/orders` · `/admin/orders/{id}` · PATCH update

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    O1[List / Filter Orders]
    O2[Select Order]
    O3[View Order Detail]
    O4[Edit Status / Price / Delivery / Notes]
    O5[Save Order Update]
    O6{Delivery overdue\nor due soon?}
    O7[Show Delivery Warning]
    D1{Order confirmed\n& in production?}
    D2{Mark delivered?}

    subgraph INC1[" «include» Authenticate "]
        AUTH[Verify admin session]
    end

    subgraph INC2[" «include» Load Order Data "]
        L1[Load customer & design]
        L2[Load production logs]
        L1 --> L2
    end

    subgraph EXT1[" «extend» Assign Technician "]
        T1[Select technician]
        T2[Update assigned_technician_id]
        T3[Create production log]
        T1 --> T2 --> T3
    end

    subgraph EXT2[" «extend» Generate Invoice 🔜 "]
        B1[Create invoice from order]
        B2[Add invoice line items]
        B1 --> B2
    end

    START --> O1
    O1 -.->|«include»| INC1
    O1 --> O2 --> O3
    O3 -.->|«include»| INC2
    O3 --> O4 --> O5 --> O6
    O6 -->|Yes| O7 --> END
    O6 -->|No| END
    O3 --> D1
    D1 -->|Yes| EXT1
    EXT1 --> O5
    O5 --> D2
    D2 -->|Yes| EXT2
    EXT2 --> END
```

| Relationship | When |
|--------------|------|
| **«include» Authenticate** | Every admin order action |
| **«include» Load Order Data** | Always when viewing order detail |
| **«extend» Assign Technician** | Order is confirmed and in active production |
| **«extend» Generate Invoice** 🔜 | Order status = Delivered |
| **«extend» Delivery Warning** | Expected date passed or due within window |

---

## Figure 3 — Assign technician to order

**Route:** PATCH `/admin/orders/{id}/assign-technician`

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    A1[Open Order Detail]
    A2{Unassign\ntechnician?}
    A3[Clear assignment]
    A4{Order assignable?}
    A5[Show error]
    A6[Select Technician]
    A7[Save assignment]
    A8{Already assigned\nto same tech?}

    subgraph INC1[" «include» Authenticate "]
        AUTH[Verify admin + production.assign]
    end

    subgraph INC2[" «include» Write Production Log "]
        LOG[INSERT production_logs]
    end

    subgraph EXT1[" «extend» View Workshop Queue "]
        W1[Browse /admin/workshop]
        W2[Check technician workload]
        W1 --> W2
    end

    START --> A1
    A1 -.->|«include»| INC1
    A1 -.->|«extend»| EXT1
    EXT1 --> A6
    A1 --> A2
    A2 -->|Yes| A3 --> INC2 --> END
    A2 -->|No| A4
    A4 -->|No| A5 --> END
    A4 -->|Yes| A6 --> A8
    A8 -->|No| A7 --> INC2 --> END
    A8 -->|Yes| END
```

---

## Figure 4 — Manage catalogue design

**Route:** `/admin/catalog/*`

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    C1[List Catalogue Designs]
    C2{Action?}
    C3[Open Create Form]
    C4[Open Edit Form]
    C5[Confirm Delete]
    C6[Save Design to DB]

    subgraph INC1[" «include» Authenticate "]
        AUTH[Verify admin session]
    end

    subgraph INC2[" «include» Validate Design Form "]
        V1[Validate name, code, category]
        V2[Validate price & weight]
        V1 --> V2
    end

    subgraph EXT1[" «extend» Upload Images "]
        U1[Store image files]
        U2[Set sort order & primary]
        U1 --> U2
    end

    subgraph EXT2[" «extend» Set Availability "]
        S1[Update in_stock / out_of_stock]
    end

    START --> C1
    C1 -.->|«include»| INC1
    C1 --> C2
    C2 -->|Create| C3 --> INC2
    C2 -->|Edit| C4 --> INC2
    C2 -->|Delete| C5 --> END
    INC2 --> C6
    C6 -.->|«extend»| EXT1
    C6 -.->|«extend»| EXT2
    EXT1 --> END
    EXT2 --> END
    C6 --> END
```

---

## Figure 5 — Manage staff account

**Route:** `/admin/users/*`

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    S1[List Staff Accounts]
    S2{Action?}
    S3[Fill Create Form]
    S4[Fill Edit Form]
    S5[Confirm Delete]
    S6[Save to users table]

    subgraph INC1[" «include» Authenticate "]
        AUTH[Verify users.manage permission]
    end

    subgraph INC2[" «include» Validate Staff Form "]
        V1[Validate name, email, role]
        V2[Hash password]
        V1 --> V2
    end

    subgraph EXT1[" «extend» Guard Last Admin "]
        G1{Last admin\nbeing removed?}
        G2[Block — show error]
        G1 -->|Yes| G2
    end

    START --> S1
    S1 -.->|«include»| INC1
    S1 --> S2
    S2 -->|Create| S3 --> INC2 --> S6 --> END
    S2 -->|Edit| S4 --> INC2 --> EXT1
    EXT1 -->|No| S6 --> END
    EXT1 -->|Yes| END
    S2 -->|Delete| S5 --> EXT1
    S5 --> S6
```

---

## Figure 6 — Update metal prices

**Route:** `/admin/metal-prices`

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    M1[Open Metal Prices Form]
    M2[Enter Gold & Silver per Gram]
    M3[Submit Update]
    M4[Show Success Message]

    subgraph INC1[" «include» Authenticate "]
        AUTH[Verify metal-prices.manage]
    end

    subgraph INC2[" «include» Upsert Metal Price "]
        U1[Load or create today's record]
        U2[Set updated_by = admin]
        U3[Save gold_price_per_gram\nsilver_price_per_gram]
        U1 --> U2 --> U3
    end

    START --> M1
    M1 -.->|«include»| INC1
    M1 --> M2 --> M3
    M3 -.->|«include»| INC2
    INC2 --> M4 --> END
```

---

## Figure 7 — View customers & workshop (read flows)

```mermaid
flowchart TB
    START((●))
    END(((⏹)))

    CH{Choose module}
    V1[View Customer List]
    V2[View Customer Profile & Orders]
    W1[View Workshop Queue]
    W2[View Technician Roster]
    W3[View Technician Workload]

    subgraph INC1[" «include» Authenticate "]
        AUTH[Verify admin session]
    end

    subgraph INC2[" «include» Load Related Data "]
        LD[Query users / orders / logs]
    end

    START --> CH
    CH -->|Customers| V1 --> V2
    CH -->|Workshop| W1
    W1 -.->|«extend»| W2
    W2 -.->|«extend»| W3
    V1 & V2 & W1 & W2 & W3 -.->|«include»| INC1
    V2 & W3 -.->|«include»| INC2
    V2 & W3 --> END
    W1 --> END
```

---

## Include & extend summary

| Base activity | «include» (mandatory) | «extend» (conditional) |
|---------------|----------------------|-------------------------|
| **Admin session** | Authenticate, Verify RBAC | — |
| **Manage order** | Authenticate, Load order data | Assign technician, Generate invoice 🔜, Delivery warning |
| **Assign technician** | Authenticate, Write production log | View workshop queue first |
| **Manage catalogue** | Authenticate, Validate form | Upload images, Set availability |
| **Manage staff** | Authenticate, Validate form | Guard last admin |
| **Update metal prices** | Authenticate, Upsert price record | — |
| **View customers/workshop** | Authenticate, Load related data | Technician roster → workload |

---

## Viva one-liner

> **Admin activity flows always «include» Authenticate and validation sub-activities; Assign Technician, Upload Images, and Generate Invoice «extend» the base flow only when their preconditions are met.**

---

*Open [`ACTIVITY_DIAGRAM_ADMIN.html`](ACTIVITY_DIAGRAM_ADMIN.html) · Print → PDF*
