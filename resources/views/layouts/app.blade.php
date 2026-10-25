<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    @includeIf('partials.header')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#111111" media="(prefers-color-scheme: dark)">
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;700;800&display=swap">

    <title>{{ __('How much does it cost to buy all Steam games') }}</title>

    @vite(['resources/js/app.js'])
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="brand" href="{{ url('/') }}">{{ __('Buy All Steam Games') }}</a>
        <nav class="nav">
            @if(($locale ?? 'en') === 'zh')
                <a href="{{ url('/?locale=en') }}" lang="en">EN</a><span class="current" lang="zh">中文</span>
            @else
                <span class="current" lang="en">EN</span><a href="{{ url('/?locale=zh') }}" lang="zh">中文</a>
            @endif
            <a class="github-link" href="https://github.com/cwang22/buy-all-steam-games" aria-label="GitHub">
                <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
            </a>
        </nav>
    </div>
</header>
<main class="container">
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container">
        Copyright &copy; {{ date('Y') }} <a href="https://seewang.me">Chenggang Wang</a>
        · Hosted on <a href="https://m.do.co/c/50614500d0db" target="_blank">DigitalOcean</a>
    </div>
</footer>
@includeIf('partials.footer')
</body>
</html>
