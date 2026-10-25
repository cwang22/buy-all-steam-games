@extends('layouts.app')

@php
    $money = fn (float $cents, int $decimals = 0) => '$'.number_format($cents / 100, $decimals);
    $percent = fn (float $ratio) => round($ratio * 100).'%';

    $discount = $record->original > 0 ? 1 - $record->sale / $record->original : 0;
    $deepestSale = collect($sales)->sortByDesc('deepestDiscount')->first();
    $latestSale = collect($sales)->last();
@endphp

@section('content')
    <section class="hero">
        <h1 class="lede">{{ __('Buying every game on Steam right now would cost') }}</h1>
        <p class="price">{{ $money($record->sale, 2) }}</p>
        <p class="summary">
            @if(round($discount * 100) > 0)
                {!! __('That’s :percent off the full price of :original.', [
                    'percent' => '<strong class="accent">'.$percent($discount).'</strong>',
                    'original' => '<strong>'.$money($record->original, 2).'</strong>',
                ]) !!}
            @else
                {{ __('Nothing is discounted right now.') }}
            @endif
            {{ __('Prices from the :cc store, updated :created_at.', [
                'cc' => $record->cc,
                'created_at' => $record->created_at->diffForHumans(),
            ]) }}
        </p>
    </section>

    <section class="trend">
        <div id="trend-chart" data-labels="{{ json_encode([
            'title' => __('Price history'),
            'sale' => __('On sale'),
            'original' => __('Full price'),
            'loading' => __('Loading…'),
            'ranges' => ['all' => __('All time'), 'year' => __('Past year'), 'month' => __('Past month')],
        ]) }}">
            <div class="chart-header"><h2>{{ __('Price history') }}</h2></div>
            <div class="chart-wrapper"><div class="chart-status">{{ __('Loading…') }}</div></div>
        </div>
    </section>

    @if($first->isNot($record) || $deepestSale)
        <section class="facts">
            @if($first->isNot($record) && $first->original > 0)
                <div class="fact">
                    <div class="fact-value">{{ number_format($record->original / $first->original, 1) }}×</div>
                    <p>{{ __('Growth in the full price since :date, from :from to :to.', [
                        'date' => $first->created_at->translatedFormat(__('F Y')),
                        'from' => $money($first->original),
                        'to' => $money($record->original),
                    ]) }}</p>
                </div>
            @endif
            @if($deepestSale)
                <div class="fact">
                    <div class="fact-value accent">{{ $percent($deepestSale->deepestDiscount) }}</div>
                    <p>{{ __('Deepest discount on record, during :sale.', ['sale' => $deepestSale->name()]) }}</p>
                </div>
                <div class="fact">
                    <div class="fact-value">{{ $money($latestSale->lowestSale) }}</div>
                    <p>
                        @if($latestSale->ongoing)
                            {{ __('Lowest total so far in the current sale, :percent below full price.', [
                                'percent' => $percent($latestSale->lowestDiscount()),
                            ]) }}
                        @else
                            {{ __('Lowest total during :sale, :percent below full price.', [
                                'sale' => $latestSale->name(),
                                'percent' => $percent($latestSale->lowestDiscount()),
                            ]) }}
                        @endif
                    </p>
                </div>
            @endif
        </section>
    @endif

    <section class="info">
        <div>
            <h2>{{ __('How does it work?') }}</h2>
            <p>{!! __('This page was inspired by :link, but it hasn\'t been updated since 2014.',
                ['link' => '<a href="http://buyallofsteam.appspot.com/" target="_blank">buyallofsteam.appspot.com</a>']) !!}</p>
            <p>{{ __('This page however is based on PHP and Laravel, and automatically fetch daily.') }}</p>
        </div>
        <div>
            <h2>API</h2>
            <p>{!! __('You can hit :link to get all the data.',
                ['link' => sprintf('<code>%s</code>', url('api/records'))]) !!}</p>
        </div>
    </section>
@endsection
