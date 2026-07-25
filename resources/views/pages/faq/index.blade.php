<x-layout.app title="Perguntas frequentes — OD Tec" description="Tire suas dúvidas sobre os serviços de tecnologia da OD Tec: prazos, orçamento, suporte e muito mais.">
    <x-slot:jsonLd>
        <x-seo.json-ld :data="$jsonLd" />
    </x-slot:jsonLd>

    <x-ui.breadcrumb :items="$breadcrumbs" />

    <section class="px-5 pt-20 sm:px-8 lg:px-14 lg:pt-28">
        <div class="mx-auto max-w-3xl">
            <x-ui.section-title as="h1" eyebrow="Dúvidas frequentes">Perguntas frequentes</x-ui.section-title>
        </div>
    </section>

    @foreach ($groups as $group)
        <x-section.faq :eyebrow="$group['title']" :title="'Sobre ' . $group['title']" :items="$group['faq']" />
    @endforeach

    <x-section.cta title="Ainda tem alguma dúvida?"
        description="Fale com a gente — a resposta é rápida e sem compromisso."
        :button="['label' => 'Falar com a OD Tec', 'url' => route('contact.show')]" />
</x-layout.app>
