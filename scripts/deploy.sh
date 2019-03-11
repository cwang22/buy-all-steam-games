#!/usr/bin/env bash
git fetch --all
git checkout --force "origin/master"
composer install --no-dev
npm run production
php artisan route:cache
php artisan config:cache