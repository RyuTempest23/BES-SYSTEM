# BeSCMS

## Overview

BeSCMS is a lightweight Barangay e-Services and Certificate Management System built with PHP. It provides a web interface for residents to submit requests, and for barangay administrators to manage pending requests, verify accounts, and generate certificates.

## Key Features

- Resident login and signup
- Certificate request submission
- Admin dashboard for request management
- Account verification and reporting
- JWT-based authentication support via `firebase/php-jwt`
- Simple, responsive login page with form validation and styled card layout

## Requirements

- PHP 7.4 or higher
- MySQL / MariaDB
- Apache or compatible web server
- Composer
- XAMPP or similar local development environment

## Installation

1. Clone or copy the project into your web server root (for example `C:\xampp\htdocs\BeSCMS`).
2. Run Composer install from the project root:

```bash
composer install
```

3. Import the database schema located in `database/bes_schema.sql` into your MySQL/MariaDB instance.
4. Configure your database connection in `config.php` or `includes/db.php` as needed.
5. Open the project in your browser at `http://localhost/BeSCMS`.

## Project Structure

- `index.php` - front controller / routing entry point
- `config.php` - application configuration
- `includes/` - shared helpers, database connection, middleware, JWT helper
- `modules/` - functional modules for auth, residents, requests, admin
- `views/` - HTML/PHP views for auth, resident, and admin pages
- `assets/images/` - project images used for backgrounds and logo
- `database/bes_schema.sql` - database schema definition
- `composer.json` - PHP dependency configuration

## Usage

- Browse to `http://localhost/BeSCMS`
- Use the login page to sign in or create a new resident account
- Admin users can manage requests in `views/admin/`
- Resident users can view their requests in `views/resident/`

## Customization

- Update styles and layout inside `views/auth/login.php`
- Change the background or logo image in `assets/images/`
- Modify request and admin logic in `modules/m3_requests.php` and `modules/m4_admin.php`

## License

This project is provided as-is. See `LICENSE` for license details.
