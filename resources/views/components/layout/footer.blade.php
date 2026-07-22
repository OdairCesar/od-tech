<footer class="border-t border-slate-800/10 bg-slate-50 px-5 py-10 sm:px-8 sm:py-14 min-[960px]:px-14">
    <div class="grid gap-10 min-[640px]:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <div class="mb-3">
                <img src="{{ asset('imgs/logo.png') }}" alt="OD Tec" class="h-6 w-auto">
            </div>
            <p class="mb-3 max-w-xs text-sm leading-relaxed text-slate-500">Criação de sites, sistemas e aplicativos sob medida para empresas que querem crescer no digital.</p>
            <p class="text-[13.5px] font-bold text-blue-600">Ideias que evoluem para soluções.</p>
        </div>

        <div>
            <div class="mb-3.5 text-sm font-bold tracking-wide text-slate-500 uppercase">Navegação</div>
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('services.index') }}" class="text-[14.5px] text-slate-800 hover:text-blue-600">Serviços</a>
                <a href="{{ route('home') }}#processo" class="text-[14.5px] text-slate-800 hover:text-blue-600">Processo</a>
                <a href="{{ route('home') }}#trabalhos" class="text-[14.5px] text-slate-800 hover:text-blue-600">Trabalhos</a>
                <a href="{{ route('cities.index') }}" class="text-[14.5px] text-slate-800 hover:text-blue-600">Cidades</a>
            </div>
        </div>

        <div>
            <div class="mb-3.5 text-sm font-bold tracking-wide text-slate-500 uppercase">Contato</div>
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('contact.show') }}" class="text-[14.5px] text-slate-800 hover:text-blue-600" data-ga-event="cta_click" data-ga-payload="{{ json_encode(['location' => 'footer', 'label' => 'Fale com a gente']) }}">Fale com a gente</a>
                <a href="mailto:contato@odairferreira.com" class="text-[14.5px] text-slate-800 hover:text-blue-600" data-ga-event="email_click">contato@odairferreira.com</a>
                <a href="https://wa.me/5514991434273" target="_blank" rel="noopener" class="text-[14.5px] text-slate-800 hover:text-blue-600" data-ga-event="whatsapp_click">WhatsApp: (14) 99143-4273</a>
                <span class="text-[14.5px] text-slate-500">CNPJ: 53.487.318/0001-05</span>
            </div>
        </div>

        <div class="border-t border-slate-800/10 pt-6 text-[13px] text-slate-400 min-[640px]:col-span-3">
            &copy; {{ now()->year }} OD Tec. Todos os direitos reservados.
        </div>
    </div>
</footer>
