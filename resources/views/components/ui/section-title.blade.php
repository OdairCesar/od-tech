@props([
    'eyebrow' => null,
    'description' => null,
    'titleClass' => 'text-[28px] sm:text-[34px]',
])

<div {{ $attributes->class(['max-w-xl']) }}>
    @if ($eyebrow)
        <div class="mb-3.5 text-sm font-bold tracking-wide text-blue-600 uppercase">{{ $eyebrow }}</div>
    @endif

    <h2 class="{{ $titleClass }} font-extrabold tracking-tight text-balance">{{ $slot }}</h2>

    @if ($description)
        <p class="mt-5 text-base leading-relaxed text-slate-500 md:text-[16.5px]">{{ $description }}</p>
    @endif
</div>
