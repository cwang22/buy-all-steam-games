@extends('layouts.app')

@php
    $sale = $record->sale / 100;
    $original = $record->original / 100;
    $discount = $original > 0 ? (int) round((1 - $sale / $original) * 100) : 0;
@endphp

@section('content')
    <section class="hero">
        <h1>{{ __('Ever wonder how much does it cost to buy all games from Steam?') }}</h1>
        <p class="lede">{{ __('Updated :created_at, based on the price of the :cc region.', [
            'created_at' => $record->created_at->diffForHumans(),
            'cc' => $record->cc,
        ]) }}</p>

        <div class="stats">
            <div class="card">
                <div class="stat-label">{{ __('On sale') }}</div>
                <div class="stat-value is-sale">${{ number_format($sale, 2) }}</div>
                <div class="stat-meta">
                    @if($discount > 0)
                        <span class="discount">-{{ $discount }}%</span>
                    @endif
                    <span>{{ __('You would save :amount', ['amount' => '$'.number_format($original - $sale, 2)]) }}</span>
                </div>
            </div>
            <div class="card">
                <div class="stat-label">{{ __('Full price') }}</div>
                <div class="stat-value">${{ number_format($original, 2) }}</div>
                <div class="stat-meta">
                    <span>{{ __('What it costs without any discounts') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div id="trend-chart" data-labels="{{ json_encode([
                'title' => __('Trends'),
                'sale' => __('On sale'),
                'original' => __('Full price'),
                'loading' => __('Loading…'),
                'ranges' => ['all' => __('All'), 'year' => __('1Y'), 'month' => __('1M')],
            ]) }}">
                <div class="chart-header"><h2>{{ __('Trends') }}</h2></div>
                <div class="chart-wrapper"><div class="chart-status">{{ __('Loading…') }}</div></div>
            </div>
        </div>
    </section>

    <section class="section info">
        <div class="card">
            <h2>{{ __('How does it work?') }}</h2>
            <p>{!! __('This page was inspired by :link, but it hasn\'t been updated since 2014.',
                ['link' => '<a href="http://buyallofsteam.appspot.com/" target="_blank">buyallofsteam.appspot.com</a>']) !!}</p>
            <p>{{ __('This page however is based on PHP and Laravel, and automatically fetch daily.') }}</p>
        </div>
        <div class="card">
            <h2>API</h2>
            <p>{!! __('You can hit :link to get all the data. It should look like this.',
                ['link' => sprintf('<code>%s</code>', url('api/records'))]) !!}</p>
<pre>{
  "data": [
    {
      "id": 1,
      "original": 233448.27,
      "sale": 229259.34,
      "created_at": "2017-04-24T20:27:36+00:00"
    },
    ...
  ]
}</pre>
        </div>
    </section>
@endsection
