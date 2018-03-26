@extends('layouts.app')

@section('content')
    <div class="row">
        <div id="showcase" class="col">
            <h1 class="mt-5">Buy All Steam Games</h1>
            <div class="mt-3">
                <a class="github-button" href="https://github.com/cwang22" data-size="large" data-show-count="true" aria-label="Follow @cwang22 on GitHub">Follow @cwang22</a>
                <a class="github-button" href="https://github.com/cwang22/buy-all-steam-games" data-size="large" data-show-count="true" aria-label="Star cwang22/buy-all-steam-games on GitHub">Star</a>
            </div>
            <div class="mt-5">
                <p>Ever wonder how much does it cost to buy all games from Steam?</p>
                <p>Well, at the moment it costs <span class="text-danger">${{ $record->sale }}</span> at a discounted price which costs<span
                            class="text-danger">${{ $record->original }}</span> at full price.</p>
                <p>This page was last updated {{ $record->created_at->diffForHumans() }}, based on the price of
                    the {{$record->cc}} region and {{$record->language}} language.
                </p>
            </div>

            <h2 class="mt-5">Trends</h2>
            <chart :records="{{ $records }}"></chart>

            <h2 class="mt-5">How does it work?</h2>
            <p>This page was inspired by <a
                        href="http://buyallofsteam.appspot.com/" target="_blank">http://buyallofsteam.appspot.com/</a>, but it hasn't
                been updated since 2014.</p>
            <p>This page however is based on PHP and Laravel, and automatically fetch data every week.</p>
            <h2 class="mt-5">API</h2>
            <p>You can hit <code>{{url('api')}}</code> to get all the data. It should look like this.</p>
            <pre>
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
            </pre>
        </div>
    </div>
@endsection