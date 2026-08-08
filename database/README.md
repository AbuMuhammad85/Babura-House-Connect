# Babura House Connect - Database Setup

This guide details how to initialize the persistent MySQL database schema for standard development and production environments.

---

## Prerequisites
1. A running MySQL or MariaDB instance (e.g. XAMPP, WAMP, or standalone MySQL Server).
2. Database details match your local `.env` configuration (default db: `babura_house_connect`).

---

## Option A: Import via MySQL CLI (Recommended)

1. Open your terminal or Command Prompt.
2. Login to your MySQL server and run the import command pointing to `database/schema.sql`:

```bash
# Log in and create the database (if not exists)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS babura_house_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the schema tables and seed data
mysql -u root -p babura_house_connect < database/schema.sql
```

*Note: Replace `root` with your database username. Press Enter when prompted for your password (leave empty if no password is configured).*

---

## Option B: Import via phpMyAdmin (Web Interface)

1. Open your browser and navigate to **http://localhost/phpmyadmin**.
2. Click on the **SQL** tab or create a new database named `babura_house_connect` using `utf8mb4_unicode_ci` collation.
3. Select the `babura_house_connect` database from the left-hand menu.
4. Click on the **Import** tab in the top navigation bar.
5. Click **Choose File** and select the [schema.sql](schema.sql) file.
6. Click the **Go** button at the bottom of the page to execute the SQL commands.

---

## Verifying the Setup

After importing the database, execute the connectivity test script:

```bash
php scripts/test_database.php
```

You should see:
```text
[PASS] Environment configuration loaded successfully.
[PASS] Database connection established successfully via PDO.
[PASS] All 16 database tables are verified in schema.
[PASS] Seed verification: 2 areas found in database.
```
