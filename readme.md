# Buy All Steam Games

[中文](https://github.com/cwang22/buy-all-steam-games/blob/master/readme.zh-Hans.md)

Ever wonder how much does it cost to buy all games from Steam?

You can check it out at https://steam.seewang.me

## How does it work?

This application was inspired by http://buyallofsteam.appspot.com/, but it hasn't been updated since 2014.

This application however is based on PHP and Laravel. Once a day it pages through every app on Steam with the Steam Web API, adds up the store prices, and saves the discounted and full-price totals. The homepage charts the history and picks out Steam sales (runs of days at 20% or more off) to show the deepest discount on record and the lowest total of the latest sale.

## API

You can hit `https://steam.seewang.me/api/records` to get all the data for the US region, in US dollars. It looks like this:

```json
{
    "data": [
        {
            "id": 1,
            "original": 233448.27,
            "sale": 229259.34,
            "created_at": "2017-04-24T20:27:36+00:00"
        }
    ]
}
```

Add `?interval=week` to get one record per week instead, the cheapest of each week, so sale dips are kept.

## Installation

Requirements: PHP 8.2+, Composer, a database (MySQL in production) and a [Steam Web API key](https://steamcommunity.com/dev/apikey). Node.js 20.19+ is only needed to change the frontend, since the built assets in `public/build` are committed.

```bash
git clone https://github.com/cwang22/buy-all-steam-games.git
cd buy-all-steam-games
composer install
cp .env.example .env
php artisan key:generate
```

Set your database credentials and `STEAM_API_KEY` in `.env`, then create the tables:

```bash
php artisan migrate
```

Fetching runs as a queued job (`QUEUE_DRIVER=database`), so start a worker and queue the first fetch. It takes about 10 minutes per region:

```bash
php artisan queue:work   # keep this running in another terminal
php artisan fetch US
```

In production, keep `php artisan queue:work` running under a process monitor such as Supervisor, and add the Laravel scheduler to cron so prices are fetched daily. See the [official documentation](https://laravel.com/docs/12.x/scheduling#running-the-scheduler) for details:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Development

```bash
npm ci
npm run dev       # Vite dev server
npm run build     # rebuild public/build before committing frontend changes
php artisan test
```

## License

[MIT](https://github.com/cwang22/buy-all-steam-games/blob/master/LICENSE)
