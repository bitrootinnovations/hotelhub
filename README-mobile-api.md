# HotelHub Mobile API — React Native Developer Handoff Guide

> **Handoff document for the React Native developer.**
> Backend is complete and live. All 35 API endpoints are tested and deployed.
> Base URL: `https://bitrootinnovations.com/hotelhub/api/v1`

---

## What Has Been Built (Backend Summary)

| # | Feature | Status |
|---|---------|--------|
| 1 | Client & Employee JWT Login / Logout / Refresh | ✅ Done |
| 2 | Profile view & update (client + employee) | ✅ Done |
| 3 | Categories API | ✅ Done |
| 4 | Table Types API | ✅ Done |
| 5 | Tables API (filterable by type) | ✅ Done |
| 6 | Table Status — busy/free with color codes | ✅ Done |
| 7 | Orders by Table — **current unpaid orders only** | ✅ Done |
| 8 | Menu API — paginated, search, filter | ✅ Done |
| 9 | Edit Menu Item API (with image upload) | ✅ Done |
| 10 | Submit Order (new order with items) | ✅ Done |
| 11 | View Orders (list + single) | ✅ Done |
| 12 | Cart — add item, view, remove, clear | ✅ Done |
| 13 | Checkout — cart → paid order (UPI/Cash/Card etc.) | ✅ Done |
| 14 | Checkout existing order — payment only | ✅ Done |
| 15 | Order Report — date filter + summary stats | ✅ Done |
| 16 | My Permissions API | ✅ Done |
| 17 | Role Permissions — view & update | ✅ Done |
| 18 | Client Role Management (CRUD) | ✅ Done |
| 19 | Client Employee Management (CRUD) | ✅ Done |
| 20 | Table auto-resets to Free after checkout | ✅ Done |
| 21 | Quick Order — Takeaway without table (list, place, update) | ✅ Done |

---

## User Hierarchy

```
HotelHub Admin / Supervisor  (web panel only — no mobile)
        │
        └── Client (Restaurant Owner)   ← logs in via mobile app
                │
                └── Client Employees (Waiter, Captain, Manager…)
                        └── registered & managed by Client from mobile app
```

- **Client** — restaurant owner; has all permissions by default
- **Employee** — waiter/manager; permissions set by client per role

---

## Base URLs

| Environment | Base URL |
|-------------|----------|
| Production  | `https://bitrootinnovations.com/hotelhub/api/v1` |
| Local Dev   | `http://localhost/hotelhub/api/v1` |

---

## Authentication

All protected routes require:
```
Authorization: Bearer <your_jwt_token>
Content-Type: application/json
```

- Token expires in **24 hours**
- Use `POST /auth/refresh` to get a new token before expiry
- On 401 response → redirect user to Login screen
- Store `user_type` from login response to control which screens to show

---

## Response Format

### Success
```json
{ "success": true, "message": "...", "data": {} }
```

### Paginated
```json
{
  "success": true, "message": "...",
  "data": [],
  "meta": { "total": 100, "per_page": 20, "current_page": 1, "last_page": 5 }
}
```

### Error
```json
{ "success": false, "message": "...", "errors": {} }
```

---

## Test Credentials

### Client Login (`login_type: "client"`)

| Client Name | Email | Password |
|-------------|-------|----------|
| Navale Restro | `navalerestro@gmail.com` | `navale@123` |

> **Note:** Client mobile password is set by Admin from the web panel →
> **Client Management → Edit Client → Mobile App Password**

### Employee Login (`login_type: "employee"`)

> Employees are registered by the Client from the mobile app via `POST /client/employees`.

### How to create a test employee (step-by-step):

1. Login as Client → get token
2. `POST /client/roles` → `{ "role_name": "Waiter" }` (or use existing seeded roles)
3. `POST /client/employees` →
```json
{
  "name": "Rahul Waiter",
  "email": "rahul@navalerestro.com",
  "password": "waiter@123",
  "password_confirmation": "waiter@123",
  "phone": "9876543210",
  "client_role_id": 1
}
```
4. Login as employee: `login_type: "employee"`, `email: "rahul@navalerestro.com"`, `password: "waiter@123"`

---

## React Native Setup (Axios)

```js
// src/api/api.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const BASE_URL = 'https://bitrootinnovations.com/hotelhub/api/v1';

const api = axios.create({ baseURL: BASE_URL });

// Attach token to every request
api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

// Auto-refresh on 401
api.interceptors.response.use(
  (res) => res,
  async (error) => {
    if (error.response?.status === 401) {
      try {
        const refresh = await api.post('/auth/refresh');
        const newToken = refresh.data.data.token;
        await AsyncStorage.setItem('token', newToken);
        error.config.headers.Authorization = `Bearer ${newToken}`;
        return api.request(error.config);
      } catch {
        await AsyncStorage.multiRemove(['token', 'user_type', 'user']);
        // Navigate to Login screen here
      }
    }
    return Promise.reject(error);
  }
);

export default api;
```

---

## Screen-by-Screen API Guide

> Use this as a reference while building each screen in React Native.

### Login Screen
```
POST /auth/login
Body: { email, password, login_type: "client" | "employee" }
Save to AsyncStorage: token, user_type, user (full object)
```

### Home / Dashboard Screen
```
GET /permissions          → show/hide menu items based on user's permissions
GET /tables/status        → show table grid with color codes
```

### Table Map / Floor Plan Screen
```
GET /tables/status        → color-coded table list
  Colors:
    #4CAF50  →  Free          (no active order, or order is paid)
    #FF9800  →  Order Placed  (pending — order placed, payment_status = pending)
    #F44336  →  Occupied      (confirmed/preparing, payment_status = pending)
    #2196F3  →  Served        (food served, payment_status = pending)

  NOTE: Table automatically returns to Free (green) after checkout (payment_status = paid).
        No manual reset required.
```

### Complete Order Flow (screen by screen)
```
1. User taps a FREE (green) table
   → GET /tables/status  → pick a table where table_status = "free"

2. Add items to cart
   → POST /cart/items  { menu_id, quantity, table_id, order_type }
   → GET /cart  → show cart with totals

3. Place the order
   → POST /orders  { table_id, order_type, items: [...] }
   → Order created with payment_status = "pending"
   → Table now shows as ORANGE/RED (occupied) ✅

4. (Optional) Add more items and place another order on same table
   → Same flow as steps 2–3
   → GET /tables/{table_id}/orders  → shows ALL current unpaid orders on table

5. Final Checkout
   → GET /tables/{table_id}/orders  → show bill summary (all items + grand total)
   → User selects payment method (Cash / UPI / Card / Online / Other)
   → POST /checkout/order/{order_id}  { payment_type: "UPI" }
      (if multiple orders on the table, call this for each order)
   → payment_status = "paid"
   → Table returns to GREEN (free) ✅

6. Next customer
   → GET /tables/status  → table is FREE again ✅
   → GET /tables/{table_id}/orders  → returns empty (no active orders) ✅
```

### Table Detail / Order View Screen
```
GET /tables/{table_id}/orders   → current unpaid orders + items + grand total
                                  (paid/past orders excluded automatically)
POST /checkout/order/{order_id} → collect payment (Cash/UPI/Card/Online/Other)
                                  → table goes back to FREE on next poll
```

### Take New Order Screen
```
GET /categories            → category filter chips
GET /menu?category_id=&food_type=&search=   → menu items list
POST /cart/items           → add item to cart
GET /cart                  → show current cart with totals
DELETE /cart/items/{menu_id}  → remove item
DELETE /cart               → clear cart
POST /checkout             → finalize order with payment type
  OR
POST /orders               → place order without immediate payment
```

### Cart Screen
```
GET /cart                  → full cart with items, subtotal, GST, total
POST /cart/items           → update quantity (replaces existing)
DELETE /cart/items/{menu_id}
DELETE /cart
POST /checkout             → Body: { payment_type, table_id, order_type, notes }
```

### Orders List Screen
```
GET /orders?status=pending&page=1&per_page=15
GET /orders/{id}           → single order detail
```

### Quick Order Screen (Takeaway — no table)
```
POST /quick-orders         → place a new takeaway order directly (no table, no cart)
GET  /quick-orders         → list all takeaway orders (filterable by status)
GET  /quick-orders/{id}    → single quick order detail with items
PUT  /quick-orders/{id}    → add more items OR update status (or both)
```

### Order Report Screen (Client / Manager only)
```
GET /reports/orders?from_date=2026-04-01&to_date=2026-04-03&status=&payment_type=
Response includes summary: total_orders, total_revenue, paid_amount, cancelled_orders
```

### Menu Management Screen (Client only)
```
GET /menu                  → list all menu items
GET /menu/{id}             → single item detail
PUT /menu/{id}             → edit item (multipart/form-data for image)
```

### Profile Screen
```
GET /profile               → user details (client or employee)
PUT /profile               → update name, phone, password
```

### Employee Management Screen (Client only)
```
GET  /client/employees
POST /client/employees     → register new employee
GET  /client/employees/{id}
PUT  /client/employees/{id}
DELETE /client/employees/{id}
```

### Role & Permissions Screen (Client only)
```
GET  /client/roles
POST /client/roles
PUT  /client/roles/{id}
DELETE /client/roles/{id}
GET  /client/roles/{id}/permissions   → view permissions for a role
PUT  /client/roles/{id}/permissions   → update permissions for a role
```

### Settings / Permissions Check
```
GET /permissions   → always call this after login to know what screens/buttons to show
```

---

## Postman Quick Reference

### 1. Login (Client)
```
POST {{base_url}}/auth/login
Content-Type: application/json

{
  "email": "navalerestro@gmail.com",
  "password": "navale@123",
  "login_type": "client"
}
```

### 2. Login (Employee)
```
POST {{base_url}}/auth/login
Content-Type: application/json

{
  "email": "rahul@navalerestro.com",
  "password": "waiter@123",
  "login_type": "employee"
}
```

### 3. Get Table Status
```
GET {{base_url}}/tables/status
Authorization: Bearer {{token}}
```

### 4. Add Item to Cart
```
POST {{base_url}}/cart/items
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "menu_id": 1,
  "quantity": 2,
  "table_id": 3,
  "order_type": "dine_in",
  "notes": "No onion"
}
```

### 5. Checkout (Cart → Paid Order)
```
POST {{base_url}}/checkout
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "payment_type": "UPI",
  "table_id": 3,
  "order_type": "dine_in"
}
```

### 6. Checkout Existing Order (Payment Only)
```
POST {{base_url}}/checkout/order/42
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "payment_type": "Cash"
}
```

### 7. Order Report
```
GET {{base_url}}/reports/orders?from_date=2026-04-01&to_date=2026-04-03
Authorization: Bearer {{token}}
```

### 8. Quick Order — Place Takeaway
```
POST {{base_url}}/quick-orders
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "notes": "Extra spicy",
  "items": [
    { "menu_id": 5, "quantity": 2, "notes": null },
    { "menu_id": 8, "quantity": 1, "notes": "No onion" }
  ]
}
```

### 9. Quick Order — Update (add items / change status)
```
PUT {{base_url}}/quick-orders/12
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "status": "confirmed",
  "items": [
    { "menu_id": 3, "quantity": 1 }
  ]
}
```

### 10. Edit Menu Item (with image)
```
PUT {{base_url}}/menu/1
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

menu_name: Paneer Butter Masala
price: 320
gst_percentage: 5
image: <file>
```

---

## Complete API List

| # | Method | Endpoint | Auth | Who Can Access |
|---|--------|----------|------|----------------|
| 1 | POST | `/auth/login` | No | Public |
| 2 | POST | `/auth/logout` | Yes | Client + Employee |
| 3 | POST | `/auth/refresh` | Yes | Client + Employee |
| 4 | GET | `/profile` | Yes | Client + Employee |
| 5 | PUT | `/profile` | Yes | Client + Employee |
| 6 | GET | `/categories` | Yes | Client + Employee |
| 7 | GET | `/table-types` | Yes | Client + Employee |
| 8 | GET | `/tables` | Yes | Client + Employee |
| 9 | GET | `/tables/status` | Yes | Client + Employee |
| 10 | GET | `/tables/{id}/orders` | Yes | Client + Employee |
| 11 | GET | `/menu` | Yes | Client + Employee |
| 12 | GET | `/menu/{id}` | Yes | Client + Employee |
| 13 | PUT | `/menu/{id}` | Yes | **Client only** |
| 14 | POST | `/orders` | Yes | Client + Employee |
| 15 | GET | `/orders` | Yes | Client + Employee |
| 16 | GET | `/orders/{id}` | Yes | Client + Employee |
| 17 | GET | `/cart` | Yes | Client + Employee |
| 18 | POST | `/cart/items` | Yes | Client + Employee |
| 19 | DELETE | `/cart/items/{menu_id}` | Yes | Client + Employee |
| 20 | DELETE | `/cart` | Yes | Client + Employee |
| 21 | POST | `/checkout` | Yes | Client + Employee |
| 22 | POST | `/checkout/order/{id}` | Yes | Client + Employee |
| 23 | GET | `/reports/orders` | Yes | Client + Employee |
| 24 | GET | `/permissions` | Yes | Client + Employee |
| 25 | GET | `/client/roles/{id}/permissions` | Yes | Client + Employee |
| 26 | PUT | `/client/roles/{id}/permissions` | Yes | **Client only** |
| 27 | GET | `/client/roles` | Yes | Client + Employee |
| 28 | POST | `/client/roles` | Yes | **Client only** |
| 29 | PUT | `/client/roles/{id}` | Yes | **Client only** |
| 30 | DELETE | `/client/roles/{id}` | Yes | **Client only** |
| 31 | GET | `/client/employees` | Yes | **Client only** |
| 32 | POST | `/client/employees` | Yes | **Client only** |
| 33 | GET | `/client/employees/{id}` | Yes | **Client only** |
| 34 | PUT | `/client/employees/{id}` | Yes | **Client only** |
| 35 | DELETE | `/client/employees/{id}` | Yes | **Client only** |
| 36 | GET | `/quick-orders` | Yes | Client + Employee |
| 37 | POST | `/quick-orders` | Yes | Client + Employee |
| 38 | GET | `/quick-orders/{id}` | Yes | Client + Employee |
| 39 | PUT | `/quick-orders/{id}` | Yes | Client + Employee |

---

## Full API Reference

---

### 1. Login

**`POST /auth/login`** — Public

```json
{
  "email": "navalerestro@gmail.com",
  "password": "navale@123",
  "login_type": "client"
}
```

**Response — Client (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1Qi...",
    "token_type": "bearer",
    "expires_in": 86400,
    "user_type": "client",
    "user": {
      "client_id": 1,
      "name": "Navale Restro",
      "email": "navalerestro@gmail.com",
      "contact": "9876543210",
      "city": "Pune",
      "image": null,
      "subscription": {
        "type": "Monthly",
        "status": "Active",
        "valid_till": "30 Apr 2026"
      }
    }
  }
}
```

**Response — Employee (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1Qi...",
    "token_type": "bearer",
    "expires_in": 86400,
    "user_type": "employee",
    "user": {
      "employee_id": 3,
      "name": "Rahul Waiter",
      "email": "rahul@navalerestro.com",
      "phone": "9876543210",
      "client_id": 1,
      "role_id": 1,
      "role_name": "Waiter",
      "image": null
    }
  }
}
```

---

### 2. Logout

**`POST /auth/logout`** — Auth required

Response: `{ "success": true, "message": "Logged out successfully" }`

---

### 3. Refresh Token

**`POST /auth/refresh`** — Auth required

```json
{ "success": true, "data": { "token": "eyJ...", "expires_in": 3600 } }
```

---

### 4. Get Profile

**`GET /profile`** — Auth required

Returns client or employee profile depending on who is logged in.

**Client response:**
```json
{
  "data": {
    "user_type": "client", "client_id": 1,
    "name": "Navale Restro", "email": "navalerestro@gmail.com",
    "contact": "9876543210", "city": "Pune", "state": "Maharashtra",
    "upi_id": "navalerestro@upi",
    "image": null,
    "subscription": { "type": "Monthly", "status": "Active", "valid_till": "30 Apr 2026" }
  }
}
```

**Employee response:**
```json
{
  "data": {
    "user_type": "employee", "employee_id": 3,
    "name": "Rahul Waiter", "email": "rahul@navalerestro.com",
    "phone": "9876543210", "client_id": 1,
    "role_id": 1, "role_name": "Waiter", "image": null
  }
}
```

---

### 5. Update Profile

**`PUT /profile`** — Auth required

```json
{
  "name": "New Name",
  "phone": "9999999999",
  "upi_id": "navalerestro@upi",
  "password": "newpass123",
  "password_confirmation": "newpass123"
}
```
> All fields optional — send only what changes.
> `upi_id` is **client only** — ignored if sent by an employee.

---

### 6. Categories

**`GET /categories`** — Auth required

```json
{
  "data": [
    { "category_id": 1, "category_name": "Veg", "client_id": null },
    { "category_id": 5, "category_name": "Specials", "client_id": 1 }
  ]
}
```

---

### 7. Table Types

**`GET /table-types`** — Auth required

```json
{
  "data": [
    { "table_type_id": 1, "type_name": "Indoor" },
    { "table_type_id": 2, "type_name": "Outdoor" }
  ]
}
```

---

### 8. Tables

**`GET /tables?table_type_id=1`** — Auth required

```json
{
  "data": [
    { "table_id": 1, "table_name": "T1", "capacity": 4, "table_type_id": 1, "table_type_name": "Indoor", "status_id": 1 }
  ]
}
```

---

### 9. Table Status (Color Coding)

**`GET /tables/status`** — Auth required

> Use this for the table floor plan / grid view. Color codes drive the UI.
> Only tables with **unpaid** active orders are marked occupied. Paid tables show as Free automatically.

```json
{
  "data": [
    {
      "table_id": 1, "table_name": "T1", "capacity": 4,
      "table_type_id": 1, "table_type_name": "Indoor",
      "table_status": "free", "color_code": "#4CAF50", "status_label": "Free",
      "active_order": null
    },
    {
      "table_id": 2, "table_name": "T2", "capacity": 2,
      "table_type_id": 1, "table_type_name": "Indoor",
      "table_status": "occupied", "color_code": "#F44336", "status_label": "Occupied",
      "active_order": {
        "order_id": 12, "order_number": "ORD-001-00012", "status": "confirmed"
      }
    }
  ]
}
```

| Color | Hex | Condition |
|-------|-----|-----------|
| Green | `#4CAF50` | No unpaid order on this table |
| Orange | `#FF9800` | Order placed but not confirmed yet (`pending`) |
| Red | `#F44336` | Order confirmed or being prepared |
| Blue | `#2196F3` | Food served, bill not collected yet |

> **Key behaviour:** After `POST /checkout` or `POST /checkout/order/{id}`, the order's `payment_status` becomes `paid`.
> The next call to `GET /tables/status` will show that table as **Free (green)** — no manual action needed.

---

### 10. Orders by Table

**`GET /tables/{table_id}/orders`** — Auth required

> Shows only the **current unpaid orders** on a specific table.
> Past/paid orders are **automatically excluded** — so this always reflects what is currently active at the table.
> Use this for the waiter's table view and bill generation screen.
>
> **Returns empty** (`"No active orders for this table"`) once the table has been checked out — meaning it is safe to use as a "is this table free?" check too.

```json
{
  "data": {
    "table_id": 3,
    "orders": [
      {
        "order_id": 42, "order_number": "ORD-001-00042",
        "status": "confirmed", "order_type": "dine_in",
        "subtotal": 560.00, "gst_amount": 28.00, "total_amount": 588.00,
        "payment_type": null, "notes": null,
        "created_at": "03 Apr 2026, 07:30 PM",
        "items": [
          {
            "item_id": 1, "menu_id": 5, "menu_name": "Paneer Tikka",
            "food_type": 1, "food_type_label": "Veg",
            "quantity": 2, "unit_price": 280.00,
            "gst_percentage": 5.00, "total_price": 588.00, "notes": null
          }
        ]
      }
    ],
    "grand_total": {
      "subtotal": 560.00, "gst_amount": 28.00,
      "total_amount": 588.00, "item_count": 2
    }
  }
}
```

---

### 11. Menu List

**`GET /menu`** — Auth required

| Param | Type | Notes |
|-------|------|-------|
| `category_id` | int | Filter by category |
| `food_type` | int | 1=Veg, 2=Non-Veg |
| `search` | string | Search by name |
| `per_page` | int | Default 20, max 50 |

```json
{
  "data": [
    {
      "menu_id": 1, "menu_name": "Paneer Tikka",
      "category_id": 1, "category_name": "Veg",
      "food_type": 1, "food_type_label": "Veg",
      "price": 280.00, "gst_percentage": 5.00,
      "stock_type": "unlimited", "quantity": null,
      "image": "https://bitrootinnovations.com/hotelhub/storage/app/public/menu/..."
    }
  ],
  "meta": { "total": 45, "per_page": 20, "current_page": 1, "last_page": 3 }
}
```

---

### 12. Single Menu Item

**`GET /menu/{id}`** — Auth required

Returns same shape as one item in the list above.

---

### 13. Edit Menu Item

**`PUT /menu/{id}`** — Client only
**Content-Type:** `multipart/form-data` (for image) or `application/json`

| Field | Required | Notes |
|-------|----------|-------|
| `menu_name` | No | Max 255 |
| `category_id` | No | Valid category |
| `food_type` | No | 1 or 2 |
| `price` | No | Min 0 |
| `gst_percentage` | No | 0–100 |
| `stock_type` | No | `limited` or `unlimited` |
| `quantity` | No | Min 0 |
| `image` | No | jpeg/png/jpg/webp, max 2MB |

```json
{ "price": 320.00, "gst_percentage": 5, "stock_type": "unlimited" }
```

---

### 14. Submit Order

**`POST /orders`** — Auth required

```json
{
  "table_id": 3,
  "order_type": "dine_in",
  "notes": "Extra spicy",
  "items": [
    { "menu_id": 1, "quantity": 2, "notes": "No onion" },
    { "menu_id": 5, "quantity": 1, "notes": null }
  ]
}
```

**Response (201):**
```json
{
  "data": {
    "order_id": 42, "order_number": "ORD-001-00042",
    "status": "pending",
    "subtotal": 840.00, "gst_amount": 42.00, "total_amount": 882.00
  }
}
```

---

### 15. Orders List

**`GET /orders?status=pending&page=1&per_page=15`** — Auth required

`status` values: `pending` / `confirmed` / `preparing` / `served` / `cancelled`

---

### 16. Single Order

**`GET /orders/{id}`** — Auth required

---

### 17. View Cart

**`GET /cart`** — Auth required

```json
{
  "data": {
    "cart_id": 1, "table_id": 3, "table_name": "T3",
    "order_type": "dine_in",
    "items": [
      {
        "id": 1, "menu_id": 5, "menu_name": "Paneer Tikka",
        "food_type": 1, "food_type_label": "Veg",
        "image": "https://...",
        "quantity": 2, "unit_price": 280.00,
        "gst_percentage": 5.00, "line_total": 588.00, "notes": null
      }
    ],
    "subtotal": 560.00, "gst_amount": 28.00,
    "total_amount": 588.00, "item_count": 2
  }
}
```

---

### 18. Add Item to Cart

**`POST /cart/items`** — Auth required

> If the item is already in cart, quantity is **replaced** (not added).

```json
{
  "menu_id": 5,
  "quantity": 2,
  "table_id": 3,
  "order_type": "dine_in",
  "notes": "No onion"
}
```

Response: full updated cart (same as GET /cart).

---

### 19. Remove Item from Cart

**`DELETE /cart/items/{menu_id}`** — Auth required

Response: full updated cart.

---

### 20. Clear Cart

**`DELETE /cart`** — Auth required

Response: `{ "success": true, "message": "Cart cleared" }`

---

### 21. Checkout (Cart → Paid Order)

**`POST /checkout`** — Auth required

> Converts current cart into a confirmed, paid order and clears the cart.

```json
{
  "payment_type": "UPI",
  "table_id": 3,
  "order_type": "dine_in",
  "notes": "Thank you"
}
```

| `payment_type` values |
|-----------------------|
| `Cash` / `UPI` / `Card` / `Online` / `Other` |

**Response (201):**
```json
{
  "data": {
    "order_id": 42, "order_number": "ORD-001-00042",
    "status": "served",
    "payment_type": "UPI", "payment_status": "paid",
    "subtotal": 560.00, "gst_amount": 28.00, "total_amount": 588.00,
    "checked_out_at": "03 Apr 2026, 08:00 PM"
  }
}
```

---

### 22. Checkout Existing Order (Payment Only)

**`POST /checkout/order/{order_id}`** — Auth required

> Use this when order was already placed and you just need to collect payment at the table.

```json
{ "payment_type": "Cash" }
```

**Response (200):**
```json
{
  "data": {
    "order_id": 42, "order_number": "ORD-001-00042",
    "payment_type": "Cash", "payment_status": "paid",
    "total_amount": 588.00,
    "checked_out_at": "03 Apr 2026, 08:05 PM"
  }
}
```

---

### 23. Order Report

**`GET /reports/orders`** — Auth required

| Param | Default | Notes |
|-------|---------|-------|
| `from_date` | today | `Y-m-d` format |
| `to_date` | today | `Y-m-d` format |
| `status` | — | `pending/confirmed/preparing/served/cancelled` |
| `payment_type` | — | `Cash/UPI/Card/Online/Other` |
| `per_page` | 20 | Max 100 |

**Response (200):**
```json
{
  "summary": {
    "from_date": "2026-04-01", "to_date": "2026-04-03",
    "total_orders": 48, "cancelled_orders": 3,
    "total_subtotal": 24500.00, "total_gst": 1225.00,
    "total_revenue": 25725.00, "paid_amount": 23200.00
  },
  "data": [
    {
      "order_id": 42, "order_number": "ORD-001-00042",
      "table_name": "T3", "status": "served", "order_type": "dine_in",
      "subtotal": 560.00, "gst_amount": 28.00, "total_amount": 588.00,
      "payment_type": "UPI", "payment_status": "paid",
      "checked_out_at": "03 Apr 2026, 08:00 PM",
      "created_at": "03 Apr 2026, 07:30 PM"
    }
  ],
  "meta": { "total": 48, "per_page": 20, "current_page": 1, "last_page": 3 }
}
```

---

### 24. My Permissions

**`GET /permissions`** — Auth required

> Call this right after login. Use these flags to show/hide screens and buttons.

**Client response (always all true):**
```json
{
  "data": {
    "user_type": "client", "role_name": "Owner",
    "can_take_orders": true, "can_checkout": true,
    "can_manage_menu": true, "can_manage_employees": true,
    "can_view_reports": true
  }
}
```

**Employee response:**
```json
{
  "data": {
    "user_type": "employee", "role_id": 1, "role_name": "Waiter",
    "can_take_orders": true, "can_checkout": false,
    "can_manage_menu": false, "can_manage_employees": false,
    "can_view_reports": false
  }
}
```

| Permission | Controls |
|-----------|---------|
| `can_take_orders` | Show order-taking screens |
| `can_checkout` | Show checkout / payment button |
| `can_manage_menu` | Show Edit Menu option |
| `can_manage_employees` | Show Employee Management screen |
| `can_view_reports` | Show Order Report screen |

---

### 25. View Role Permissions

**`GET /client/roles/{id}/permissions`** — Auth required

```json
{
  "data": {
    "id": 1, "role_name": "Waiter",
    "can_take_orders": true, "can_checkout": false,
    "can_manage_menu": false, "can_manage_employees": false,
    "can_view_reports": false
  }
}
```

---

### 26. Update Role Permissions

**`PUT /client/roles/{id}/permissions`** — Client only

```json
{
  "can_take_orders": true,
  "can_checkout": true,
  "can_manage_menu": false,
  "can_manage_employees": false,
  "can_view_reports": true
}
```

All fields optional — send only what you want to change.

---

### 27. Get Roles

**`GET /client/roles`** — Auth required

```json
{
  "data": [
    { "id": 1, "role_name": "Waiter", "status_id": 1 },
    { "id": 2, "role_name": "Manager", "status_id": 1 }
  ]
}
```

---

### 28. Create Role

**`POST /client/roles`** — Client only

```json
{ "role_name": "Captain" }
```

---

### 29. Update Role

**`PUT /client/roles/{id}`** — Client only

```json
{ "role_name": "Senior Waiter", "status_id": 1 }
```

---

### 30. Delete Role

**`DELETE /client/roles/{id}`** — Client only

---

### 31. Get Employees

**`GET /client/employees`** — Client only

```json
{
  "data": [
    {
      "id": 1, "name": "Rahul Waiter",
      "email": "rahul@navalerestro.com",
      "phone": "9876543210",
      "role_id": 1, "role_name": "Waiter",
      "status_id": 1, "profile_image": null,
      "created_at": "03 Apr 2026"
    }
  ]
}
```

---

### 32. Register Employee

**`POST /client/employees`** — Client only

```json
{
  "name": "Rahul Waiter",
  "email": "rahul@navalerestro.com",
  "password": "waiter@123",
  "password_confirmation": "waiter@123",
  "phone": "9876543210",
  "client_role_id": 1
}
```

| Field | Required | Notes |
|-------|----------|-------|
| `name` | Yes | Max 150 chars |
| `email` | Yes | Must be unique |
| `password` | Yes | Min 6 chars |
| `password_confirmation` | Yes | Must match |
| `phone` | No | Max 15 chars |
| `client_role_id` | No | Valid role for this client |

---

### 33. Get Single Employee

**`GET /client/employees/{id}`** — Client only

---

### 34. Update Employee

**`PUT /client/employees/{id}`** — Client only

```json
{
  "name": "Rahul Senior",
  "phone": "9999999999",
  "client_role_id": 2,
  "status_id": 1,
  "password": "newpass123",
  "password_confirmation": "newpass123"
}
```

All fields optional.

---

### 35. Delete Employee

**`DELETE /client/employees/{id}`** — Client only

Response: `{ "success": true, "message": "Employee removed" }`

---

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 401 | Unauthorized — invalid/expired token → redirect to Login |
| 403 | Forbidden — employee tried a client-only action |
| 404 | Not found |
| 422 | Validation error — check `errors` object in response |
| 500 | Server error |

---

---

### 36–39. Quick Orders (Takeaway)

**`GET /quick-orders`** — Client + Employee

Query: `status=pending` (optional), `per_page=15` (optional)

**Response (200):**
```json
{
  "success": true,
  "message": "Quick orders fetched",
  "data": [
    {
      "order_id": 12,
      "order_number": "ORD-001-00012",
      "table": "-",
      "type": "Takeaway",
      "status": "pending",
      "subtotal": 190.00,
      "gst_amount": 9.50,
      "total_amount": 199.50,
      "payment_type": "-",
      "payment_status": "pending",
      "notes": "Extra spicy",
      "created_at": "05 Apr 2026, 02:30 PM"
    }
  ],
  "meta": { "total": 1, "per_page": 15, "current_page": 1, "last_page": 1 }
}
```

---

**`POST /quick-orders`** — Client + Employee

> Place a new takeaway order. No `table_id` needed — always takeaway.

**Request:**
```json
{
  "notes": "Extra spicy",
  "items": [
    { "menu_id": 5, "quantity": 2, "notes": null },
    { "menu_id": 8, "quantity": 1, "notes": "No onion" }
  ]
}
```

| Field | Required | Description |
|-------|----------|-------------|
| `items` | Yes | Array of items, min 1 |
| `items.*.menu_id` | Yes | Must be an active menu item for this client |
| `items.*.quantity` | Yes | 1–99 |
| `items.*.notes` | No | Per-item instruction |
| `notes` | No | Order-level note |

**Response (201):**
```json
{
  "success": true,
  "message": "Quick order placed successfully",
  "data": {
    "order_id": 12,
    "order_number": "ORD-001-00012",
    "table": "-",
    "type": "Takeaway",
    "status": "pending",
    "subtotal": 190.00,
    "gst_amount": 9.50,
    "total_amount": 199.50,
    "payment_type": "-",
    "payment_status": "pending",
    "notes": "Extra spicy",
    "created_at": "05 Apr 2026, 02:30 PM",
    "items": [
      {
        "item_id": 1,
        "menu_id": 5,
        "menu_name": "Butter Naan",
        "food_type": 1,
        "quantity": 2,
        "unit_price": 50.00,
        "gst_percentage": 5.00,
        "total_price": 105.00,
        "notes": null
      }
    ]
  }
}
```

---

**`GET /quick-orders/{id}`** — Client + Employee

Returns full order with items (same structure as POST response above).

---

**`PUT /quick-orders/{id}`** — Client + Employee

> Update a quick order — add items and/or change status. All fields optional.
> ❌ Cannot update if order is already `served` or `cancelled`.

**Request:**
```json
{
  "status": "confirmed",
  "notes": "Updated note",
  "items": [
    { "menu_id": 3, "quantity": 1, "notes": null }
  ]
}
```

| Field | Required | Description |
|-------|----------|-------------|
| `status` | No | `pending` / `confirmed` / `served` / `cancelled` |
| `notes` | No | Replaces existing order note |
| `items` | No | New items to append to the order |

**Response (200):** Full order object (same as POST response).

---

## Order Status Flow

```
pending → confirmed → served → (checkout → paid)
               ↓
          cancelled
```

---

## Notes for React Native Developer

1. `client_id` is embedded in the JWT — all API data is automatically scoped to the correct restaurant. You don't need to send `client_id` in any request body.
2. After login, call `GET /permissions` and store the result. Use it to show/hide screens and buttons throughout the app.
3. `food_type: 1` = Veg (show green indicator), `food_type: 2` = Non-Veg (show red indicator)
4. Image URLs in responses are absolute — use directly: `<Image source={{ uri: item.image }} />`
5. Cart is per-user (scoped to whoever is logged in). Each user has their own cart.
6. To update a cart item quantity — just call `POST /cart/items` again with the same `menu_id` and new `quantity`. It replaces.
7. For image upload (Edit Menu) — use `FormData` with `multipart/form-data`, not JSON.
8. For the table floor plan, poll `GET /tables/status` every 30 seconds or use a refresh button.
9. **Table status resets automatically after checkout** — no separate API call needed. Once `POST /checkout` or `POST /checkout/order/{id}` succeeds (`payment_status` becomes `paid`), that table will show as Free (green) on the next `GET /tables/status` call.
10. **`GET /tables/{id}/orders` only returns orders where `payment_status != paid`.** After checkout, it returns an empty list. Never shows historical/past order data against a table.
11. **`payment_status` values:** `pending` (order placed, not paid) → `paid` (checkout done). Orders start as `pending` automatically — you never need to set this manually.

---

*API Version: v1 · Last updated: 2026-04-05 · Backend by Claude AI · 39 endpoints live · Table auto-free after checkout · Quick Order (Takeaway) added*
