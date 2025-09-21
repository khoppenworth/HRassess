# EPSS Self-Assessment (AdminLTE 3.2 + Sections + Approvals + Import + i18n)

Features
- AdminLTE 3.2 UI (drop `dist/` + `plugins/` into `assets/adminlte/`)
- i18n: EN/AM/FR (`lang/*.json`)
- Admin: create users, change roles, reset passwords
- Admin: create questionnaires, **sections**, add/edit/delete items, import FHIR (JSON/XML), download XML template
- Staff: submit assessments (grouped by sections), view history
- Supervisor: approve/reject with comment
- FHIR API: CapabilityStatement, Questionnaire (GET), QuestionnaireResponse (GET/POST)
- CSRF + audit logs

Default accounts
- admin / admin123
- super / super123

Deploy
1. Import `init.sql`
2. Edit DB credentials in `config.php`
3. Copy AdminLTE 3.2 assets into `assets/adminlte/`
4. Visit `/index.php`

FHIR cURL example
```
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
