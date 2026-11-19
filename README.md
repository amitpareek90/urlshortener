# URL Shortener

Multi-company URL shortener built with Laravel 10. A SuperAdmin creates companies and invites their first Admin. Admins invite Admins and Members inside their own company. Admins and Members create short URLs. Visitors open a short code and are redirected to the original URL.

## Requirements

- PHP 8.1 or newer, with the `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, and `ctype` extensions
- Composer
- MySQL 5.7+ or MariaDB (the default local database)
- PHP `pdo_sqlite` (used only by the automated tests, which run against an in-memory SQLite database)

If you use XAMPP, PHP is usually at `C:\xampp\php\php.exe`. Add that folder to your `PATH`, or call `php` with the full path in the commands below.

## Setup

From the project root:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, copy the env file with:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Create the MySQL database named in `.env` (`url_shortner` by default). With XAMPP’s root user and an empty password, `.env.example` already matches:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortner
DB_USERNAME=root
DB_PASSWORD=
```

Create the database, then run migrations and the seeder:

```sql
CREATE DATABASE url_shortner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate --seed
```

The seeder inserts one SuperAdmin with raw SQL.

| Email | Password | Role |
|---|---|---|
| superadmin@example.com | password | SuperAdmin |

Start the app:

```bash
php artisan serve
```

Open [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login).

## Manual testing

1. Log in as `superadmin@example.com` / `password`.
2. Open **Invite user** and create a company plus its Admin (password must be at least 8 characters).
3. Log out, then log in as that Admin.
4. Invite another Admin or a Member into the same company.
5. As the Admin or Member, open **Create short URL**, submit an `https://` address, and confirm it appears on the Short URLs page.
6. Log in as the SuperAdmin again and confirm **Create short URL** is not available. The list includes short URLs from every company.
7. Log in as an Admin and confirm the list includes only that company’s URLs, including ones created by Members.
8. Log in as a Member and confirm the list includes only URLs that Member created.
9. Copy a short URL (`http://127.0.0.1:8000/{code}`) into a private window. It should redirect to the original address without logging in.

## Automated tests

Tests use SQLite in memory (`phpunit.xml`), so they do not touch the MySQL database.

```bash
php artisan test
```

Or:

```bash
vendor/bin/phpunit
```

Covered behavior:

- Admin and Member can create short URLs
- SuperAdmin cannot create short URLs
- Admin sees only short URLs from their own company
- Member sees only short URLs they created
- SuperAdmin sees short URLs from every company
- A public short URL redirects to the original URL
- Login, logout, and invitation rules
