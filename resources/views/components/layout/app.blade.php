@use('Illuminate\Support\Facades\Vite')

@props([
    'title',
    'description' => null,
    'canonical' => null,
    'robots' => 'index,follow',
    'ogImage' => null,
])

<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <script>document.documentElement.classList.add('js')</script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('imgs/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('imgs/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('imgs/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#2563eb">

    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    <meta name="robots" content="{{ $robots }}">

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    @php
        $resolvedOgImage = $ogImage ?? asset('imgs/og-image.png');
    @endphp

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="OD Tec">
    <meta property="og:title" content="{{ $title }}">
    @if ($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">
    @unless ($ogImage)
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endunless
    <meta property="og:image:alt" content="{{ $title }}">
    <meta property="og:locale" content="pt_BR">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    @if ($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">

    {{ $jsonLd ?? '' }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts() }}

    @if (config('services.google_analytics.id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ config('services.google_analytics.id') }}');
        </script>
    @endif
</head>
<body class="overflow-x-hidden bg-white font-sans text-slate-800 antialiased">
    <x-layout.header />

    <main>
        {{ $slot }}
    </main>

    <x-layout.footer />
</body>
</html>
