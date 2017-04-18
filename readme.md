# Buy All Steam Games

Ever wonder how much does it costs to buy all games from Steam?

You can check it out at http://steam.seewang.me

## How does it work?
This application was inspired by http://buyallofsteam.appspot.com/, which does exactly the same thing, but haven't updated since 2014.</p>

This application however is based on PHP and Laravel, and automatically fetch data every week.

## API
You can hit `http://steam.seewang.me/api` to get the latest data. It should looks like this.

        {
            "original":12345.67,
            "sale":12345.67,
            "updated_at":"2017-01-11 12:00:00"
        }
## Installation
This application is based on Laravel 5.4, uses artisan command and schedule jobs.

        git clone https://github.com/cwang22/buy-all-steam-games.git
        git cd buy-all-steam-games
        composer install
        cp .env.example .env
        php artisan key:generate
        
Now you should fetch data for the first time, it may take about 10 minutes.
        
        php artisan fetch
        
Then you should setup cron jobs for Laravel, so prices can be fetched automatically, check [official document](https://laravel.com/docs/5.4/scheduling) for detail.
