# Category RC1 Ops

Run smoke after deploy:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:category:projection:run --once
curl http://localhost:8080/metrics
```
