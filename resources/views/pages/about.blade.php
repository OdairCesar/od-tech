<x-layout.app title="Sobre a OD Tec" description="Tecnologia deve resolver problemas reais — não só gerar mais sistemas para administrar.">
    <x-ui.breadcrumb :items="[
        ['label' => 'Início', 'url' => route('home')],
        ['label' => 'Sobre'],
    ]" />

    <section class="px-5 py-20 sm:px-8 lg:px-14 lg:py-28">
        <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-2 lg:items-start">
            <div>
                <x-ui.section-title eyebrow="Sobre a OD Tec">Tecnologia deve resolver problemas reais</x-ui.section-title>
                <p class="mt-5 text-base leading-relaxed text-slate-500">
                    Desenvolvemos MVPs, aplicações web, aplicativos, automações e soluções sob medida
                    que ajudam empresas a validar ideias, otimizar processos e crescer com segurança.
                </p>
                <p class="mt-4 text-base leading-relaxed text-slate-500">
                    Um bom projeto começa antes da primeira linha de código. Por isso trabalhamos lado
                    a lado com nossos clientes, entendendo desafios e discutindo alternativas para que
                    cada decisão seja tomada com clareza.
                </p>
            </div>

            <div class="flex flex-col gap-5">
                <div class="rounded-2xl bg-slate-800 p-8 text-white">
                    <div class="mb-3 text-sm font-bold tracking-wide text-emerald-300 uppercase">Missão</div>
                    <p class="text-[15px] leading-relaxed text-white/85">
                        Transformar ideias em soluções digitais de forma rápida, transparente e
                        estratégica.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-800/10 bg-slate-50 p-8">
                    <div class="mb-3 text-sm font-bold tracking-wide text-blue-600 uppercase">Visão</div>
                    <p class="text-[15px] leading-relaxed text-slate-500">
                        Ser referência em nossa região no desenvolvimento de MVPs e soluções digitais.
                    </p>
                </div>
            </div>
        </div>
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

    <x-section.process eyebrow="Como trabalhamos" title="Um processo simples, do briefing ao lançamento" :steps="[
        ['n' => '01', 'title' => 'Descoberta', 'desc' => 'Entendemos seu negócio, objetivos e o problema a resolver.'],
        ['n' => '02', 'title' => 'Design & Arquitetura', 'desc' => 'Planejamos a solução técnica e a experiência do usuário.'],
        ['n' => '03', 'title' => 'Desenvolvimento', 'desc' => 'Construímos com entregas incrementais e comunicação constante.'],
        ['n' => '04', 'title' => 'Lançamento & Suporte', 'desc' => 'Colocamos no ar e seguimos por perto após o lançamento.'],
    ]" />

    <x-section.manifesto />

    <x-section.cta title="Vamos construir seu próximo produto digital?"
        description="Conte um pouco sobre a sua empresa e o que você precisa — a resposta é rápida e sem compromisso."
        :button="['label' => 'Falar com a OD Tec', 'url' => route('contact.show')]" />
</x-layout.app>
