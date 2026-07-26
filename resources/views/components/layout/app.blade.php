@props([
    'title',
    'description' => null,
    'canonical' => null,
    'robots' => 'index,follow',
    'ogImage' => null,
])

<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
    <x-layout.head :title="$title" :description="$description" :canonical="$canonical" :robots="$robots" :og-image="$ogImage">{{ $jsonLd ?? '' }}</x-layout.head>
<body class="overflow-x-hidden bg-white font-sans text-slate-800 antialiased">
    <x-layout.header />

    <main>
        {{ $slot }}
    </main>

    <x-layout.footer />

    <x-layout.cookie-consent />

    @livewireScripts
</body>
</html>
