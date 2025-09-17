# EPSS Self-Assessment Web App

This is a LAMP-based web application for the Ethiopia EPSS staff self-assessment.

## Features
- Authentication (staff & admin)
- Self-assessment submission
- Admin panel with user/questionnaire management
- FHIR-compliant API (Questionnaire, QuestionnaireResponse, metadata)
- Export data as CSV
- Looker Studio integration

## Setup
1. Import `init.sql` into MySQL
2. Update DB credentials in `config.php`
3. Download AdminLTE into `assets/adminlte/`
4. Deploy to Apache server with PHP

## Looker Studio
Connect the MySQL database to Google Looker Studio for visualization.
