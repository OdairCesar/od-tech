<footer class="border-t border-slate-800/10 bg-slate-50 px-5 py-10 sm:px-8 sm:py-14 min-[960px]:px-14">
    <div class="grid gap-10 min-[640px]:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <div class="mb-3 flex items-center gap-2">
                <svg width="24" height="24" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="32" r="26" stroke="#1E293B" stroke-width="7"/>
                    <path d="M32,6 C43,6 43,58 32,58 C25,58 21,50 21,32 C21,14 25,6 32,6 Z" fill="#1E293B"/>
                </svg>
                <span class="text-lg font-extrabold tracking-tight text-slate-800">OD <span class="font-medium">Tech</span></span>
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
                <a href="{{ route('contact.show') }}" class="text-[14.5px] text-slate-800 hover:text-blue-600">Fale com a gente</a>
                <span class="text-[14.5px] text-slate-500">Brasil</span>
            </div>
        </div>

        <div class="border-t border-slate-800/10 pt-6 text-[13px] text-slate-400 min-[640px]:col-span-3">
            &copy; {{ now()->year }} OD Tech. Todos os direitos reservados.
        </div>
    </div>
</footer>
