# School Website — Database Layer
**Intellectitech Ntinda Hub | June 2026**

---

## Setup Instructions (Do This in Order)

### Step 1 — Import the Database
1. Open **phpMyAdmin** in your browser (usually `http://localhost/phpmyadmin`)
2. Click **Import** tab
3. Choose the file: `admin/db_tables.sql`
4. Click **Go**

This creates the database, all tables, and a default admin account.

### Step 2 — Configure Database Connection
Open `config/database.php` and update if needed:
```php
define('DB_USER', 'root');
define('DB_PASS', '');  
```

### Step 3 — Place Project in Server Root
- **XAMPP:** Put the folder in `C:/xampp/htdocs/school-website`
- **WAMP:** Put the folder in `C:/wamp/www/school-website`

### Step 4 — Run the Site
Open your browser and go to:
```
http://localhost/school-website/index.php
```

### Step 5 — Admin Login
```
URL:      http://localhost/school-website/admin/login.php
Email: admin@stmarys.ac.ug
Password: Admin@1234
```
**Change this password immediately after first login.**

---

## File Structure
```
school-website/
├── config/
│   └── database.php        ← PDO connection
├── includes/
│   ├── functions.php       ← All CRUD functions
│   ├── header.php          ← Nav + head HTML
│   └── footer.php          ← Footer + JS
├── assets/
│   ├── css/style.css       ← All styling
│   ├── js/main.js          ← Nav toggle + AJAX
│   └── images/             ← Upload images here
├── api/
│   └── news.php            ← JSON endpoint for AJAX
├── admin/
│   ├── db_tables.sql       ← Run this FIRST
│   ├── login.php
│   ├── dashboard.php
│   ├── manage-news.php
│   ├── messages.php
│   ├── manage-enquiries
│   ├── manage-events
│   ├── manage-staff
│   └── logout.php
├── index.php
├── contact.php
├── process_contact.php
├── admissions.php
├── about.php
├── academics.php
├── news.php
├── process_download.php
├── staff.php
├── view_document.php
└── process_enquiry.php

```

---

## Security Features Implemented
- PDO prepared statements on every query (SQL injection blocked)
- `htmlspecialchars()` on all output (XSS blocked)
- `password_hash()` / `password_verify()` for admin auth
- Session guard on every admin page
- POST-only form handlers (direct URL access blocked)
- `charset=utf8` in PDO DSN (encoding attacks blocked)
