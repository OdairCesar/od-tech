<x-layout.app title="Serviços — OD Tec" description="Conheça os serviços de tecnologia da OD Tec: sites, sistemas web, aplicativos, APIs e muito mais.">
    <x-slot:jsonLd>
        <x-seo.json-ld :data="$jsonLd" />
    </x-slot:jsonLd>

    <x-ui.breadcrumb :items="$breadcrumbs" />

    <section class="px-5 py-20 sm:px-8 lg:px-14 lg:py-28">
        <div class="mx-auto max-w-6xl">
            <x-ui.section-title eyebrow="Serviços" class="mb-10">Soluções completas para levar sua empresa ao digital</x-ui.section-title>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <x-ui.card>
                        <x-ui.icon-badge :icon="$service['icon']" bg="bg-blue-600" />
                        <h3 class="text-lg font-bold">{{ $service['title'] }}</h3>
                        <p class="text-[15px] leading-relaxed text-slate-500">{{ $service['subtitle'] }}</p>
                        <a href="{{ $service['url'] }}" class="text-sm font-bold text-blue-600">Saiba mais &rarr;</a>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>
</x-layout.app>
