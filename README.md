# Redstone Core — Landing Page

A responsive marketing landing page for a software-engineering studio, built with
plain PHP and MySQL. It presents services and captures leads through a contact form
that stores submissions in the database.

**Live site:** https://redstone.lat

> The phone number and email bundled in this repository are **example placeholders**
> (not the real contact data). The live site above is the actual deployment.

## Problem

Small studios need a fast, dependency-light web presence that works on cheap shared
hosting, captures leads reliably, and ships with sane security defaults — without a
heavy framework or a build step.

## Features

- Single-file PHP page (`index.php`) — no framework, no build step.
- Lead-capture contact form with server-side validation + anti-spam honeypot.
- Data persisted in MySQL through PDO prepared statements.
- Dark/light theme, mobile/desktop preview tool, scroll animations (`main.js`, vanilla JS).
- Accessibility touches: skip link, ARIA labels, reduced-motion support.
- Config via environment / `.env`; the app **fails closed** if DB credentials are missing.

## Stack

| Layer | Technology |
|---|---|
| Backend | PHP (PDO, prepared statements) |
| Database | MySQL 8 (`utf8mb4`) |
| Frontend | HTML5, CSS3, vanilla ES6 JavaScript |
| Icons / fonts | Font Awesome (CDN), Google Fonts | 
| Server | Apache (`.htaccess`) or any PHP host |

## Architecture

```
Browser ──► index.php ──► conexion.php ──► MySQL (PDO)
              │  reads .env / environment vars
              └─ POST contact form ──► INSERT INTO contacto
```

- `conexion.php` loads credentials with **environment-first** precedence and a
  fail-closed guard: if any required key is missing it aborts instead of falling
  back to hardcoded credentials.
- `.htaccess` denies web access to `.env*` files when `AllowOverride` is enabled.

## Getting started

```bash
# 1. Create the database schema
mysql -u root -p < database.sql

# 2. Configure credentials
cp env.example .env
nano .env            # set DB_HOST, DB_NAME, DB_USER, DB_PASS

# 3. Serve it (example with PHP's built-in server)
php -S localhost:8000
```

Then open http://localhost:8000 and submit the contact form.

## Project structure

```
index.php            Page markup, contact-form handling, service cards
conexion.php         Env/.env loader + fail-closed PDO connection
database.sql         Schema: contacto (leads) + servicios (services)
env.example          Environment template (no secrets)
.htaccess            Deny access to .env*
assets/css/style.css Styling, theming, animations
assets/js/main.js    Vanilla JS: validation, menu, scrollspy, theme, preview
```

## Security notes

- No credentials in code: all secrets come from `.env` / environment variables.
- Prepared statements everywhere (no SQL injection in the form path).
- Honeypot field silently discards bot submissions.
- PDO exception details are logged, never shown to visitors.

## Result

A lightweight, self-hosted landing page that runs on minimal shared hosting,
stores leads safely, and needs no framework, package manager, or build pipeline.

## Credits

Built by **Loy (MrLooy)**.

## License

MIT — see [LICENSE](LICENSE).
