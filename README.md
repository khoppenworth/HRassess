# EPSS Self-Assessment (AdminLTE 3.2 + i18n + FHIR + Import + Approvals)

**Default Users**
- Admin: `admin / admin123`
- Supervisor: `super / super123`

## Setup
1. Import `init.sql` into MySQL
2. Edit `config.php` with DB credentials
3. Download AdminLTE **3.2** and copy `dist/` and `plugins/` into `assets/adminlte/`
4. Ensure Apache serves this folder, HTTPS recommended

## i18n
- Language switcher in navbar (EN/AM/FR)
- Strings in `lang/*.json`

## Admin Panel
- Users: `/admin/users.php`
- Manage Questionnaires (create, add items, **import FHIR JSON/XML**): `/admin/questionnaire_manage.php`
- Supervisor Review (approve/reject): `/admin/supervisor_review.php`
- Export CSV: `/admin/export.php`

## Staff
- Dashboard: `/dashboard.php`
- Submit assessment: `/submit_assessment.php?qid=1`

## FHIR Endpoints
- CapabilityStatement: `/fhir/metadata.php`
- Questionnaire (GET): `/fhir/Questionnaire.php?id=1` or list
- QuestionnaireResponse (GET/POST): `/fhir/QuestionnaireResponse.php`

### cURL Example (POST QuestionnaireResponse)
```bash
curl -X POST "https://your-domain/fhir/QuestionnaireResponse.php"   -H "Content-Type: application/json"   -d '{
    "resourceType": "QuestionnaireResponse",
    "questionnaire": "1",
    "user_id": 1,
    "item": [
      { "linkId": "q1", "answer": [{ "valueBoolean": true }] },
      { "linkId": "q2", "answer": [{ "valueString": "Paracetamol, Amoxicillin" }] },
      { "linkId": "q3", "answer": [{ "valueString": "2025-09-01" }] }
    ]
  }'
```

### Import a FHIR Questionnaire
- JSON: use `samples/sample_questionnaire.json`
- XML: use `samples/sample_questionnaire.xml`
In Admin → *Manage Questionnaires* → *Import Questionnaire File (XML/JSON)*.

## Notes
- Responses enter `status=submitted` by default. Supervisors **approve** or **reject**.
- CSV export includes status and review metadata.  
- Connect MySQL to Looker Studio directly for reporting.
