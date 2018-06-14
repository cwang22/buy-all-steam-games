# Buy All Steam Games

[中文](https://github.com/cwang22/buy-all-steam-games/blob/master/readme.zh-Hans.md)

Ever wonder how much does it cost to buy all games from Steam?

You can check it out at http://steam.seewang.me

## How does it work?
This application was inspired by http://buyallofsteam.appspot.com/, but it haven't been updated since 2014.</p>

This application however is based on PHP and Laravel, and automatically fetch data daily.

## API
You can hit `http://steam.seewang.me/api` to get the latest data. It should looks like this.

        [
            {
                "original": 233448.27,
                "sale": 229259.34,
                "cc": "US"
                "language": "english"
                "created_at": "2017-04-18 14:10:59"
            },
            ...
        ]
        
## Installation
This application is based on Laravel 5.6, uses artisan command and schedule jobs.

        git clone https://github.com/cwang22/buy-all-steam-games.git
        git cd buy-all-steam-games
        composer install
        cp .env.example .env
        php artisan key:generate
        
Now you should fetch data for the first time, it may take about 10 minutes.
        
        php artisan fetch
        
Then you should setup cron jobs for Laravel, so prices can be fetched automatically, check [official document](https://laravel.com/docs/5.6/scheduling) for detail.

## License
[MIT](https://github.com/cwang22/buy-all-steam-games/blob/master/LICENSE)
