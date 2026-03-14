#!/bin/sh
php -r "file_put_contents('report/catalog-cache-metrics.json', json_encode(['ts'=>date('c'),'hits'=>[],'misses'=>[]], JSON_PRETTY_PRINT));"
