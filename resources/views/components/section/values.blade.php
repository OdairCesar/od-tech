@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'items' => [],
])

<section {{ $attributes->class(['mx-auto max-w-[1180px] px-5 py-16 sm:px-8 sm:py-[110px] min-[960px]:px-14']) }}>
    <x-ui.section-title :eyebrow="$eyebrow" :description="$description" titleClass="text-[28px] sm:text-[38px]" class="mb-12 max-w-2xl" data-reveal>
        {{ $title }}
    </x-ui.section-title>

    <div data-reveal class="relative" style="transition-delay: 100ms">
        <div data-carousel class="flex items-center gap-3">
            <button
                type="button"
                data-carousel-prev
                aria-label="Anterior"
                class="hidden h-11 w-11 flex-none items-center justify-center rounded-full border border-slate-800/10 bg-white shadow-[0_8px_20px_-8px_rgba(30,41,59,0.2)] sm:flex"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <ul data-carousel-track class="flex snap-x gap-5 overflow-x-auto scroll-smooth py-2">
                @foreach ($items as $item)
                    <li class="w-[280px] flex-none sm:w-[320px]">
                        <div class="flex h-full items-start gap-4 rounded-2xl border border-slate-800/10 bg-white p-6">
                            <span class="flex h-[34px] w-[34px] flex-none items-center justify-center rounded-[10px] bg-gradient-to-br from-blue-600 to-emerald-500 text-white">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </span>
                            <div>
                                <h4 class="mb-1 text-[16.5px] font-bold">{{ $item['title'] }}</h4>
                                <p class="text-[14.5px] leading-relaxed text-slate-500">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <button
                type="button"
                data-carousel-next
                aria-label="Próximo"
                class="hidden h-11 w-11 flex-none items-center justify-center rounded-full border border-slate-800/10 bg-white shadow-[0_8px_20px_-8px_rgba(30,41,59,0.2)] sm:flex"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E293B" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        @if (count($items) > 1)
            <div data-carousel-pagination class="mt-8 flex items-center justify-center gap-2">
                @foreach ($items as $index => $item)
                    <button
                        type="button"
                        data-carousel-dot="{{ $index }}"
                        aria-label="Ir para o item {{ $index + 1 }}"
                        class="values-pagination-dot {{ $index === 0 ? 'is-active' : '' }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</section>
