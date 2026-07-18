<x-layout.app :title="$vm->seo->title" :description="$vm->seo->description"
    :canonical="$vm->seo->canonical" :robots="$vm->seo->robots">
    <x-slot:jsonLd>
        <x-seo.json-ld :data="$vm->jsonLd" />
    </x-slot:jsonLd>

    <x-ui.breadcrumb :items="$vm->breadcrumbs" />

    <x-section.hero :title="$vm->h1" :subtitle="$vm->subtitle"
        :primary="['label' => $vm->ctaLabel, 'url' => route('contact.show')]" dark />

    <x-section.problem title="Sobre este serviço">
        <p>{{ $vm->intro }}</p>
    </x-section.problem>

    <x-section.benefits eyebrow="Benefícios" title="Por que escolher a OD Tech" :items="$vm->benefits" />

    @if (! empty($vm->faq))
        <x-section.faq eyebrow="Dúvidas frequentes" title="Perguntas frequentes" :items="$vm->faq" />
    @endif

    @if (! empty($vm->relatedLinks))
        <section class="bg-slate-50 px-5 py-20 sm:px-8 lg:px-14 lg:py-28">
            <div class="mx-auto max-w-6xl">
                <x-ui.section-title eyebrow="Continue explorando" class="mb-10">Você também pode se interessar</x-ui.section-title>

                <div class="flex flex-wrap gap-3">
                    @foreach ($vm->relatedLinks as $link)
                        <a href="{{ $link['url'] }}" class="rounded-full border border-slate-800/10 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:text-blue-600">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-section.cta title="Vamos construir seu próximo produto digital?"
        :button="['label' => $vm->ctaLabel, 'url' => route('contact.show')]" />
</x-layout.app>
