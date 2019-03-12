#!/bin/bash
git pull
composer install --no-dev
npm run production
php artisan route:cache
php artisan config:cache