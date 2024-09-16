@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col">
            <h1 class="mt-5">{{ __('Buy All Steam Games') }}</h1>
            <div class="mt-5">
                @if($locale === 'zh')
                    <p><a href="{{ url('/?locale=en') }}">English</a> | 中文</p>
                @else
                    <p>English | <a href="{{ url('/?locale=zh') }}">中文</a></p>
                @endif
            </div>
            <div class="mt-3">
                <a class="github-button" href="https://github.com/cwang22" data-size="large" data-show-count="true"
                   aria-label="Follow @cwang22 on GitHub">Follow @cwang22</a>
                <a class="github-button" href="https://github.com/cwang22/buy-all-steam-games" data-size="large"
                   data-show-count="true" aria-label="Star cwang22/buy-all-steam-games on GitHub">Star</a>
            </div>
            <div class="mt-5">
                <p>{{ __('Ever wonder how much does it cost to buy all games from Steam?') }}</p>
                <p>{!! __('Well, at the moment it costs :sale at a discounted price which costs :original at full price.', [
                    'sale' => sprintf('<span class="text-danger font-weight-bold">$%s</span>', $record->sale / 100),
                    'original' => sprintf('<span class="text-danger font-weight-bold">$%s</span>', $record->original / 100)
                    ]
                )  !!}</p>
                <p class="text-muted"><small>{{ __('This page was last updated :created_at, based on the price of the :cc region.', [
                        'created_at' => $record->created_at->diffForHumans(),
                        'cc' => $record->cc
                    ]) }}</small></p>
            </div>
            <h2 class="mt-5">{{ __('Trends') }}</h2>
            <Chart></Chart>

            <h2 class="mt-5 pt-5">{{ __('How does it work?') }}</h2>
            <p>{!! __('This page was inspired by :link, but it hasn\'t been updated since 2014.', 
                ['link' => '<a href="http://buyallofsteam.appspot.com/" target="_blank">http://buyallofsteam.appspot.com/</a>']) !!}</p>
            <p>{{ __('This page however is based on PHP and Laravel, and automatically fetch daily.') }}</p>
            <h2 class="mt-5">API</h2>
            <p>{!! __('You can hit :link to get all the data. It should look like this.',
                ['link' => sprintf('<code>%s</code>', url('api/records'))]) !!}
            </p>
            <pre>
            {
                "data": [
                    {
                        "id": 1,
                        "original": 233448.27,
                        "sale": 229259.34,
                        "created_at": "2017-04-24T20:27:36+00:00"
                    },
                    ...
                ]
            }
            </pre>
        </div>
    </div>
@endsection