# App / Category RC1 → GA

Install:
1. composer install
2. php bin/console doctrine:migrations:migrate
3. php -S 127.0.0.1:8080 -t public public/index.php

Smoke:
- curl http://localhost:8080/api/category/storefront
- php tools/smoke/category-k6.js (via k6)

Deploy:
- helm install category ./deploy/helm/category
