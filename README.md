# EPSS Self-Assessment (Core)

This is the **Core App** deliverable for the EPSS Self-Assessment system (ZIP 1).

## Deploy (quick)
1. Create MySQL DB `epss` and user with privileges.
2. Import `init.sql`.
3. Put AdminLTE 3.2 assets under `assets/adminlte/dist/` and `assets/adminlte/plugins/`.
4. Update DB creds in `config.php`.
5. Point Apache vhost to this folder. Visit `/index.php`.

Default users:
- admin / admin123
- super / super123
- staff / super123 (demo)

## Files in Core
- Authentication, role checks
- Submit assessments, personal performance
- i18n (EN/AM/FR)
