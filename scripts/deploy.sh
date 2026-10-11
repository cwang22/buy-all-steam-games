#!/bin/bash
cd "${0%/*}"
cd ..
git fetch origin
git reset --hard origin/master
composer install --no-dev
php artisan route:cache
php artisan config:cache
php artisan nightwatch:deploy "$(git rev-parse --short HEAD)" --ref="$(git rev-parse HEAD)"
php artisan queue:restart