# AGENTS.md

Laravel 10 / PHP 8.2 app that sums the price of every Steam game daily and charts the trend. Live at http://steam.seewang.me.

## Commands

```bash
composer install && cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan fetch US            # first data fetch, ~10 min per region

npm run dev                     # Vite dev server (set VITE_DEV_SERVER_HOST if not localhost)
npm run build                   # output in public/build is committed

php artisan test                                   # whole suite
php artisan test tests/Feature/HomepageTest.php    # one file
php artisan test --filter it_works                 # one test
```

## Gotchas

- `resources/views/partials/*.blade.php` and `public/ads.txt` are gitignored on purpose: they hold ads/analytics and exist only on the server.
- Code style is applied by StyleCI on PRs; there is no local linter config.
