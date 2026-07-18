<x-layout.app title="OD Tech — Sites, sistemas e aplicativos sob medida" description="Desenvolvemos MVPs, sites, sistemas e aplicativos que ajudam sua empresa a validar ideias, automatizar processos e crescer com segurança.">
    <x-slot:jsonLd>
        <x-seo.json-ld :data="$jsonLd" />
    </x-slot:jsonLd>

    <x-section.hero
        eyebrow="Ideias que evoluem para soluções"
        title="Tecnologia que tira sua empresa do papel e coloca no mercado"
        subtitle="Desenvolvemos MVPs, sites, sistemas e aplicativos que ajudam sua empresa a validar ideias, automatizar processos e crescer com segurança."
        :primary="['label' => 'Solicitar orçamento', 'url' => route('contact.show')]"
        :secondary="['label' => 'Ver serviços', 'url' => route('services.index')]"
        dark
    >
        <img src="{{ asset('imgs/imagem-hero.png') }}" alt="OD Tech" class="h-full w-full rounded-3xl object-cover" />
    </x-section.hero>

    <x-section.problem id="sobre" eyebrow="Sobre a OD Tech" title="Tecnologia deve resolver problemas reais — não só gerar mais sistemas para administrar">
        <p>Desenvolvemos MVPs, aplicações web, aplicativos, automações e soluções sob medida que ajudam empresas a validar ideias, otimizar processos e crescer com segurança.</p>
        <p>Um bom projeto começa antes da primeira linha de código. Por isso trabalhamos lado a lado com nossos clientes, entendendo desafios e discutindo alternativas para que cada decisão seja tomada com clareza.</p>

        <x-slot:aside>
            <div class="rounded-[20px] bg-slate-800 p-8 text-white">
                <div class="mb-3 text-sm font-bold tracking-wide text-emerald-300 uppercase">Missão</div>
                <p class="text-[15px] leading-relaxed text-white/85">Transformar ideias em soluções digitais de forma rápida, transparente e estratégica — desenvolvendo MVPs e softwares que validam negócios, automatizam processos e geram resultados reais.</p>
            </div>
            <div class="rounded-[20px] border border-slate-800/10 bg-slate-50 p-8">
                <div class="mb-3 text-sm font-bold tracking-wide text-blue-600 uppercase">Visão</div>
                <p class="text-[15px] leading-relaxed text-slate-500">Ser referência em nossa região no desenvolvimento de MVPs e soluções digitais, reconhecida pela transparência e pela capacidade de transformar ideias em produtos escaláveis.</p>
            </div>
        </x-slot:aside>
    </x-section.problem>

    <x-section.services-grid id="servicos" eyebrow="Serviços" title="Soluções completas para levar sua empresa ao digital" :items="$services" />

    <x-section.process id="processo" eyebrow="Como trabalhamos" title="Um processo simples, do briefing ao lançamento" :steps="[
        ['n' => '01', 'title' => 'Descoberta', 'desc' => 'Entendemos seu negócio, objetivos e o problema a resolver.'],
        ['n' => '02', 'title' => 'Design & Arquitetura', 'desc' => 'Planejamos a solução técnica e a experiência do usuário.'],
        ['n' => '03', 'title' => 'Desenvolvimento', 'desc' => 'Construímos com entregas incrementais e comunicação constante.'],
        ['n' => '04', 'title' => 'Lançamento & Suporte', 'desc' => 'Colocamos no ar e seguimos por perto após o lançamento.'],
    ]" />

    <section class="px-5 py-16 text-center sm:px-8 sm:py-[80px] min-[960px]:px-14" data-reveal>
        <h2 class="mb-3.5 text-[28px] font-extrabold tracking-tight sm:text-[32px]">Faça pensando. Pense fazendo.</h2>
        <p class="text-base text-slate-500">Errar cedo custa menos. Aprender cedo vale mais.</p>
    </section>

    <x-section.values eyebrow="Nossos valores" title="Perto do seu negócio, do início ao pós-lançamento" description="Sem intermediários e sem burocracia: você conversa direto com quem escreve o código, entende o que está sendo construído e recebe atualizações reais a cada etapa." :items="[
        ['title' => 'Transparência', 'desc' => 'O cliente acompanha cada etapa do projeto e participa das decisões importantes.'],
        ['title' => 'Honestidade', 'desc' => 'Sempre recomendamos a solução que faz mais sentido para o negócio, mesmo que não seja a mais cara.'],
        ['title' => 'Qualidade', 'desc' => 'Desenvolvemos soluções pensando em desempenho, manutenção e evolução.'],
        ['title' => 'Ética', 'desc' => 'Construímos relações baseadas em respeito e confiança.'],
        ['title' => 'Compromisso', 'desc' => 'Assumimos responsabilidade pelos resultados e cumprimos o que prometemos.'],
        ['title' => 'Foco no cliente', 'desc' => 'Antes de escrever código, buscamos entender o problema.'],
        ['title' => 'Colaboração', 'desc' => 'As melhores soluções surgem quando cliente e equipe trabalham juntos.'],
    ]" />

    <x-section.manifesto />

    <x-section.projects id="trabalhos" eyebrow="Trabalhos" title="Tipos de projeto que já entregamos" :items="[
        ['title' => 'Site institucional', 'desc' => 'Presença digital completa para marcas e negócios locais.', 'url' => route('services.show', 'criacao-de-sites'), 'image' => 'imgs/card-site.png'],
        ['title' => 'Sistemas Web', 'desc' => 'Plataformas de gestão sob medida para operações internas.', 'url' => route('services.show', 'desenvolvimento-de-sistemas-web'), 'image' => 'imgs/card-web.png'],
        ['title' => 'Aplicativo mobile', 'desc' => 'Apps nativos e híbridos, do zero até a loja de aplicativos.', 'url' => route('services.show', 'desenvolvimento-de-app'), 'image' => 'imgs/card-apps.png'],
    ]" />

    @if ($cities->isNotEmpty())
        <section class="bg-slate-50 px-5 py-16 sm:px-8 sm:py-[110px] min-[960px]:px-14">
            <div class="mx-auto max-w-[1180px]">
                <x-ui.section-title eyebrow="Cidades atendidas" titleClass="text-[28px] sm:text-[38px]" class="mb-10" data-reveal>Onde já estamos presentes</x-ui.section-title>

                <div class="flex flex-wrap gap-3">
                    @foreach ($cities as $city)
                        <a href="{{ route('cities.show', $city) }}" class="rounded-full border border-slate-800/10 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:text-blue-600">
                            {{ $city->name }}/{{ $city->uf }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-section.cta title="Vamos construir seu próximo produto digital?"
        description="Conte um pouco sobre a sua empresa e o que você precisa — a resposta é rápida e sem compromisso."
        :button="['label' => 'Falar com a OD Tech', 'url' => route('contact.show')]" />
</x-layout.app>
