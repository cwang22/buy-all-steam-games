@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col">
            <h1 class="mt-5">Buy All Steam Games</h1>
            <div class="mt-5">
                <p>English | <a href="{{ url('/zh') }}">中文</a></p>
            </div>
            <div class="mt-3">
                <a class="github-button" href="https://github.com/cwang22" data-size="large" data-show-count="true"
                   aria-label="Follow @cwang22 on GitHub">Follow @cwang22</a>
                <a class="github-button" href="https://github.com/cwang22/buy-all-steam-games" data-size="large"
                   data-show-count="true" aria-label="Star cwang22/buy-all-steam-games on GitHub">Star</a>
            </div>
            <Home></Home>

            <h2 class="mt-5 pt-5">How does it work?</h2>
            <p>This page was inspired by <a
                        href="http://buyallofsteam.appspot.com/" target="_blank">http://buyallofsteam.appspot.com/</a>,
                but it hasn't
                been updated since 2014.</p>
            <p>This page however is based on PHP and Laravel, and automatically fetch daily.</p>
            <h2 class="mt-5">API</h2>
            <p>You can hit <code>{{url('api')}}</code> to get all the data. It should look like this.</p>
            <pre>
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
            </pre>
        </div>
    </div>
@endsection