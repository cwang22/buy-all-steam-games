#!/bin/bash
cd "${0%/*}"
cd ..
git fetch -all
git reset --hard origin/master
git pull
composer install --no-dev
npm run production
php artisan route:cache
php artisan config:cache
php artisan queue:restart