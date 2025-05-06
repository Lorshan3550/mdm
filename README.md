# Master Data Management (MDM) System

A simple Master Data Management web application built using **PHP** and **MySQL**, featuring user authentication, CRUD operations, and role-based access control. This system enables users to manage brands, categories, and items with user-specific access and admin capabilities.

---

## ✨ Features

### ✅ Authentication

* User registration, login, and logout.
* Access restricted to authenticated users only.
* Admin users have access to all data across the system.

### ✅ Role-Based Access Control

* `is_admin` column in the `users` table controls admin privileges.
* Admins can view and manage all records.
* Regular users can only manage their own data.

### ✅ Master Data Management (CRUD)

* **Brands**

  * Create, view (paginated), edit, and delete.
  * Soft validation and confirmation on delete.
* **Categories**

  * Create, view (paginated), edit, and delete.
* **Items**

  * Create, view (paginated), edit, and delete.
  * Upload attachments (e.g., documents or images).
  * Items are linked to Brands and Categories.

### ✅ Dashboard

* Displays total counts of users, brands, categories, and items.
* Shows recent activity logs across Brands, Categories, and Items.
* Visual bar chart using Chart.js for data distribution.

### ✅ Pagination

* Paginated views for Brands, Categories, and Items (5 per page).

### ✅ Validation

* Input validation for required fields and format.
* Display of validation error messages.

### ✅ (Optional) Advanced Features

* **Search/Filter**: Filter items by name, code, or status (future enhancement).
* **Export**: Option to export item data as CSV/Excel/PDF (future enhancement).

---

## 📃 Database Design

**Database Name**: `mdm_db`

### Tables:

#### 1. `users`

| Column      | Type      |
| ----------- | --------- |
| id          | INT, PK   |
| name        | VARCHAR   |
| email       | VARCHAR   |
| password    | VARCHAR   |
| is\_admin   | BOOLEAN   |
| created\_at | TIMESTAMP |
| updated\_at | TIMESTAMP |

#### 2. `master_brands`

| Column      | Type                      |
| ----------- | ------------------------- |
| id          | INT, PK                   |
| user\_id    | INT, FK                   |
| code        | VARCHAR                   |
| name        | VARCHAR                   |
| status      | ENUM('Active','Inactive') |
| created\_at | TIMESTAMP                 |
| updated\_at | TIMESTAMP                 |

#### 3. `categories`

| Column      | Type                      |
| ----------- | ------------------------- |
| id          | INT, PK                   |
| user\_id    | INT, FK                   |
| code        | VARCHAR                   |
| name        | VARCHAR                   |
| status      | ENUM('Active','Inactive') |
| created\_at | TIMESTAMP                 |
| updated\_at | TIMESTAMP                 |

#### 4. `items`

| Column       | Type                      |
| ------------ | ------------------------- |
| id           | INT, PK                   |
| user\_id     | INT, FK                   |
| brand\_id    | INT, FK                   |
| category\_id | INT, FK                   |
| code         | VARCHAR                   |
| name         | VARCHAR                   |
| attachment   | VARCHAR (file path)       |
| status       | ENUM('Active','Inactive') |
| created\_at  | TIMESTAMP                 |
| updated\_at  | TIMESTAMP                 |

---

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/mdm.git
cd mdm
```

### 2. Move Project to XAMPP/WAMP

* **XAMPP**: Move the `mdm` folder to `C:/xampp/htdocs/`
* **WAMP**: Move the `mdm` folder to `C:/wamp/www/`

### 3. Set Up the Database

* Open phpMyAdmin or MySQL CLI.
* Create database:

```sql
CREATE DATABASE mdm_db;
```

* Import schema file (e.g., `schema.sql`) into the database.

### 4. Install Dependencies

```bash
composer install
```

### 5. Configure Environment Variables

Create a `.env` file in the root of the project:

```ini
DB_HOST=127.0.0.1
DB_NAME=mdm_db
DB_USER=root
DB_PASS=
DB_PORT=3306
```

### 6. Run the Application

Visit: [http://localhost/mdm/](http://localhost/mdm/)

---

## 📁 Project Structure

```
mdm/
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── brand/
│   ├── create.php
│   ├── edit.php
│   ├── delete.php
│   └── index.php
├── category/
│   ├── create.php
│   ├── edit.php
│   ├── delete.php
│   └── index.php
├── item/
│   ├── create.php
│   ├── edit.php
│   ├── delete.php
│   └── index.php
├── config/
│   └── db.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── auth_check.php
├── uploads/
├── dashboard.php
├── index.php
└── .env
```

---

## 📸 Screenshots

### 🔹 Login
![Login](/screenshots/Login%20Page.png)

### 🔹 Register
![Register](/screenshots/Register%20Page.png)

### 🔹 Sidebar
![Sidebar](/screenshots/SideBar.png)

### 🔹 Dashboard
![Dashboard](/screenshots/Dashboard.png)

### 🔹 Brand
![Brand Index](/screenshots/Brand%20Index%20Page.png)

![Create Brand](/screenshots/Create%20Brand.png)

![Update Brand](/screenshots/Edit%20Brand.png)

### 🔹 Category
![Category Index](/screenshots/Category%20Index%20Page.png)

![Create Category](/screenshots/Create%20Category.png)

![Update Category](/screenshots/Edit%20Category.png)

### 🔹 Item
![Item Index](/screenshots/Item%20Index%20Page.png)

![Create Item](/screenshots/Create%20Item.png)

![Update Item](/screenshots/Edit%20Item.png)
---

## 👨‍💻 Developer

Created by Lorshan for a PHP MDM project.

---

## ✉ Feedback

If you find any bugs or have suggestions, feel free to open an issue or a pull request!
