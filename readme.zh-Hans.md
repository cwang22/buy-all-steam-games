# 买下所有 Steam 游戏要多少钱?

[English](https://github.com/cwang22/buy-all-steam-games/blob/master/readme.md)

你有没有想过买下所有 Steam 游戏要花多少钱？

https://steam.seewang.me

## 数据是怎么得到的？

这个网站的灵感来源于 [http://buyallofsteam.appspot.com/](http://buyallofsteam.appspot.com/)，但是原帖从 2014 年之后就没有更新了。

于是我就用 Laravel 做了这么一个网站。它每天通过 Steam Web API 遍历 Steam 上的所有应用，把商店价格加起来，保存折扣价和原价的总额。首页会画出价格走势，并找出 Steam 特卖（连续折扣在 20% 以上的日子），显示有记录以来的最大折扣和最近一次特卖的最低总价。

## API

你可以访问 `https://steam.seewang.me/api/records` 来获取 US 区的所有历史数据（单位为美元），大概长下面这样：

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

加上 `?interval=week` 可以只获取每周一条记录（每周最便宜的那一条），这样特卖的低点不会丢失。

## 安装

需要 PHP 8.2+、Composer、一个数据库（线上使用 MySQL）和一个 [Steam Web API key](https://steamcommunity.com/dev/apikey)。前端构建好的文件 `public/build` 已经提交到仓库，只有修改前端时才需要 Node.js 20.19+。

```bash
git clone https://github.com/cwang22/buy-all-steam-games.git
cd buy-all-steam-games
composer install
cp .env.example .env
php artisan key:generate
```

在 `.env` 里填好数据库配置和 `STEAM_API_KEY`，然后创建数据表：

```bash
php artisan migrate
```

获取数据是一个队列任务（`QUEUE_DRIVER=database`），所以先启动队列进程，再获取第一次的数据，每个区大概需要 10 分钟：

```bash
php artisan queue:work   # 在另一个终端里保持运行
php artisan fetch US
```

线上环境需要用 Supervisor 之类的进程管理工具保持 `php artisan queue:work` 运行，并把 Laravel 任务调度加到 cron 里，保证每天获取数据，可以参考 [Laravel 官方文档](https://laravel.com/docs/12.x/scheduling#running-the-scheduler)：

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 开发

```bash
npm ci
npm run dev       # Vite 开发服务器
npm run build     # 提交前端修改前重新构建 public/build
php artisan test
```

## 许可协议

[MIT](https://github.com/cwang22/buy-all-steam-games/blob/master/LICENSE)
