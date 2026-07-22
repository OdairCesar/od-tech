@props([
    'href' => '#',
    'variant' => 'primary',
    'newTab' => false,
    'gaEvent' => null,
    'gaPayload' => null,
])

@php
    $variants = [
        'primary' => 'text-white bg-gradient-to-br from-blue-600 via-emerald-500 to-blue-600 bg-[length:200%_200%] bg-left transition-[background-position_.4s_ease,transform_.25s_ease,box-shadow_.25s_ease] hover:bg-right hover:-translate-y-0.5 hover:shadow-[0_12px_28px_-10px_rgba(37,99,235,0.45)]',
        'outline-light' => 'text-slate-800 border border-slate-800/20 transition-transform duration-200 hover:-translate-y-0.5',
        'outline-dark' => 'text-white border border-white/30 transition-transform duration-200 hover:-translate-y-0.5',
    ];
@endphp

<a
    href="{{ $href }}"
    @if ($newTab) target="_blank" rel="noopener noreferrer" @endif
    @if ($gaEvent) data-ga-event="{{ $gaEvent }}" @endif
    @if ($gaPayload) data-ga-payload="{{ json_encode($gaPayload) }}" @endif
    {{ $attributes->class(['inline-block rounded-full px-8 py-4 text-base font-bold text-center', $variants[$variant] ?? $variants['primary']]) }}
>{{ $slot }}</a>
