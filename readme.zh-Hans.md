# 买下所有 Steam 游戏要多少钱?

你有没有想过买下所有 Steam 游戏要花多少钱？

https://steam.seewang.me

## How does it work?
这个网站的灵感来源于 [http://buyallofsteam.appspot.com/](http://buyallofsteam.appspot.com/)，但是原帖从 2014 年之后就没有更新了。

于是我就用 Laravel 做了这么一个网站，每天自动获取数据。

## API
你可以访问 `http://steam.seewang.me/api` 来获取所有历史数据，API 大概长下面这样：

        [
            {
                "original": 233448.27,
                "sale": 229259.34,
                "cc": "US"
                "language": "English"
                "created_at": "2017-04-18 14:10:59"
            },
            ...
        ]
        
## 安装
这个项目基于 Laravel 5.6，并使用了 Artisan 命令和任务调度。

        git clone https://github.com/cwang22/buy-all-steam-games.git
        git cd buy-all-steam-games
        composer install
        cp .env.example .env
        php artisan key:generate
        
用下面的命令获取第一次的数据，大概需要 10 分钟。
        
        php artisan fetch
        
设置自动任务调度来保证每天获取数据，可以参考 [Laravel 官方文档](https://laravel.com/docs/5.6/scheduling)。

## 许可协议
MIT
[MIT](https://github.com/cwang22/buy-all-steam-games/blob/master/LICENSE)
