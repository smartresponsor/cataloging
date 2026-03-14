# Cataloging catch hardening note

Goal:
- avoid silent catch blocks
- log operational failures
- return or collect human-readable English messages
- keep service-level failures diagnosable from logs

Applied in this wave:
- importer and batch/import services
- command services with transactional behavior
- main category API / admin move / webhook controllers

Follow-up:
- run `php tools/inspection/catalog-catch-audit.php`
- verify that every catch either logs, throws, or returns a human-readable message
