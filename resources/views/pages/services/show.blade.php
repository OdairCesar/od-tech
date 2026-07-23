<x-layout.app :title="$vm->seo->title" :description="$vm->seo->description" :canonical="$vm->seo->canonical" :robots="$vm->seo->robots">
    <x-slot:jsonLd>
        <x-seo.json-ld :data="$vm->jsonLd" />
    </x-slot:jsonLd>

    <x-ui.breadcrumb :items="$vm->breadcrumbs" />

    <x-section.hero :eyebrow="'Serviço'" :title="$vm->title" :subtitle="$vm->subtitle"
        :primary="['label' => 'Solicitar orçamento', 'url' => route('contact.show')]" dark>
        @if ($vm->heroImageUrl)
            <img src="{{ $vm->heroImageUrl }}" alt="{{ $vm->title }}" class="h-full w-full rounded-3xl object-cover" />
        @endif
    </x-section.hero>

    <x-section.problem :title="'Sobre este serviço'">
        <p>{{ $vm->description }}</p>
    </x-section.problem>

    <x-section.benefits eyebrow="Benefícios" :title="'Por que escolher a OD Tec'" :items="$vm->benefits" />

    @if (! empty($vm->faq))
        <x-section.faq eyebrow="Dúvidas frequentes" title="Perguntas frequentes" :items="$vm->faq" />
    @endif

    <x-section.cta title="Vamos construir seu próximo produto digital?"
        :button="['label' => 'Falar com a OD Tec', 'url' => route('contact.show')]" />
</x-layout.app>
