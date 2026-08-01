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

    <x-section.benefits eyebrow="Benefícios" title="Por que escolher a OD Tec" :items="$vm->benefits" />

    @if (! empty($vm->faq))
        <x-section.faq eyebrow="Dúvidas frequentes" title="Perguntas frequentes" :items="$vm->faq" />
    @endif

    <x-section.related-links :links="$vm->relatedLinks" />

    <x-section.cta title="Vamos construir seu próximo produto digital?"
        :button="['label' => $vm->ctaLabel, 'url' => route('contact.show')]" />
</x-layout.app>
