@php
    $navLinks = [
        ['label' => 'Sobre', 'url' => route('about')],
        ['label' => 'Serviços', 'url' => route('servicos')],
        ['label' => 'Processo', 'url' => route('home').'#processo'],
        ['label' => 'Trabalhos', 'url' => route('home').'#trabalhos'],
        ['label' => 'Blog', 'url' => route('blog.index')],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-slate-800/10 bg-white/85 backdrop-blur-md">
    <nav class="flex items-center justify-between px-8 py-5 min-[960px]:px-14" aria-label="Principal">
        <a href="{{ route('home') }}" class="flex items-center">
            <img src="{{ asset('imgs/logo.png') }}" alt="OD Tec" class="h-8 w-auto">
        </a>

        <div class="hidden items-center gap-9 min-[780px]:flex">
            @foreach ($navLinks as $link)
                <a href="{{ $link['url'] }}" class="text-[15px] font-semibold text-slate-800 transition-transform duration-200 hover:-translate-y-0.5">{{ $link['label'] }}</a>
            @endforeach

            <x-ui.button :href="route('contact.show')" variant="primary" class="px-6 py-3 text-sm">Fale com a gente</x-ui.button>
        </div>

        <button
            type="button"
            data-menu-toggle
            aria-controls="mobile-menu"
            aria-expanded="false"
            aria-label="Abrir menu"
            class="flex h-10 w-10 items-center justify-center rounded-[10px] min-[780px]:hidden"
        >
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/>
            </svg>
        </button>
    </nav>

    <div
        id="mobile-menu"
        data-mobile-menu
        class="absolute inset-x-0 top-full hidden flex-col gap-1 border-b border-slate-800/10 bg-white px-6 pt-3 pb-6 shadow-[0_16px_32px_-12px_rgba(30,41,59,0.15)] min-[780px]:hidden"
    >
        @foreach ($navLinks as $link)
            <a href="{{ $link['url'] }}" class="rounded-lg px-2 py-3 text-base font-semibold text-slate-800">{{ $link['label'] }}</a>
        @endforeach

        <x-ui.button :href="route('contact.show')" variant="primary" class="mt-2 w-full text-[15px]">Fale com a gente</x-ui.button>
    </div>
</header>
