# EPSS Self-Assessment (LAMP + AdminLTE 3.2 + FHIR JSON + i18n)

- Default admin: **admin / admin123**
- Import `init.sql` into MySQL and set DB creds in `config.php`
- Download AdminLTE 3.2 into `assets/adminlte/` (keep `dist/` and `plugins/`)
- FHIR JSON endpoints at `/fhir/`
- Languages: EN (default), AM, FR (switch via 🌐 menu or `/set_lang.php?lang=xx`)

## CURL: Submit a QuestionnaireResponse
```bash
curl -X POST "https://your-domain/fhir/QuestionnaireResponse.php"   -H "Content-Type: application/json"   -d '{
    "resourceType": "QuestionnaireResponse",
    "questionnaire": "1",
    "user_id": 1,
    "status": "completed",
    "authored": "2025-09-18T12:00:00Z",
    "item": [
      { "linkId": "q1", "answer": [{ "valueBoolean": true }] },
      { "linkId": "q2", "answer": [{ "valueString": "Paracetamol, Amoxicillin" }] },
      { "linkId": "q3", "answer": [{ "valueString": "2025-09-01" }] }
    ]
  }'
```

## Deployment
1. Create DB & import `init.sql`
2. Update `config.php`
3. Place AdminLTE 3.2 in `assets/adminlte/`
4. Set Apache `DocumentRoot` to this folder and enable HTTPS
5. Login with **admin / admin123** and create staff users
