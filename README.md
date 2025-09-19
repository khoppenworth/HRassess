# HRassess (Recompiled)

A lightweight LAMP stack app for assessments, with **AdminLTE 3.2** UI via CDN, **i18n**, **FHIR** endpoints, and **Moodle/FHIR XML questionnaire import**.

## Quick Start

1. Create a MySQL database and user. Import `init.sql`.
2. Copy files to your PHP server (Apache or Nginx+PHP-FPM).
3. Update DB credentials in `config.php` or via environment variables.
4. Create the first admin user by visiting `/admin/users.php` (requires login); or use `init.sql` which includes a default admin (user: `admin`, pass: `admin123`).
5. Log in at `/login.php`.

## ENV Variables (recommended)
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `BASE_URL`

If not set, sensible defaults from `config.php` are used (update before prod).

## AdminLTE
- Uses AdminLTE 3.2 from CDN in `templates/head.php`.

## Importing Questionnaires
- Navigate to **Admin → Questionnaires → Import XML**.
- Supports **FHIR Questionnaire XML** (root `<Questionnaire>`) and **Moodle quiz XML** (root `<quiz>`).
- Moodle multichoice answers with `fraction="100"` are imported as correct choices.

## FHIR Endpoints
- `fhir/questionnaire.php` (GET list, GET by id)
- `fhir/questionnaire_response.php` (POST create)

## Security Notes
- Password hashing with `password_hash` (bcrypt).
- CSRF tokens on POST forms for admin actions.
- Prepared statements everywhere.
- Basic rate limiting hook (placeholder) in `lib/security.php`.

## License
MIT
