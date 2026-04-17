# HotelHub — Project Overview

> A Laravel-based **Company Admin Dashboard** system for managing hotel clients, roles, permissions, employees, and restaurant operations across a hospitality platform.

---

## Tech Stack

| Layer       | Technology                                                         |
|-------------|--------------------------------------------------------------------|
| Backend     | PHP 8.x · Laravel 8.83                                             |
| Frontend    | Bootstrap 5 · DataTables · Feather Icons · Tabler Icons            |
| Database    | MySQL (via XAMPP) · Database: `hotelhub`                           |
| Server      | Apache (XAMPP) · `http://localhost/hotelhub` · Production: LiteSpeed |
| Storage     | Laravel Public Disk (`storage/app/public`)                         |

---

## Project Structure

```
hotelhub/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ClientMasterController.php
│   │   ├── RoleMasterController.php
│   │   ├── PermissionMasterController.php
│   │   ├── EmployeeMasterController.php
│   │   ├── CategoryMasterController.php
│   │   ├── TableTypeMasterController.php
│   │   ├── TableMasterController.php
│   │   ├── MenuMasterController.php
│   │   └── Api/                         ← Mobile API controllers (JWT)
│   │       ├── BaseApiController.php
│   │       ├── AuthController.php
│   │       ├── ProfileController.php
│   │       ├── CategoryController.php
│   │       ├── TableTypeController.php
│   │       ├── TableController.php
│   │       ├── TableStatusController.php
│   │       ├── MenuController.php
│   │       ├── OrderController.php
│   │       ├── OrderTableController.php
│   │       ├── OrderReportController.php
│   │       ├── CartController.php
│   │       ├── CheckoutController.php
│   │       ├── ClientRoleController.php
│   │       ├── ClientEmployeeController.php
│   │       └── MobilePermissionsController.php
│   ├── Http/Middleware/
│   │   ├── JwtMiddleware.php            ← web JWT (users table)
│   │   └── MobileAuthMiddleware.php     ← mobile JWT (client or employee)
│   ├── Models/
│   │   ├── User.php
│   │   ├── ClientMaster.php
│   │   ├── ClientRole.php
│   │   ├── ClientEmployee.php
│   │   ├── RoleMaster.php
│   │   ├── PermissionMaster.php
│   │   ├── EmployeeMaster.php
│   │   ├── CategoryMaster.php
│   │   ├── TableTypeMaster.php
│   │   ├── TableMaster.php
│   │   ├── MenuMaster.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Cart.php
│   │   └── CartItem.php
│   └── Providers/
│       └── AppServiceProvider.php   ← shares $base_url, $isAdmin, $userPermissions globally
├── database/
│   └── migrations/
│       ├── create_users_table.php
│       ├── create_client_masters_table.php
│       ├── create_role_masters_table.php
│       ├── create_permission_masters_table.php
│       ├── add_role_status_to_users_table.php
│       ├── create_employee_masters_table.php
│       ├── seed_admin_user.php
│       ├── create_category_masters_table.php
│       ├── create_table_type_masters_table.php
│       ├── create_table_masters_table.php
│       ├── create_menu_masters_table.php
│       └── add_subscription_to_client_masters.php
├── resources/views/
│   ├── auth/
│   │   └── login.blade.php
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── left-menu.blade.php      ← permission-gated menu
│   │   ├── header.blade.php         ← dynamic user name/role + logout
│   │   └── footer.blade.php
│   ├── dashboard.blade.php
│   ├── clients/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   ├── roles/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   ├── permissions/
│   │   ├── all.blade.php
│   │   └── edit.blade.php
│   ├── employees/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   ├── categories/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   ├── table-types/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   ├── tables/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   ├── menus/
│   │   ├── all.blade.php
│   │   ├── add.blade.php
│   │   └── edit.blade.php
│   └── category/                    ← original template folder (unused)
├── ftp-config.php               ← SFTP credentials (excluded from deploy, not in git)
├── deploy-ftp.php               ← SFTP deployment script (php deploy-ftp.php)
└── routes/
    └── web.php
```

---

## Database Tables

| Table                    | Description                                          |
|--------------------------|------------------------------------------------------|
| `users`                  | Login accounts (role_id FK, status_id)               |
| `password_resets`        | Laravel default password resets                      |
| `failed_jobs`            | Laravel default failed jobs                          |
| `personal_access_tokens` | Laravel Sanctum tokens                               |
| `client_masters`         | Hotel client registration details                    |
| `role_masters`           | System roles (Admin seeded by default)               |
| `permission_masters`     | Role-wise permissions per module (31 modules)        |
| `employee_masters`       | System employees with login accounts                 |
| `category_masters`       | Food categories (Veg, Non-Veg, Starters, etc.)       |
| `table_type_masters`     | Table types (Indoor, Outdoor, Rooftop, etc.)         |
| `table_masters`          | Individual restaurant tables per client              |
| `menu_masters`           | Menu items per client with pricing and images        |
| `orders`                 | Order headers (status, type, totals, payment)        |
| `order_items`            | Line items per order (menu_id, qty, price, gst)      |
| `client_roles`           | Client-defined roles (Waiter, Manager, etc.) + permissions |
| `client_employees`       | Restaurant employees registered via mobile app       |
| `carts`                  | Active cart per user (client or employee)            |
| `cart_items`             | Items in an active cart                              |

### `users` Columns

| Column              | Type         | Notes                                     |
|---------------------|--------------|-------------------------------------------|
| `id`                | bigint PK    | Auto increment                            |
| `role_id`           | bigint FK    | References `role_masters.role_id`         |
| `name`              | varchar      | Required                                  |
| `email`             | varchar      | Required · Unique                         |
| `password`          | varchar      | Hashed                                    |
| `status_id`         | tinyint      | 1=Active 0=Inactive · Default 1           |
| `remember_token`    | varchar      | Nullable                                  |

### `client_masters` Columns

| Column                    | Type          | Notes                                              |
|---------------------------|---------------|----------------------------------------------------|
| `client_id`               | bigint PK     | Auto increment                                     |
| `client_name`             | varchar(150)  | Required                                           |
| `image`                   | varchar       | Profile image path (nullable)                      |
| `address`                 | text          | Required                                           |
| `city`                    | varchar(100)  | Nullable                                           |
| `state`                   | varchar(100)  | Nullable                                           |
| `pincode`                 | varchar(10)   | Nullable                                           |
| `latitude`                | decimal(10,7) | Nullable                                           |
| `longitude`               | decimal(10,7) | Nullable                                           |
| `contact_number`          | varchar(15)   | Required                                           |
| `email_id`                | varchar(150)  | Required · Unique                                  |
| `aadhar_image`            | varchar       | Aadhar card image path (nullable)                  |
| `gst_number`              | varchar(20)   | Nullable                                           |
| `subscription_type`       | enum          | Monthly / Quarterly / Yearly · Nullable            |
| `subscription_price`      | decimal(10,2) | Admin-set price per client · Nullable              |
| `subscription_start_date` | date          | Nullable                                           |
| `subscription_end_date`   | date          | Auto-calculated from type + start date · Nullable  |
| `status_id`               | tinyint       | 1=Active 2=Inactive 3=Suspended 4=Trial · Default 1|
| `created_by`              | bigint        | Nullable                                           |
| `deleted_at`              | timestamp     | Soft delete                                        |

### `role_masters` Columns

| Column        | Type         | Notes                            |
|---------------|--------------|----------------------------------|
| `role_id`     | bigint PK    | Auto increment                   |
| `role_name`   | varchar(100) | Required · Unique                |
| `description` | text         | Nullable                         |
| `status_id`   | tinyint      | 1=Active 0=Inactive · Default 1  |
| `deleted_at`  | timestamp    | Soft delete                      |

> **Default seed:** `Admin` role (role_id = 1) is auto-inserted on migration.

### `permission_masters` Columns

| Column          | Type         | Notes                              |
|-----------------|--------------|------------------------------------|
| `permission_id` | bigint PK    | Auto increment                     |
| `role_id`       | bigint FK    | References `role_masters.role_id`  |
| `menu_name`     | varchar(100) | Module name                        |
| `can_view`      | tinyint      | 0 or 1 · Default 0                 |
| `can_add`       | tinyint      | 0 or 1 · Default 0                 |
| `can_edit`      | tinyint      | 0 or 1 · Default 0                 |
| `can_delete`    | tinyint      | 0 or 1 · Default 0                 |

### `employee_masters` Columns

| Column           | Type         | Notes                              |
|------------------|--------------|------------------------------------|
| `employee_id`    | bigint PK    | Auto increment                     |
| `employee_name`  | varchar(150) | Required                           |
| `email_id`       | varchar(150) | Required · Unique                  |
| `contact_number` | varchar(15)  | Required                           |
| `role_id`        | bigint FK    | References `role_masters.role_id`  |
| `designation`    | varchar(100) | Nullable                           |
| `department`     | varchar(100) | Nullable                           |
| `address`        | text         | Nullable                           |
| `profile_image`  | varchar      | Image path (nullable)              |
| `status_id`      | tinyint      | 1=Active 0=Inactive · Default 1    |
| `created_by`     | bigint       | Nullable                           |
| `deleted_at`     | timestamp    | Soft delete                        |

### `category_masters` Columns

| Column          | Type         | Notes                                      |
|-----------------|--------------|--------------------------------------------|
| `category_id`   | bigint PK    | Auto increment                             |
| `category_name` | varchar(100) | Required (e.g. Veg, Non-Veg, Starters)     |
| `client_id`     | bigint FK    | Nullable — null means global (all clients) |
| `status_id`     | tinyint      | 1=Active 0=Inactive · Default 1            |
| `created_by`    | bigint       | Nullable                                   |
| `deleted_at`    | timestamp    | Soft delete                                |

### `table_type_masters` Columns

| Column          | Type         | Notes                                      |
|-----------------|--------------|--------------------------------------------|
| `table_type_id` | bigint PK    | Auto increment                             |
| `type_name`     | varchar(100) | Required (e.g. Indoor, Outdoor, Rooftop)   |
| `client_id`     | bigint FK    | Nullable — null means global               |
| `status_id`     | tinyint      | 1=Active 0=Inactive · Default 1            |
| `created_by`    | bigint       | Nullable                                   |
| `deleted_at`    | timestamp    | Soft delete                                |

### `table_masters` Columns

| Column          | Type              | Notes                                    |
|-----------------|-------------------|------------------------------------------|
| `table_id`      | bigint PK         | Auto increment                           |
| `table_name`    | varchar(50)       | Required (e.g. T1, T2, A, B, 1, 2)      |
| `table_type_id` | bigint FK         | References `table_type_masters`          |
| `client_id`     | bigint FK         | References `client_masters`              |
| `capacity`      | tinyint unsigned  | Nullable (seating capacity)              |
| `status_id`     | tinyint           | 1=Available 0=Inactive · Default 1       |
| `created_by`    | bigint            | Nullable                                 |
| `deleted_at`    | timestamp         | Soft delete                              |

### `menu_masters` Columns

| Column           | Type          | Notes                                        |
|------------------|---------------|----------------------------------------------|
| `menu_id`        | bigint PK     | Auto increment                               |
| `menu_name`      | varchar(150)  | Required                                     |
| `category_id`    | bigint FK     | References `category_masters`                |
| `food_type`      | tinyint       | 1=Veg · 2=Non-Veg                            |
| `price`          | decimal(10,2) | Required                                     |
| `gst_percentage` | decimal(5,2)  | Default 0                                    |
| `stock_type`     | varchar(10)   | Unit or Kg                                   |
| `quantity`       | decimal(10,2) | Nullable                                     |
| `image`          | varchar       | Image path (nullable)                        |
| `client_id`      | bigint FK     | References `client_masters`                  |
| `status_id`      | tinyint       | 1=Active 0=Inactive · Default 1              |
| `created_by`     | bigint        | Nullable                                     |
| `deleted_at`     | timestamp     | Soft delete                                  |

---

## Registered Routes

### Auth Routes
| Method | URI       | Name         | Action                      |
|--------|-----------|--------------|-----------------------------|
| GET    | `/login`  | `login`      | `AuthController@showLogin`  |
| POST   | `/login`  | `login.post` | `AuthController@login`      |
| POST   | `/logout` | `logout`     | `AuthController@logout`     |

### Protected Routes (auth middleware)
| Method | URI                            | Name                        | Controller                          |
|--------|--------------------------------|-----------------------------|-------------------------------------|
| GET    | `/dashboard`                   | `dashboard`                 | `HomeController@index`              |
| GET    | `/clients`                     | `clients.index`             | `ClientMasterController@index`      |
| GET    | `/clients/add`                 | `clients.create`            | `ClientMasterController@create`     |
| POST   | `/clients/add`                 | `clients.store`             | `ClientMasterController@store`      |
| GET    | `/clients/allData`             | `clients.allData`           | `ClientMasterController@allData`    |
| POST   | `/clients/update-status`       | `clients.updateStatus`      | `ClientMasterController@updateStatus`|
| GET    | `/clients/{id}`                | `clients.edit`              | `ClientMasterController@edit`       |
| POST   | `/clients/{id}`                | `clients.update`            | `ClientMasterController@update`     |
| POST   | `/clients/{id}/delete`         | `clients.destroy`           | `ClientMasterController@destroy`    |
| GET    | `/roles`                       | `roles.index`               | `RoleMasterController@index`        |
| GET    | `/roles/add`                   | `roles.create`              | `RoleMasterController@create`       |
| POST   | `/roles/add`                   | `roles.store`               | `RoleMasterController@store`        |
| GET    | `/roles/allData`               | `roles.allData`             | `RoleMasterController@allData`      |
| POST   | `/roles/update-status`         | `roles.updateStatus`        | `RoleMasterController@updateStatus` |
| GET    | `/roles/{id}`                  | `roles.edit`                | `RoleMasterController@edit`         |
| POST   | `/roles/{id}`                  | `roles.update`              | `RoleMasterController@update`       |
| POST   | `/roles/{id}/delete`           | `roles.destroy`             | `RoleMasterController@destroy`      |
| GET    | `/permissions`                 | `permissions.index`         | `PermissionMasterController@index`  |
| GET    | `/permissions/{roleId}`        | `permissions.edit`          | `PermissionMasterController@edit`   |
| POST   | `/permissions/{roleId}`        | `permissions.update`        | `PermissionMasterController@update` |
| GET    | `/employees`                   | `employees.index`           | `EmployeeMasterController@index`    |
| GET    | `/employees/add`               | `employees.create`          | `EmployeeMasterController@create`   |
| POST   | `/employees/add`               | `employees.store`           | `EmployeeMasterController@store`    |
| GET    | `/employees/allData`           | `employees.allData`         | `EmployeeMasterController@allData`  |
| POST   | `/employees/update-status`     | `employees.updateStatus`    | `EmployeeMasterController@updateStatus`|
| GET    | `/employees/{id}`              | `employees.edit`            | `EmployeeMasterController@edit`     |
| POST   | `/employees/{id}`              | `employees.update`          | `EmployeeMasterController@update`   |
| POST   | `/employees/{id}/delete`       | `employees.destroy`         | `EmployeeMasterController@destroy`  |
| GET    | `/categories`                  | `categories.index`          | `CategoryMasterController@index`    |
| GET    | `/categories/add`              | `categories.create`         | `CategoryMasterController@create`   |
| POST   | `/categories/add`              | `categories.store`          | `CategoryMasterController@store`    |
| GET    | `/categories/allData`          | `categories.allData`        | `CategoryMasterController@allData`  |
| POST   | `/categories/update-status`    | `categories.updateStatus`   | `CategoryMasterController@updateStatus`|
| GET    | `/categories/{id}`             | `categories.edit`           | `CategoryMasterController@edit`     |
| POST   | `/categories/{id}`             | `categories.update`         | `CategoryMasterController@update`   |
| POST   | `/categories/{id}/delete`      | `categories.destroy`        | `CategoryMasterController@destroy`  |
| GET    | `/table-types`                 | `table-types.index`         | `TableTypeMasterController@index`   |
| GET    | `/table-types/add`             | `table-types.create`        | `TableTypeMasterController@create`  |
| POST   | `/table-types/add`             | `table-types.store`         | `TableTypeMasterController@store`   |
| GET    | `/table-types/allData`         | `table-types.allData`       | `TableTypeMasterController@allData` |
| POST   | `/table-types/update-status`   | `table-types.updateStatus`  | `TableTypeMasterController@updateStatus`|
| GET    | `/table-types/{id}`            | `table-types.edit`          | `TableTypeMasterController@edit`    |
| POST   | `/table-types/{id}`            | `table-types.update`        | `TableTypeMasterController@update`  |
| POST   | `/table-types/{id}/delete`     | `table-types.destroy`       | `TableTypeMasterController@destroy` |
| GET    | `/tables`                      | `tables.index`              | `TableMasterController@index`       |
| GET    | `/tables/add`                  | `tables.create`             | `TableMasterController@create`      |
| POST   | `/tables/add`                  | `tables.store`              | `TableMasterController@store`       |
| GET    | `/tables/allData`              | `tables.allData`            | `TableMasterController@allData`     |
| POST   | `/tables/update-status`        | `tables.updateStatus`       | `TableMasterController@updateStatus`|
| GET    | `/tables/{id}`                 | `tables.edit`               | `TableMasterController@edit`        |
| POST   | `/tables/{id}`                 | `tables.update`             | `TableMasterController@update`      |
| POST   | `/tables/{id}/delete`          | `tables.destroy`            | `TableMasterController@destroy`     |
| GET    | `/menus`                       | `menus.index`               | `MenuMasterController@index`        |
| GET    | `/menus/add`                   | `menus.create`              | `MenuMasterController@create`       |
| POST   | `/menus/add`                   | `menus.store`               | `MenuMasterController@store`        |
| GET    | `/menus/allData`               | `menus.allData`             | `MenuMasterController@allData`      |
| POST   | `/menus/update-status`         | `menus.updateStatus`        | `MenuMasterController@updateStatus` |
| GET    | `/menus/{id}`                  | `menus.edit`                | `MenuMasterController@edit`         |
| POST   | `/menus/{id}`                  | `menus.update`              | `MenuMasterController@update`       |
| POST   | `/menus/{id}/delete`           | `menus.destroy`             | `MenuMasterController@destroy`      |

---

## Permission Modules (31 total)

```
Dashboard              Client Registration     Role Master
Permission Master      Products                Categories
Sub Categories         Brands                  Units
Variant Attributes     Warranties              Manage Stock
Stock Adjustment       Stock Transfer          Sales
Purchase Orders        Purchase Returns        Expenses
Income                 Bank Accounts           Customers
Suppliers              Employees               Departments
Payroll                Reports                 Users
Settings               Table Type Master       Table Master
Menu Master
```

> **Admin rule:** `role_id = 1` always has full access. Permission matrix is read-only for Admin.
> All other roles are fully configured by Admin via the Permission Master screen.
> **Menu visibility:** `$userPermissions` (keyed by module name) is shared globally via `AppServiceProvider`.
> Each menu section checks `can_view`, and Add links check `can_add` before rendering.

---

## Default Login Credentials

| Role       | Email                   | Password       |
|------------|-------------------------|----------------|
| Admin      | admin@hotelhub.com      | admin@123      |
| Supervisor | nisargnavale@gmail.com  | supervisor@123 |

---

## Development Task Log

---

### Task 1 — Company Admin Dashboard Redesign
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**
- Redesigned `resources/views/dashboard.blade.php` for the **Company Admin** (Host of Hosts) role
- Replaced all POS/inventory template labels with HotelHub hospitality terminology
- No layout, charts, or design structure was removed

**Key label changes:**

| Old Label             | New Label                  |
|-----------------------|----------------------------|
| Welcome, Admin        | Welcome, Company Admin     |
| Total Sales           | Total Platform Revenue     |
| Total Sales Return    | Total Refunds              |
| Total Purchase        | Total Subscriptions        |
| Total Purchase Return | Subscription Cancellations |
| Profit                | Net Platform Profit        |
| Invoice Due           | Pending Hotel Invoices     |
| Top Selling Products  | Top Performing Hotels      |
| Low Stock Products    | Expiring Subscriptions     |
| Recent Sales          | Recent Hotel Orders        |
| Sales Statics         | Revenue Statistics         |
| Top Customers         | Top Hotel Clients          |
| Top Categories        | Top Hotel Services         |
| Suppliers             | Hotel Clients              |
| Customer              | Restaurant Guests          |
| Orders                | Active KOTs                |

---

### Task 2 — Client Master Table, Migration, Model & Controller
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**
- Created migration `create_client_masters_table.php` with all required columns
- Created `ClientMaster` model (SoftDeletes, `getStatusLabelAttribute`)
- Created `ClientMasterController` with full CRUD + file upload handling
- Added initial resource route in `web.php`

---

### Task 3 — Database Rename to `hotelhub`
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**
- Updated `.env` → `DB_DATABASE=hotelhub`
- Created `hotelhub` database in MySQL via XAMPP CLI
- Ran `php artisan migrate` — all tables created fresh in `hotelhub` DB

---

### Task 4 — Client Master CRUD Blade Views & Routes
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**
- Created `resources/views/clients/all.blade.php`
  - DataTable with AJAX from `/clients/allData`
  - Status toggle (Active / Inactive / Suspended / Trial) via AJAX
  - Delete row via AJAX with confirmation
  - Excel export
- Created `resources/views/clients/add.blade.php`
  - Accordion form with 4 sections: Basic Info, Address & Location, Profile Image, Aadhar Image
  - Client-side image preview using FileReader API
- Created `resources/views/clients/edit.blade.php`
  - Pre-filled from `$client` model
  - Shows existing images with option to replace
- Replaced `Route::resource()` with 8 explicit prefix routes
- Updated `left-menu.blade.php` — added **Client Management** section with submenu

---

### Task 5 — Role Master & Permission Master
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**

**Migrations:**
- `create_role_masters_table.php` — seeds Admin role (role_id=1) automatically
- `create_permission_masters_table.php` — FK cascade, unique constraint on (role_id, menu_name)

**Models:**
- `RoleMaster.php` — SoftDeletes, `permissions()` hasMany, `isAdmin()` helper
- `PermissionMaster.php` — `role()` belongsTo

**Controllers:**
- `RoleMasterController.php` — Full CRUD; Admin protected from delete/deactivation
- `PermissionMasterController.php` — 31 modules as single source of truth; bulk delete-and-reinsert on save

**Blade Views:**
- `roles/all.blade.php` — DataTable + status toggle + delete + Assign Permissions shortcut button
- `roles/add.blade.php` — Accordion form (role name, status, description)
- `roles/edit.blade.php` — Pre-filled; Admin fields readonly/disabled + info banner
- `permissions/all.blade.php` — Lists all roles with Assign Permissions / View Permissions buttons
- `permissions/edit.blade.php` — Full permission matrix (31 rows × 4 columns) with Select All / Clear All

**Routes:** 8 routes under `/roles`, 3 routes under `/permissions`

**Left Menu:** Added **User Management** section (Role Master submenu + Permission Master direct link)

**Business rules:**
- Admin always has full access — cannot be changed
- Admin role cannot be deleted or deactivated
- Non-admin roles: admin sets View / Add / Edit / Delete per module
- Save replaces all permissions for the role in one transaction

---

### Task 6 — README Documentation
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**
- Created this `README.md` with full project overview, database schema, route table, module list, and complete development task log

---

### Task 7 — Login / Logout & Employee Master
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**

**Authentication:**
- Created `AuthController` with `showLogin()`, `login()`, `logout()` using Laravel's `Auth::attempt()`
- Created `resources/views/auth/login.blade.php` — converted from `template/signin-3.html` to Blade
- Guest middleware on login routes; `auth` middleware wraps all protected routes
- Root `/` redirects to dashboard (or login if unauthenticated)
- Logout button in header dropdown via POST form (CSRF-safe)
- Header shows logged-in user's name and role dynamically

**Migrations:**
- `add_role_status_to_users_table` — adds `role_id` (FK → role_masters) and `status_id` to `users`
- `create_employee_masters_table` — full employee columns with softDeletes
- `seed_admin_user` — inserts default admin: `admin@hotelhub.com` / `admin@123`

**Models:**
- `User.php` — added role_id, status_id to fillable; `role()` belongsTo; `isAdmin()` helper
- `EmployeeMaster.php` — SoftDeletes, `role()` belongsTo, `getStatusLabelAttribute()`

**Controllers:**
- `EmployeeMasterController.php` — Full CRUD; creating an employee also creates a `users` record (login account) with the submitted password

**Blade Views:**
- `employees/all.blade.php` — DataTable with AJAX, status badge toggle, edit/delete actions
- `employees/add.blade.php` — Accordion form with password + confirm password fields
- `employees/edit.blade.php` — Pre-filled; shows existing profile image with replace option

**Routes:** 8 routes under `/employees`

**Left Menu:** Added **Employee Management** section

---

### Task 8 — Permission-Based Menu Visibility
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**
- Updated `AppServiceProvider` to share two global view variables on every request:
  - `$isAdmin` — `true` when `role_id = 1`
  - `$userPermissions` — collection of full permission rows keyed by `menu_name`
- Updated `left-menu.blade.php` — each menu section and Add/Edit/Delete links are wrapped in `@if` checks
- Updated `clients/all.blade.php` and `employees/all.blade.php` — page-level Add buttons and DataTable action buttons also respect `can_add`, `can_edit`, `can_delete`

**Permission check pattern used:**
```blade
{{-- Show section --}}
@if($isAdmin || ($userPermissions->has('Module') && $userPermissions->get('Module')->can_view))

{{-- Show Add button --}}
@if($isAdmin || ($userPermissions->has('Module') && $userPermissions->get('Module')->can_add))
```

**Modules gated in left menu:**
- Dashboard · Client Registration · Employees · Role Master · Permission Master
- Categories · Table Type Master · Table Master · Menu Master

---

### Task 9 — Restaurant Masters (Category, Table Type, Table, Menu)
**Done By:** 🤖 Claude AI
**Date:** 2026-04-01

**What was done:**

**Migrations (4):**
- `create_category_masters_table` — food categories, optional client_id (null = global)
- `create_table_type_masters_table` — table types (Indoor, Outdoor, Rooftop), optional client_id
- `create_table_masters_table` — individual tables with table_type_id FK, client_id FK, capacity
- `create_menu_masters_table` — menu items with category, food_type, price, gst, stock_type, quantity, image, client_id

**Models (4):** `CategoryMaster`, `TableTypeMaster`, `TableMaster`, `MenuMaster`
- All use SoftDeletes
- All have `client()` belongsTo relationship
- `MenuMaster` has `getFoodTypeLabelAttribute()` (1=Veg, 2=Non-Veg)
- `TableTypeMaster` has `tables()` hasMany

**Controllers (4):** Full CRUD for each — `allData()` returns JSON for DataTable, image upload for Menu

**Blade Views (12):** `all`, `add`, `edit` for each master — all permission-gated

**Routes:** 8 routes each under `/categories`, `/table-types`, `/tables`, `/menus`

**Left Menu:** Added **Restaurant Management** section with all 4 submenus

**Permission Modules added:** `Table Type Master`, `Table Master`, `Menu Master` (total now 31)

**Design decisions:**
- Category & Table Type: `client_id` nullable — blank = global (shared across all clients)
- Table & Menu: `client_id` required — always client-specific
- Table status: `1=Available`, `0=Inactive`
- Menu images stored at `storage/app/public/menus/`

**Seed data inserted:**
- 11 tables for Navale Restro / Indoor type (T1–T6 capacity 4, T7–T11 capacity 6)
- 20 menu items (10 Veg + 10 Non-Veg) with placeholder images from template assets

---

### Task 10 — Dashboard Currency Symbol Update
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Replaced all `$` currency symbols with `₹` in `dashboard.blade.php`
- 52 occurrences updated across stat cards, tables, and chart labels
- PHP/Blade `$` variables were preserved (only `$` followed by digits were replaced)

---

### Task 11 — Left Menu Cleanup
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Removed all unused template sections from `left-menu.blade.php`:
  - Super Admin, Application, Layouts, Inventory, Stock, Sales, Promo, Purchases
  - Finance & Accounts, Peoples, HRM, Reports, Content (CMS), Pages, Settings, UI Interface
- Kept only the 5 HotelHub-relevant sections:
  - **Main** — Dashboard
  - **Restaurant Management** — Category, Table Type, Table, Menu Masters
  - **Client Management** — Client Registration
  - **Employee Management** — Employee Master
  - **User Management** — Role Master, Permission Master
- Replaced hardcoded "Adrian Herman" sidebar profile with `Auth::user()->name` and `Auth::user()->role->role_name`

---

### Task 12 — Client Subscription Management
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**

**Migration:**
- `add_subscription_to_client_masters` — adds 4 columns to `client_masters`:
  `subscription_type`, `subscription_price`, `subscription_start_date`, `subscription_end_date`

**Model (`ClientMaster.php`):**
- Added 4 fields to `$fillable`
- Added date casts for `subscription_start_date` and `subscription_end_date`
- Added `getSubscriptionStatusAttribute()` — returns `None` / `Active` / `Expired`

**Controller (`ClientMasterController.php`):**
- `store()` and `update()` validate and save subscription fields
- `calcEndDate()` private method auto-computes end date:
  - Monthly → start + 1 month
  - Quarterly → start + 3 months
  - Yearly → start + 1 year
- `allData()` returns formatted subscription fields including computed `subscription_status`

**Blade Views:**
- `clients/add.blade.php` — new **Subscription Details** accordion (type, price ₹, start date, read-only end date preview that updates live via JS)
- `clients/edit.blade.php` — same accordion pre-filled from existing record
- `clients/all.blade.php` — 4 new DataTable columns: Subscription (badge), Price (₹), Valid Till, Sub. Status (Active/Expired badge)

**Business rules:**
- Subscription is optional — clients with no subscription type show `-`
- Admin sets the price freely per client (no fixed plan table)
- End date is always auto-calculated; it cannot be manually overridden

---

### Task 13 — SFTP Deployment Setup
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Created `ftp-config.php` — stores SFTP credentials (host, port, user, password, remote path)
- Created `deploy-ftp.php` — deployment script using `phpseclib/phpseclib` (SFTP over SSH port 22)
- Installed `phpseclib/phpseclib:~3.0` via composer
- Deployed all 13,157 project files to production server

**Production server details:**
- Host: `103.174.103.63` (port 22 SFTP)
- Remote path: `/var/www/b0f61805-21c5-4340-86e4-38984a620615/bitrootinnovations.com/hotelhub/`
- Live URL: `https://bitrootinnovations.com/hotelhub`

**To deploy updates:**
```bash
php deploy-ftp.php
```

**Files excluded from deploy:** `vendor/` (if already on server), `.env`, `ftp-config.php`, `deploy-ftp.php`, `.git`

---

### Task 14 — Production Server Configuration
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Created `.env` on server with production DB credentials and `APP_URL=https://bitrootinnovations.com/hotelhub`
- Updated `AppServiceProvider.php` on server — `base_url` set to production URL
- Created root `.htaccess` — routes all requests to root `index.php` (LiteSpeed-compatible)
  - Static assets served from `public/` directory when files exist there
  - PHP routes handled by root `index.php` (correct SCRIPT_NAME for Laravel path resolution)
- Updated `public/.htaccess` on server — redirects `/hotelhub/public/*` → `/hotelhub/*` (301)
- Result: app runs cleanly at `https://bitrootinnovations.com/hotelhub/login` with no `/public/` in URL

**Key fix:** Routing through root `index.php` (not `public/index.php`) so Laravel's Symfony Request computes `pathInfo = /login` correctly from `SCRIPT_NAME = /hotelhub/index.php`

---

## Quick Start

```bash
# 1. Install dependencies
composer install

# 2. Configure environment
cp .env.example .env
# Edit .env — set DB_DATABASE=hotelhub, DB_USERNAME=root, DB_PASSWORD=

# 3. Generate app key
php artisan key:generate

# 4. Create database
mysql -u root -e "CREATE DATABASE hotelhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations
php artisan migrate

# 6. Create storage symlink
php artisan storage:link

# 7. Open in browser
# http://localhost/hotelhub/login
```

---

## Quick URLs

### Local
| Page               | URL                                      |
|--------------------|------------------------------------------|
| Login              | `http://localhost/hotelhub/login`        |
| Dashboard          | `http://localhost/hotelhub/dashboard`    |
| All Clients        | `http://localhost/hotelhub/clients`      |
| All Roles          | `http://localhost/hotelhub/roles`        |
| Permission Master  | `http://localhost/hotelhub/permissions`  |
| All Employees      | `http://localhost/hotelhub/employees`    |
| All Categories     | `http://localhost/hotelhub/categories`   |
| All Table Types    | `http://localhost/hotelhub/table-types`  |
| All Tables         | `http://localhost/hotelhub/tables`       |
| All Menu Items     | `http://localhost/hotelhub/menus`        |

### Production
| Page               | URL                                                          |
|--------------------|--------------------------------------------------------------|
| Login              | `https://bitrootinnovations.com/hotelhub/login`              |
| Dashboard          | `https://bitrootinnovations.com/hotelhub/dashboard`          |
| All Clients        | `https://bitrootinnovations.com/hotelhub/clients`            |
| All Roles          | `https://bitrootinnovations.com/hotelhub/roles`              |
| Permission Master  | `https://bitrootinnovations.com/hotelhub/permissions`        |
| All Employees      | `https://bitrootinnovations.com/hotelhub/employees`          |
| All Categories     | `https://bitrootinnovations.com/hotelhub/categories`         |
| All Table Types    | `https://bitrootinnovations.com/hotelhub/table-types`        |
| All Tables         | `https://bitrootinnovations.com/hotelhub/tables`             |
| All Menu Items     | `https://bitrootinnovations.com/hotelhub/menus`              |

---

### Task 15 — React Native Mobile API (JWT)
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Installed `tymon/jwt-auth:^1.0` — JWT authentication for Laravel
- Updated `User` model to implement `JWTSubject` (embeds `client_id`, `role_id`, `name` in token)
- Created `JwtMiddleware` — validates token, returns clean 401 JSON on failure
- Registered `jwt.auth` middleware alias in `Kernel.php`
- Added `api` guard (`driver: jwt`) in `config/auth.php`

**New Migrations (all ran on local + production):**
- `add_client_id_to_users_table` — links users to a client; indexed
- `create_orders_table` — order header with composite indexes on `(client_id, status)`, `created_at`
- `create_order_items_table` — line items with FK cascade from orders
- `add_performance_indexes` — composite indexes on all client-wise master tables for 100+ concurrent users

**New Models:** `Order`, `OrderItem`

**API Controllers (all under `app/Http/Controllers/Api/`):**
- `BaseApiController` — `success()`, `error()`, `paginated()` response helpers
- `AuthController` — login, logout, refresh token
- `ProfileController` — GET + PUT profile with client subscription details
- `CategoryController` — client-scoped + global categories
- `TableTypeController` — client-scoped + global table types
- `TableController` — filterable by table_type_id
- `MenuController` — paginated, filterable by category/food_type/search
- `OrderController` — list, detail, submit (bulk insert, single query for menu validation)

**Routes (`routes/api.php`):**
- Public: `POST /api/v1/auth/login`
- Protected (JWT): profile, categories, table-types, tables, menu, orders
- Throttle: 60 req/min public · 120 req/min authenticated

**Performance optimisations:**
- Composite DB indexes on all `(client_id, status_id)` pairs
- Eager loading with `select()` — no N+1 queries
- Bulk `insert()` for order items (one query regardless of item count)
- All menu IDs validated in a single `whereIn` query before order creation
- Pagination capped at 50 items/page

**Deployed & tested live:**
- `POST https://bitrootinnovations.com/hotelhub/api/v1/auth/login` → ✅ 200 OK with JWT token
- `php artisan migrate --force` ran successfully on production

**Documentation:** `README-mobile-api.md` created with full API reference for React Native developer

---

---

### Task 16 — Client Role & Employee Management APIs
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Created `ClientRole` model — client-defined roles (Waiter, Captain, Manager, etc.)
- Created `ClientEmployee` model — implements `JWTSubject`; `getJWTIdentifier()` returns `'employee_' . id` to avoid collisions with ClientMaster tokens
- Updated `ClientMaster` — implements `JWTSubject`; `getJWTIdentifier()` returns `'client_' . client_id`
- Updated `MobileAuthMiddleware` — resolves `ClientMaster` or `ClientEmployee` from JWT `user_type` claim; sets `mobile_user`, `client_id`, `user_type` on request attributes
- Unified login endpoint — `login_type: "client"` or `"employee"` in request body
- Created `ClientRoleController` — CRUD for client-defined roles
- Created `ClientEmployeeController` — CRUD for restaurant employees; client-scoped
- Seeded 2 static roles per client: **Manager** and **Waiter**

**Migrations:**
- `create_client_roles_table` — role_name, client_id, status_id, soft delete
- `create_client_employees_table` — name, email, password, phone, client_role_id FK, client_id FK, indexes

---

### Task 17 — Mobile API Bug Fixes
**Done By:** 🤖 Claude AI
**Date:** 2026-04-02

**What was done:**
- Fixed all API controllers that used `JWTAuth::user()->client_id` (returned null for ClientMaster) — replaced with `$request->attributes->get('client_id')` from MobileAuthMiddleware
- Fixed `MenuController::show()` method signature to accept `(Request $request, int $id)` instead of just `int $id`
- Fixed client login "Invalid email or password": changed `status_id` from 4 (Trial) to 1 (Active) in DB for test client; re-hashed password using `Hash::make()` (was hashed with raw PHP `password_hash()`)
- Fixed `TableTypeController` and `TableController` to use request attributes

---

### Task 18 — Order, Cart, Checkout & Table Status APIs
**Done By:** 🤖 Claude AI
**Date:** 2026-04-03

**What was done:**

**New Migrations (4):**
- `2026_04_03_000001_add_payment_to_orders_table` — adds `payment_type` (enum: Cash/UPI/Card/Online/Other), `payment_status` (enum: paid/unpaid), `checked_out_at` (timestamp)
- `2026_04_03_000002_create_carts_table` — `cart_id`, `client_id`, `table_id`, `user_ref` (e.g. `client_1` or `employee_3`), `order_type`; index on `(client_id, user_ref)`
- `2026_04_03_000003_create_cart_items_table` — unique on `(cart_id, menu_id)`; FK cascade from carts
- `2026_04_03_000004_add_permissions_to_client_roles` — adds 5 boolean columns: `can_take_orders`, `can_checkout`, `can_manage_menu`, `can_manage_employees`, `can_view_reports`; sets Manager defaults

**New Models:** `Cart`, `CartItem` — Cart has `forUser()` static helper and `getTotal()` method

**New API Controllers:**
- `TableStatusController` — `GET /tables/status` — returns all tables with color-coded status (`#4CAF50` free · `#FF9800` pending · `#F44336` occupied · `#2196F3` served)
- `CartController` — view, add item (upsert), remove item, clear cart
- `CheckoutController` — `POST /checkout` converts cart → paid Order; `POST /checkout/order/{id}` marks existing order as paid
- `OrderTableController` — `GET /tables/{id}/orders` — active orders + items + grand total for a table
- `OrderReportController` — `GET /reports/orders` — date range filter, summary stats (revenue, paid, cancelled)
- `MobilePermissionsController` — `GET /permissions` (my permissions); `GET|PUT /client/roles/{id}/permissions`

**`MenuController`** — added `PUT /menu/{id}` for editing menu item details and image

**Routes (`routes/api.php`) — 14 new endpoints added:**
```
GET  /tables/status
GET  /tables/{id}/orders
GET  /cart
POST /cart/items
DELETE /cart/items/{menu_id}
DELETE /cart
POST /checkout
POST /checkout/order/{id}
GET  /reports/orders
GET  /permissions
GET  /client/roles/{id}/permissions
PUT  /client/roles/{id}/permissions
PUT  /menu/{id}
```

**Deployed:** `deploy-partial.php` script uploads only changed files (skips vendor/); migrations ran on production via HTTP trigger.

---

### Task 19 — Partial Deploy Script
**Done By:** 🤖 Claude AI
**Date:** 2026-04-03

**What was done:**
- Created `deploy-partial.php` — uploads only specific changed/new files via SFTP (avoids re-uploading entire vendor directory which caused connection timeouts)
- Created `deploy-migrate.php` — uploads a temporary PHP migration runner to `/public/`, triggers it via HTTP, then deletes it — allows running `php artisan migrate --force` on the server without SSH access
- Both helper scripts are excluded from main deploy and not committed to git

---

### Task 20 — README & Mobile API Docs Update
**Done By:** 🤖 Claude AI
**Date:** 2026-04-03

**What was done:**
- Updated `README-mobile-api.md` — added 14 new API sections (Table Status, Cart, Checkout, Order Report, Permissions, Edit Menu) bringing total documented endpoints from 22 to 35
- Updated Complete API List table with all 35 endpoints and access levels
- Updated `README.md` — added new DB tables, updated project structure with all new models/controllers/middleware, added Task 16–20 development log entries

---

---

### Task 21 — Dashboard Fixes (Images, Menu Names, Client Logo)
**Done By:** 🤖 Claude AI
**Date:** 2026-04-17

**What was done:**
- Fixed all 5 image path occurrences in `dashboard.blade.php` — changed from `/storage/` to `/storage/app/public/` to match server disk layout
- Made `$base_url` dynamic — reads from `config('app.url')` via `AppServiceProvider` so it no longer gets overwritten on every deploy
- Added menu names display in Recent Orders and Transactions table — shows first 2 item names with `+N more` for larger orders
- Fixed missing client logo in Top Hotels list — corrected image path pattern

**Key change in `AppServiceProvider`:**
```php
$base_url = rtrim(config('app.url'), '/');
```

---

### Task 22 — Client Portal (Dashboard, Orders, Menus, Employees, Reports)
**Done By:** 🤖 Claude AI
**Date:** 2026-04-17

**What was done:**

**New Controllers (`app/Http/Controllers/Client/`):**
- `DashboardController` — today's orders, revenue, active KOTs, total menu items, live table occupancy grid, recent 10 orders
- `MenuReportController` — menu-wise sales (qty + revenue per item), top selling list
- `ClientEmployeeController` — full CRUD for restaurant staff scoped to logged-in client

**New Middleware:**
- `ClientAccess` — registered as `client.access`; checks auth, checks `client_id` is set, checks `plan_type = Premium`; logs out and redirects with error on failure

**New Views (`resources/views/client/`):**
- `dashboard.blade.php` — 4 stat cards, live table grid (color-coded: green=available, red=occupied, grey=inactive), recent orders table
- `menu-report.blade.php` — date filter, 3 summary cards, DataTable, Top 10 selling list

**Scoped Controllers (Admin controllers updated for client users):**
- `CategoryMasterController` — `allData`, `store`, `update` automatically scope to `client_id`
- `MenuMasterController` — same scoping pattern
- `OrderReportController` — forces `client_id` filter; hides client dropdown in view

**Blade Views updated:**
- `menus/add.blade.php` and `edit.blade.php` — client dropdown hidden for client users, replaced with hidden input; JS fixed to work with both `<select>` and `<input>`
- `categories/add.blade.php` and `edit.blade.php` — client dropdown wrapped with `@if(!$isClientUser)`

**Routes (`routes/web.php`):**
- `/client/*` routes added under `auth` + `client.access` middleware group
- Client portal: dashboard, category master, menu master, employee master, order report, menu-wise report

**Left Menu (`left-menu.blade.php`):**
- "Main" section hidden for client users
- All admin sections (Client Management, Employee Management, Reports, User Management, Restaurant Management) hidden via `@if(!$isClientUser)`
- New **Client Portal** section shown only for `@if($isClientUser)`: Dashboard, Categories, Menu Master, Employees, Order Report, Menu-wise Report

**Header (`header.blade.php`):**
- Search, store select, Add New, POS, flag, email, notifications, settings — all hidden for client users
- Map pin icon — shown for admin only
- Client logout: right-aligned with `ms-auto`, shows user avatar initial + name + styled logout button

---

### Task 23 — Two-Tier Client Plan System (Basic / Premium)
**Done By:** 🤖 Claude AI
**Date:** 2026-04-17

**What was done:**

**Migration:**
- `2026_04_17_000001_add_plan_type_to_client_masters` — adds `plan_type` enum (`Basic`/`Premium`) defaulting to `Basic` after `subscription_end_date`

**Model (`ClientMaster.php`):**
- Added `plan_type` to `$fillable`

**Controller (`ClientMasterController.php`):**
- Added `plan_type` validation (`nullable|in:Basic,Premium`) in `store()` and `update()`
- Included `plan_type` in `$request->only()` for both methods
- Added `plan_type` to `allData()` response (defaults to `'Basic'` if null)

**Auth (`AuthController.php`):**
- On login: if `client_id` is set, checks `plan_type === 'Premium'`; if Basic, logs out and returns error: *"Web portal access is available for Premium plan clients only. Please contact your administrator."*
- Premium clients redirect to `client.dashboard`

**Middleware (`ClientAccess.php`):**
- Same Premium check on every `/client/*` request — defence in depth
- Basic plan clients are logged out and redirected to login with error

**Views:**
- `clients/add.blade.php` — Plan Type select added in Subscription Details accordion (Basic default, Premium option with helper text)
- `clients/edit.blade.php` — same select pre-filled from `$client->plan_type`

**Data update:**
- Navale Restro (`client_id=1`) updated to `plan_type = Premium` on production server

| Plan | Mobile App | Web Portal |
|---|---|---|
| Basic | ✅ | ❌ |
| Premium | ✅ | ✅ |

---

### Task 24 — GitHub Repository Setup & README
**Done By:** 🤖 Claude AI
**Date:** 2026-04-17

**What was done:**
- Initialized git repository and pushed entire codebase to `https://github.com/bitrootinnovations/hotelhub.git`
- Added `ftp-config.php` to `.gitignore` (contains server credentials)
- Created and maintained this `README.md` with full project documentation
- Subsequent feature commits pushed to `master` branch

**Commit history:**
| Commit | Description |
|---|---|
| `cd7c0e9` | Initial commit — HotelHub production codebase |
| `b596da1` | Add two-tier client plan system (Basic / Premium) |

---

*Last updated: 2026-04-17 — Tasks 1–24 complete · Live at https://bitrootinnovations.com/hotelhub*
