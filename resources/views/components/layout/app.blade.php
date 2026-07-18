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

    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    <meta name="robots" content="{{ $robots }}">

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="OD Tech">
    <meta property="og:title" content="{{ $title }}">
    @if ($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    @if ($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif

    {{ $jsonLd ?? '' }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ Vite::fonts() }}
</head>
<body class="overflow-x-hidden bg-white font-sans text-slate-800 antialiased">
    <x-layout.header />

    <main>
        {{ $slot }}
    </main>

    <x-layout.footer />
</body>
</html>
