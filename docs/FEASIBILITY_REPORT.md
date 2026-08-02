# FEASIBILITY REPORT

## Rajabharana Jewellery Management System

---

**Prepared by:** [Your Full Name]  
**Student Index / Registration No.:** [Your Index Number]  
**Degree Programme:** [e.g. BSc (Hons) in Information Technology]  
**Institution:** [Your University / Institute Name]  
**Supervisor:** [Supervisor Name]  
**Date:** June 2025  
**Version:** 1.0  

---

## TABLE OF CONTENTS

1. Executive Summary  
2. Introduction  
3. Background of the Organization  
4. Problem Statement  
5. Objectives of the Project  
6. Scope of the System  
7. Existing System  
8. Proposed System — Rajabharana Jewellery Management System  
9. System Modules and Features (As Implemented)  
10. Technology Stack  
11. Database Design Overview  
12. User Roles and Access Control  
13. Order Processing Workflow  
14. Market Feasibility  
15. Technical Feasibility  
16. Operational Feasibility  
17. Economic Feasibility  
18. Legal, Social and Ethical Feasibility  
19. SWOT Analysis  
20. Risk Analysis  
21. Cost–Benefit Analysis  
22. Implementation Plan and Current Status  
23. Testing Strategy  
24. Conclusion and Recommendation  
25. References  
Appendix A — System URLs and Panels  
Appendix B — Database Tables  
Appendix C — Test User Accounts  
Appendix D — Order Status Definitions  

---

## 1. EXECUTIVE SUMMARY

This document presents a feasibility study for the **Rajabharana Jewellery Management System**, a web-based application developed to support the daily operations of Rajabharana Jewellery — a jewellery business in Sri Lanka that sells catalogue items and accepts custom-designed orders.

The system replaces manual, paper-based and informal communication methods with a centralized digital platform. It provides:

- A **Customer Portal** for browsing the catalogue, placing orders, and tracking order status  
- An **Admin Panel** for order management, customer records, inventory, metal prices, staff accounts, and workshop operations  
- A separate **Workshop (Technician) Panel** for production staff to view assigned jobs and update manufacturing progress  

The feasibility study evaluates the project from market, technical, operational, economic, and legal perspectives. Based on the analysis and the successful development of a working prototype, the project is assessed as **FEASIBLE** and **RECOMMENDED** for deployment at Rajabharana Jewellery.

---

## 2. INTRODUCTION

Jewellery businesses in Sri Lanka manage complex operations: customer consultations, custom design specifications, gold quality selection, gemstone details, workshop production, quality checking, and final delivery. When these processes are handled manually, information is easily lost, delivery dates are missed, and customers cannot track their orders online.

The Rajabharana Jewellery Management System was developed as a final-year / business IT project to solve these problems using modern web technologies. This feasibility report documents why the system is viable, what it contains, and how it benefits the organization.

---

## 3. BACKGROUND OF THE ORGANIZATION

**Organization Name:** Rajabharana Jewellery  

**Nature of Business:** Retail and custom jewellery manufacturing  

**Services Offered:**

- Sale of ready-made jewellery from an in-house catalogue (rings, necklaces, bracelets, earrings, chains, pendants, bangles, anklets)  
- Custom jewellery orders based on customer specifications and reference images  
- Gold jewellery in multiple qualities (24K, 22K, 18K, 14K, white gold, rose gold)  
- Gemstone-set pieces with detailed stone specifications  

**Currency Used in System:** Sri Lankan Rupees (LKR)  

**Staff Categories:**

- Administrator  
- Inventory Manager  
- Sales Staff  
- Workshop Technician  
- Customer (online registered user)  

---

## 4. PROBLEM STATEMENT

The following problems were identified before developing the system:

**4.1** Customer orders were recorded manually, leading to missing details and duplicate data entry.  

**4.2** Customers had no online way to check order status, causing frequent phone calls to the shop.  

**4.3** Custom order pricing was inconsistent because quotes were not stored in one central system.  

**4.4** Daily gold and silver rates were not linked to the ordering and catalogue process.  

**4.5** Workshop jobs were assigned verbally with no formal record of who was working on which order.  

**4.6** Technicians had access to unnecessary customer contact details instead of only job specifications.  

**4.7** Delivery deadlines were not monitored systematically, resulting in overdue orders.  

**4.8** Staff had no role-based access control — all users could potentially see or change data outside their job function.  

**4.9** Catalogue inventory (designs, images, stock status, prices) was not managed through a single application.  

---

## 5. OBJECTIVES OF THE PROJECT

### 5.1 Primary Objectives

1. Develop a web-based jewellery management system for Rajabharana Jewellery.  
2. Allow customers to register, browse the catalogue, and place catalogue or custom orders online.  
3. Provide administrators with tools to confirm orders, set prices, and manage the full order lifecycle.  
4. Enable workshop technicians to view assigned jobs and update production status up to “Ready for Pickup”.  
5. Implement role-based access control (RBAC) for all staff types.  
6. Protect customer privacy by showing technicians only order specifications, not contact details.  

### 5.2 Secondary Objectives

1. Display daily gold and silver metal rates to customers and staff.  
2. Alert administrators about overdue and due-soon deliveries.  
3. Maintain a production log for technician assignments and workshop updates.  
4. Validate all user input through centralized form validation rules.  
5. Provide a professional, responsive user interface suitable for office and workshop use.  

---

## 6. SCOPE OF THE SYSTEM

### 6.1 In Scope (Implemented)

- User registration, login, email verification, and password management  
- Customer profile with compulsory phone number and address  
- Public catalogue browsing and design detail pages  
- Customer order placement (catalogue design or custom design with reference image upload)  
- Order status tracking and customer-initiated cancellation  
- Admin dashboard with business KPIs and delivery alerts  
- Admin order management (status, price, delivery date, internal notes)  
- Admin customer listing and detail views  
- Admin catalogue/inventory management with multiple images per design  
- Admin daily metal price management (gold and silver per gram in LKR)  
- Admin staff account management with role assignment  
- Admin workshop/production queue with technician assignment  
- Separate technician panel at `/technician`  
- Production log audit trail  
- Form validation on all major forms  

### 6.2 Out of Scope (Future Enhancements)

- Online payment gateway (PayHere, card payments, etc.)  
- SMS and email order notifications  
- Native mobile applications (iOS/Android)  
- Multi-branch / franchise support  
- Integrated accounting and payroll  
- Automated AI-based design feasibility scoring  

---

## 7. EXISTING SYSTEM

Before this project, Rajabharana Jewellery relied on manual processes:

| Activity | Existing Method | Limitation |
|----------|-----------------|------------|
| Order recording | Paper forms / notebooks | Difficult to search and track |
| Customer communication | Phone calls and shop visits | Time-consuming for staff |
| Pricing | Manual calculation | Inconsistent quotes |
| Metal rates | Whiteboard or verbal updates | Not visible to online customers |
| Production tracking | Verbal assignment to technicians | No audit trail |
| Inventory | Physical display / memory | No digital stock status |
| Staff access | Shared knowledge | No permission control |

This existing system was slow, error-prone, and did not support online customer self-service.

---

## 8. PROPOSED SYSTEM — RAJABHARANA JEWELLERY MANAGEMENT SYSTEM

The proposed system is a three-panel web application built on Laravel 12. Each user type logs in and is redirected to the appropriate interface:

| User Type | Panel | Login Redirect |
|-----------|-------|----------------|
| Customer | Customer Portal (`/dashboard`) | Customer dashboard |
| Administrator, Inventory Manager, Sales Staff | Admin Panel (`/admin`) | Admin dashboard or inventory (based on role) |
| Workshop Technician | Workshop Panel (`/technician`) | Technician job dashboard |

**System Architecture (Logical):**

Customer Portal ──┐  
Admin Panel ──────┼──► Laravel Application (Business Logic, RBAC, Validation) ──► MySQL Database  
Workshop Panel ───┘  

Public visitors can browse the catalogue without logging in. Registered and verified customers can place orders. Staff access is restricted by role-based permissions.

---

## 9. SYSTEM MODULES AND FEATURES (AS IMPLEMENTED)

### 9.1 Public Module

- Home/welcome page  
- Catalogue listing with category filter  
- Individual design detail page  
- Purchase flow (redirects to login if not authenticated)  

### 9.2 Customer Module

- Customer dashboard with order summary and today’s metal rates  
- Place new order (catalogue item or custom design)  
- Upload reference image for custom designs  
- View order list and individual order details  
- Cancel eligible orders  
- Profile management (name, email, phone, address, city, password)  
- Profile completion required before placing orders  

### 9.3 Admin Module

**Dashboard**
- Total orders, pending orders, in-production count, ready orders  
- Total registered customers and quoted order value  
- Overdue and due-soon delivery counts with highlighted order lists  
- Today’s gold and silver rates  
- Recent orders and pending review queue  

**Order Management**
- Filter and search orders by status, customer, and due date  
- View full order details (customer, design, specifications, pricing)  
- Update order status, expected delivery date, estimated price, and admin notes  
- Overdue/due-soon row highlighting on order list  

**Workshop Management**
- Production queue for confirmed through ready orders  
- KPI cards: queue total, unassigned, in production, quality check, ready  
- Filter by status, technician, unassigned jobs, and search  
- Inline technician assignment (admin only)  
- Technician roster with workload counts  
- Individual technician workload view  

**Customer Management**
- List and search registered customers  
- View customer profile and order history  

**Inventory / Catalogue Management**
- Create, edit, and delete catalogue designs  
- Fields: name, code, description, category, gold quality, weight, selling price, availability status  
- Multiple images per design with primary image selection  
- Image upload and deletion  

**Metal Prices**
- Set daily gold price per gram (LKR)  
- Set daily silver price per gram (LKR)  
- Track who updated the rate and when  

**Staff Account Management (Admin only)**
- Create, edit, and delete staff accounts  
- Assign roles: Administrator, Inventory Manager, Sales Staff, Workshop Technician  

### 9.4 Workshop (Technician) Module

- Separate panel — not part of the admin sidebar  
- Dashboard showing active assigned jobs with due-date highlighting  
- Job detail page with design type, catalogue reference, reference image, and full specifications  
- Does NOT show customer name, email, phone, or delivery address  
- Update job status: In Production → Quality Check → Ready for Pickup  
- Add workshop notes stored in production log  
- View production history for each job  

### 9.5 Security and Validation Module

- Laravel Breeze authentication (login, register, password reset, email verification)  
- Password policy: minimum 8 characters with uppercase, lowercase, and number  
- Middleware: EnsureCustomer, EnsureAdmin, EnsureTechnician, EnsurePermission  
- Centralized validation rules in `ValidationRules.php`  
- Dedicated Form Request classes for all major forms  
- HTML5 validation attributes on Blade forms  
- Input sanitization trait on form requests  

---

## 10. TECHNOLOGY STACK

| Layer | Technology | Version / Detail |
|-------|------------|----------------|
| Backend Framework | Laravel | 12.x |
| Programming Language | PHP | 8.2 or higher |
| Authentication | Laravel Breeze | 2.4 (dev) |
| Template Engine | Blade | Included with Laravel |
| CSS Framework | Tailwind CSS | 3.x |
| JavaScript | Alpine.js | 3.4 |
| Build Tool | Vite | 7.x |
| Database | MySQL | 8.x |
| ORM | Eloquent | Included with Laravel |
| HTTP Client | Axios | 1.11 |
| Server (Development) | PHP built-in / Laravel Herd | — |
| Version Control | Git | — |

**Why this stack was chosen:**

- Laravel provides secure authentication, migrations, validation, and rapid development.  
- MySQL is reliable for relational order and inventory data.  
- Tailwind CSS enables a professional, responsive UI without heavy custom CSS.  
- Blade templates are easy to maintain for a single-business application.  
- All core technologies are free and open-source, reducing project cost.  

---

## 11. DATABASE DESIGN OVERVIEW

The system uses the following main database tables:

### 11.1 users

Stores customers and all staff accounts.

Key fields: name, email, password, role, phone, address, city, profile_photo_path, email_verified_at

Roles stored: customer, admin, manager, staff, technician

### 11.2 orders

Stores all customer orders.

Key fields: order_number, user_id, design_type, catalog_design_id, reference_image_path, item_type, item_name, size, weight_grams, specifications, gold_quality, gemstone_type, gemstone_details, quantity, special_instructions, expected_delivery_date, contact_phone, delivery_address, status, estimated_price, admin_notes, assigned_technician_id, assigned_at

Order numbers are auto-generated in format: RJ-YYYYMMDD-XXXX

### 11.3 catalog_designs

Stores jewellery catalogue items.

Key fields: name, code, description, category, image_path, default_gold_quality, starting_weight_grams, selling_price, availability_status, is_active

### 11.4 catalog_images

Stores multiple images linked to each catalogue design with primary image flag.

### 11.5 metal_prices

Stores daily gold and silver rates.

Key fields: gold_price_per_gram, silver_price_per_gram, price_date, updated_by

### 11.6 production_logs

Stores workshop activity audit trail.

Key fields: order_id, user_id, from_status, to_status, note, timestamps

---

## 12. USER ROLES AND ACCESS CONTROL

The system implements Role-Based Access Control (RBAC) through `config/rbac.php` and the `Permission` enum.

| Role | Panel Access | Permissions |
|------|--------------|-------------|
| Customer | Customer Portal | Place and view own orders; manage profile |
| Sales Staff | Admin Panel | Dashboard, view/manage orders, view customers, view catalogue |
| Inventory Manager | Admin Panel | View and manage catalogue/inventory only |
| Workshop Technician | Workshop Panel | View and update assigned production jobs |
| Administrator | Admin Panel | Full access to all modules including staff and workshop assignment |

**Permission list defined in the system:**

- dashboard.view  
- orders.view  
- orders.manage  
- customers.view  
- catalog.view  
- catalog.manage  
- metal-prices.manage  
- users.manage  
- production.view  
- production.assign  
- production.manage  

Only the Administrator has full access. Technician assignment is restricted to users with `production.assign` permission (Administrator only in this project).

---

## 13. ORDER PROCESSING WORKFLOW

### 13.1 Order Status Flow

Pending Review → Confirmed → In Production → Quality Check → Ready for Pickup → Delivered

Orders may also be set to Cancelled at appropriate stages by admin or customer.

### 13.2 Process Description

**Step 1 — Customer places order**  
Customer selects catalogue design or submits custom design with specifications, gold quality, gemstone details, quantity, expected delivery date, and reference image (optional).

**Step 2 — Admin reviews (Pending Review)**  
Administrator or Sales Staff reviews the order, sets the estimated price (especially for custom designs), and confirms or adjusts the delivery date.

**Step 3 — Order confirmed**  
Status changed to Confirmed. Order becomes eligible for technician assignment.

**Step 4 — Technician assigned (Admin only)**  
Administrator assigns a workshop technician from the order page or Workshop queue.

**Step 5 — Production**  
Technician updates status through the Workshop Panel: In Production → Quality Check → Ready for Pickup. Workshop notes are recorded in the production log.

**Step 6 — Delivery**  
Admin marks order as Delivered when the customer collects the item.

### 13.3 Delivery Monitoring

The system automatically flags orders as:

- **Overdue** — expected delivery date has passed and order is not yet delivered  
- **Due Soon** — expected delivery within 3 days (configurable in `config/jewellery.php`)  

These alerts appear on the admin dashboard and order lists.

---

## 14. MARKET FEASIBILITY

**Finding: FEASIBLE**

The Sri Lankan jewellery market continues to demand both traditional craftsmanship and modern customer convenience. Customers increasingly expect to browse designs online, submit custom requests digitally, and receive order updates without visiting the shop repeatedly.

**Target users and demand:**

- Local customers ordering wedding and occasion jewellery  
- Customers seeking custom designs with specification tracking  
- Shop staff needing organized order and inventory management  
- Workshop teams needing clear job assignments  

**Competitive advantage of this system:**

- Built specifically for jewellery workflows (gold quality, gemstones, metal rates, production stages)  
- Separate technician panel protects customer privacy  
- No recurring SaaS subscription — owned by the business  
- Can be extended with payments and notifications in future phases  

The market need for digital order management in SME jewellery businesses supports the viability of this project.

---

## 15. TECHNICAL FEASIBILITY

**Finding: FEASIBLE**

### 15.1 Hardware Requirements

**Development environment:**
- Personal computer (Windows/macOS/Linux)  
- Minimum 8 GB RAM recommended  
- Internet connection for package installation  

**Production server (minimum):**
- 1–2 vCPU, 2 GB RAM VPS or shared hosting  
- 10 GB storage for application and uploaded images  
- MySQL 8.x database  

**Client devices:**
- Any device with a modern web browser (Chrome, Edge, Firefox, Safari)  
- Responsive layout supports desktop, tablet, and mobile screens  

### 15.2 Software Requirements

- PHP 8.2+  
- Composer  
- Node.js and npm (for frontend asset build)  
- MySQL 8.x  
- Web server (Apache/Nginx) or PHP development server  

### 15.3 Technical Assessment

| Criterion | Assessment |
|-----------|------------|
| Availability of technology | All tools are free and widely available |
| Team skill requirement | PHP/Laravel skills are common in Sri Lanka |
| System performance | Suitable for SME order volumes |
| Scalability | Can be upgraded to larger hosting as business grows |
| Maintainability | Laravel migrations and modular controllers support updates |
| Security | Authentication, RBAC, validation, and password hashing implemented |

A working prototype has been successfully developed and tested in a local development environment, confirming technical feasibility.

---

## 16. OPERATIONAL FEASIBILITY

**Finding: FEASIBLE**

The system mirrors the actual workflow of Rajabharana Jewellery. Staff already follow stages from order intake to delivery; the system formalizes these steps digitally.

### 16.1 Staff Training Requirements

| Role | Training Content | Estimated Duration |
|------|------------------|-------------------|
| Administrator | All modules, workshop assignment, staff management | 2–3 hours |
| Sales Staff | Orders, customers, catalogue viewing | 1–2 hours |
| Inventory Manager | Catalogue create/edit, images, stock status | 1–2 hours |
| Workshop Technician | Workshop panel, status updates, notes | 30–60 minutes |

### 16.2 Operational Benefits

- Single source of truth for all orders  
- Reduced phone inquiries about order status  
- Clear technician workload visibility for administrators  
- Automatic delivery deadline alerts  
- Audit trail for production decisions  

### 16.3 Change Management

Staff acceptance is supported because:

- The interface uses familiar jewellery terminology  
- Technicians use a simplified separate panel  
- Administrators retain full control over pricing and assignments  

---

## 17. ECONOMIC FEASIBILITY

**Finding: FEASIBLE**

### 17.1 Development Cost (Estimated)

| Item | Cost (LKR) |
|------|------------|
| Requirement analysis and design | 30,000 – 60,000 |
| System development (academic / in-house) | 0 – 150,000 |
| Testing and documentation | 20,000 – 40,000 |
| **Total (this project — in-house development)** | **50,000 – 250,000** |

*This project was developed as a student/final-year or in-house implementation, keeping development cost minimal.*

### 17.2 Annual Operating Cost (Estimated)

| Item | Cost (LKR/year) |
|------|-----------------|
| Web hosting (VPS or shared) | 12,000 – 36,000 |
| Domain name | 3,000 – 5,000 |
| SSL certificate | Free (Let's Encrypt) |
| Maintenance and minor updates | 24,000 – 60,000 |
| **Total annual operating cost** | **39,000 – 101,000** |

### 17.3 Expected Benefits (Estimated Annual)

| Benefit | Value (LKR/year) |
|---------|------------------|
| Reduced staff time on phone status inquiries | 60,000 – 120,000 |
| Fewer missed deliveries and improved customer retention | 50,000 – 200,000 |
| More accurate custom order pricing | 30,000 – 100,000 |
| Faster order confirmation and processing | 25,000 – 75,000 |
| **Total estimated annual benefit** | **165,000 – 495,000** |

### 17.4 Payback Period

Estimated first-year total cost: approximately LKR 200,000 (development + hosting)  
Estimated annual benefit: LKR 165,000 – 495,000  

**Estimated payback period: 6 to 14 months**

---

## 18. LEGAL, SOCIAL AND ETHICAL FEASIBILITY

**Finding: FEASIBLE**

### 18.1 Legal Considerations

- Customer personal data (name, email, phone, address) is stored in the database and must be handled according to Sri Lanka’s Personal Data Protection Act (PDPA).  
- Role-based access limits who can view customer information.  
- Technicians are intentionally restricted from viewing customer contact details.  
- Passwords are hashed and never stored in plain text.  
- Recommended before public launch: publish Privacy Policy and Terms of Service on the registration page.  

### 18.2 Social Impact

- Customers benefit from transparency and convenience.  
- Employees benefit from clearer job assignments and less miscommunication.  
- The business presents a modern, professional image.  

### 18.3 Ethical Considerations

- Customer data is collected only for order fulfilment purposes.  
- Production logs support accountability without unnecessary surveillance.  
- Pricing and delivery dates are recorded accurately to maintain customer trust.  

---

## 19. SWOT ANALYSIS

**Strengths**
- Custom-built for Rajabharana Jewellery workflows  
- Role-based access control for five user types  
- Separate technician panel with privacy protection  
- Production log audit trail  
- Responsive, professional user interface  
- Open-source technology — no license fees  
- Delivery overdue/due-soon alerts built in  

**Weaknesses**
- No online payment integration yet  
- Requires internet connection at all times  
- Initial staff training needed  
- Single-business system (not multi-branch)  
- No automated SMS/email notifications yet  

**Opportunities**
- Add PayHere or bank payment gateway  
- SMS alerts for order status changes  
- Sales and production analytics reports  
- Mobile app in future phase  
- Integration with accounting software  

**Threats**
- Cybersecurity risks if server is not properly secured  
- Staff resistance to changing manual habits  
- Server downtime without backup plan  
- Gold price volatility affecting quotes  

---

## 20. RISK ANALYSIS

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Staff continue using manual methods | Medium | High | Management support; training; monitor usage |
| Incorrect price on custom order | Medium | Medium | Admin approval before confirmation |
| Missed delivery date | Medium | High | Dashboard alerts; workshop queue prioritization |
| Unauthorized data access | Low | High | RBAC; strong passwords; HTTPS in production |
| Database or server failure | Low | High | Daily automated backups |
| Wrong technician assigned | Medium | Low | Admin-only assignment; production log |
| Customer submits incomplete order | Low | Medium | Server-side and HTML5 form validation |

---

## 21. COST–BENEFIT ANALYSIS

### Three-Year Projection (Estimated)

| Year | Cost (LKR) | Benefit (LKR) | Net Benefit (LKR) |
|------|------------|---------------|-------------------|
| Year 1 | 200,000 | 300,000 | +100,000 |
| Year 2 | 70,000 | 350,000 | +280,000 |
| Year 3 | 70,000 | 400,000 | +330,000 |
| **3-Year Total** | **340,000** | **1,050,000** | **+710,000** |

### Intangible Benefits

- Improved customer trust and repeat business  
- Better management decisions from centralized data  
- Reduced stress during peak seasons (wedding season, Avurudu)  
- Foundation for future digital services  

---

## 22. IMPLEMENTATION PLAN AND CURRENT STATUS

| Phase | Activities | Duration | Status |
|-------|------------|----------|--------|
| Phase 1 | Requirement gathering, feasibility study, database design | 2–3 weeks | Completed |
| Phase 2 | Authentication, customer portal, order placement | 3–4 weeks | Completed |
| Phase 3 | Admin dashboard, order management, customer management | 2–3 weeks | Completed |
| Phase 4 | Catalogue/inventory and metal price modules | 2 weeks | Completed |
| Phase 5 | RBAC, staff accounts, form validation | 2 weeks | Completed |
| Phase 6 | Workshop module, technician panel, production log | 2 weeks | Completed |
| Phase 7 | System testing and documentation | 1–2 weeks | Completed |
| Phase 8 | Production deployment and staff training | 1 week | Pending |

**Total project duration:** approximately 14–18 weeks

---

## 23. TESTING STRATEGY

The following testing methods were applied / are planned:

**Unit Testing** — PHPUnit tests for core application logic (Laravel test suite included).  

**Feature Testing** — Tests for authentication, profile, and password update flows.  

**Manual User Acceptance Testing (UAT)** — Testing each role:

| Test Role | Test Scenarios |
|-----------|----------------|
| Customer | Register, login, place catalogue order, place custom order, view status, cancel order |
| Sales Staff | View and update orders, view customers |
| Inventory Manager | Add/edit catalogue items, upload images |
| Administrator | All admin functions, assign technician, manage staff |
| Technician | View assigned jobs, update status, add workshop note |

**Validation Testing** — All forms tested with invalid input to confirm server-side validation messages appear correctly.  

**Security Testing** — Verify customers cannot access admin routes; technicians cannot access customer contact details; unauthorized roles receive 403 errors.  

---

## 24. CONCLUSION AND RECOMMENDATION

### 24.1 Conclusion

The Rajabharana Jewellery Management System addresses real operational problems faced by Rajabharana Jewellery. The feasibility study examined market demand, technical requirements, operational fit, costs, risks, and legal considerations.

All major feasibility criteria support the project:

- **Market feasibility** — Confirmed demand for digital order management  
- **Technical feasibility** — Working prototype built on proven technologies  
- **Operational feasibility** — Aligns with existing business workflow  
- **Economic feasibility** — Positive return within approximately one year  
- **Legal/social feasibility** — Manageable with RBAC and privacy controls  

### 24.2 Recommendation

It is **recommended to deploy** the Rajabharana Jewellery Management System for live use at Rajabharana Jewellery, subject to:

1. Secure production hosting with HTTPS enabled  
2. Daily MySQL database backups  
3. Staff training for each role before go-live  
4. Publication of Privacy Policy and Terms of Service  
5. Phased rollout: admin and sales staff first, then workshop technicians  

Future Phase 2 development should prioritize SMS/email notifications and online payment integration based on business need.

---

## 25. REFERENCES

1. Laravel Documentation — https://laravel.com/docs  
2. PHP Documentation — https://www.php.net/docs.php  
3. MySQL Documentation — https://dev.mysql.com/doc/  
4. Tailwind CSS Documentation — https://tailwindcss.com/docs  
5. Personal Data Protection Act, Sri Lanka — https://www.data.gov.lk  
6. Sommerville, I. (2016). *Software Engineering*, 10th Edition. Pearson.  
7. Pressman, R.S. (2014). *Software Engineering: A Practitioner's Approach*. McGraw-Hill.  

---

## APPENDIX A — SYSTEM URLS AND PANELS

| URL | Description | Access |
|-----|-------------|--------|
| `/` | Public home page | Everyone |
| `/catalog` | Browse jewellery catalogue | Everyone |
| `/login` | User login | Everyone |
| `/register` | Customer registration | Everyone |
| `/dashboard` | Customer dashboard | Customer |
| `/orders` | Customer orders | Customer |
| `/admin` | Admin dashboard | Admin, Manager, Staff |
| `/admin/orders` | Order management | Admin, Staff |
| `/admin/workshop` | Production queue | Admin |
| `/admin/workshop/technicians` | Technician roster | Admin |
| `/admin/customers` | Customer management | Admin, Staff |
| `/admin/catalog` | Inventory management | Admin, Manager, Staff |
| `/admin/metal-prices` | Metal rate management | Admin |
| `/admin/users` | Staff account management | Admin |
| `/technician` | Workshop job dashboard | Technician |

---

## APPENDIX B — DATABASE TABLES

| Table Name | Purpose |
|------------|---------|
| users | Customers and staff accounts |
| orders | Customer jewellery orders |
| catalog_designs | Catalogue/inventory items |
| catalog_images | Multiple images per catalogue item |
| metal_prices | Daily gold and silver rates |
| production_logs | Workshop assignment and status audit trail |
| cache | Laravel system cache |
| jobs | Laravel queue jobs |
| password_reset_tokens | Password reset tokens |
| sessions | User session storage |

---

## APPENDIX C — TEST USER ACCOUNTS

These accounts are created by the database seeder for development and testing:

| Email | Password | Role |
|-------|----------|------|
| admin@rajabharana.com | password | Administrator |
| manager@rajabharana.com | Password1 | Inventory Manager |
| staff@rajabharana.com | Password1 | Sales Staff |
| technician@rajabharana.com | Password1 | Workshop Technician |
| customer@rajabharana.com | password | Customer |

---

## APPENDIX D — ORDER STATUS DEFINITIONS

| Status Code | Display Label | Description |
|-------------|---------------|-------------|
| pending | Pending Review | Order submitted; awaiting admin review and pricing |
| confirmed | Confirmed | Order accepted; ready for production assignment |
| in_production | In Production | Workshop is manufacturing the piece |
| quality_check | Quality Check | Piece completed; undergoing quality inspection |
| ready | Ready for Pickup | Order complete; waiting for customer collection |
| delivered | Delivered | Customer has collected the order |
| cancelled | Cancelled | Order cancelled by customer or admin |

---

**END OF FEASIBILITY REPORT**

*Replace all [bracketed] fields on the title page with your personal and academic details before submission.*
