@extends('layouts.app')

@section('content')
    <div class="row">
        <div id="showcase" class="col">
            <h1 class="mt-5">买下所有 Steam 游戏要多少钱?</h1>
            <div class="mt-3">
                <a class="github-button" href="https://github.com/cwang22" data-size="large" data-show-count="true"
                   aria-label="Follow @cwang22 on GitHub">Follow @cwang22</a>
                <a class="github-button" href="https://github.com/cwang22/buy-all-steam-games" data-size="large"
                   data-show-count="true" aria-label="Star cwang22/buy-all-steam-games on GitHub">Star</a>
            </div>
            <div class="mt-5">
                <p><a href="{{ url('/') }}">English</a> | 中文</p>
            </div>
            <div class="mt-3">
                <p>你有没有想过买下所有 Steam 游戏要花多少钱？</p>
                <p>截至目前，Steam 所有游戏打折后的价格为 <span class="text-danger">${{ $record->sale }}</span>，而原价则为 <span
                            class="text-danger">${{ $record->original }}</span>。</p>
                <p>这一数据更新于 {{ $record->created_at->diffForHumans() }}, 取自 {{$record->cc}} 区且语言为 {{$record->language}}
                    的数据。
                </p>
            </div>
            <h2 class="mt-5">趋势</h2>
            <chart :records="{{ $records }}"></chart>
            <h2 class="mt-5 pt-5">数据是怎么得到的？</h2>
            <p>这个网站的灵感来源于 <a
                        href="http://buyallofsteam.appspot.com/" target="_blank">http://buyallofsteam.appspot.com/</a>，但是原帖从
                2014 年之后就没有更新了。</p>
            <p>于是我就用 Laravel 做了这么一个网站，每天自动获取数据。</p>
            <h2 class="mt-5">API</h2>
            <p>你可以访问 <code>{{url('api')}}</code> 来获取所有历史数据，API 大概长下面这样：</p>
            <pre>
            [
                {
                    "original": 233448.27,
                    "sale": 229259.34,
                    "cc": "US"
                    "language": "English"
                    "created_at": "2017-04-18 14:10:59"。
                },
                ...
            ]
            </pre>
        </div>
    </div>
@endsection