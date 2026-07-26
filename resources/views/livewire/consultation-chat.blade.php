<div class="flex h-full min-h-0 flex-1 flex-col">
    @if ($step === 'chat')
        <div class="flex min-h-0 flex-1 flex-col gap-4">
            <div
                x-data
                x-init="
                    $el.scrollTop = $el.scrollHeight;
                    new MutationObserver(() => { $el.scrollTop = $el.scrollHeight }).observe($el, { childList: true, subtree: true });
                "
                class="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto rounded-2xl border border-slate-800/10 bg-white p-5"
            >
                @foreach ($chatLog as $index => $entry)
                    <div wire:key="log-{{ $index }}" class="flex {{ $entry['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-3 text-[15px] leading-relaxed {{ $entry['role'] === 'user' ? 'bg-blue-600 text-white' : 'border border-slate-800/10 bg-white text-slate-800' }}">
                            {{ $entry['content'] }}
                        </div>
                    </div>
                @endforeach

                @if ($consultation)
                    @foreach ($consultation->messages as $index => $entry)
                        <div wire:key="ai-{{ $index }}" class="flex {{ $entry['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-2xl px-4 py-3 text-[15px] leading-relaxed {{ $entry['role'] === 'user' ? 'bg-blue-600 text-white' : 'border border-slate-800/10 bg-white text-slate-800' }}">
                                {{ $entry['content'] }}
                            </div>
                        </div>
                    @endforeach
                @endif

                <div wire:loading wire:target="submitInput" class="flex justify-start">
                    <div class="rounded-2xl border border-slate-800/10 bg-white px-4 py-3 text-[15px] text-slate-400">
                        digitando...
                    </div>
                </div>
            </div>

            @if ($stage === 'project_type')
                <div class="flex flex-wrap gap-2">
                    @foreach ($projectTypeOptions as $option)
                        <button
                            type="button"
                            wire:click="selectProjectType('{{ $option }}')"
                            wire:loading.attr="disabled"
                            wire:target="selectProjectType"
                            class="rounded-full border border-slate-800/15 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:border-blue-600 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ $option }}
                        </button>
                    @endforeach

                    <button
                        type="button"
                        wire:click="selectProjectType('Outro')"
                        wire:loading.attr="disabled"
                        wire:target="selectProjectType"
                        class="rounded-full border border-slate-800/15 px-4 py-2 text-sm font-semibold text-slate-800 transition-colors hover:border-blue-600 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        Outro
                    </button>
                </div>
            @else
                <form wire:submit="submitInput" class="flex gap-3">
                    <input
                        wire:model="input"
                        wire:loading.attr="readonly"
                        wire:target="submitInput"
                        type="{{ $stage === 'email' ? 'email' : ($stage === 'phone' ? 'tel' : 'text') }}"
                        placeholder="Digite sua resposta..."
                        class="w-full rounded-xl border border-slate-800/15 px-4 py-3 text-[15px]"
                    >
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submitInput"
                        class="inline-block shrink-0 rounded-full bg-gradient-to-br from-blue-600 via-emerald-500 to-blue-600 px-6 py-3 text-center text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        Enviar
                    </button>
                </form>
            @endif

            @error('input') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @elseif ($step === 'generating')
        <div wire:poll.3s="pollReportStatus" class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-slate-800/10 bg-white px-6 py-16 text-center">
            <p class="text-lg font-semibold text-slate-800">Estamos preparando sua análise...</p>
            <p class="mt-2 text-[15px] text-slate-500">Isso leva menos de um minuto. Não feche esta página.</p>
        </div>
    @elseif ($step === 'report')
        @php $report = $consultation->report; @endphp
        <div x-data="{ expanded: false }" class="flex min-h-0 flex-1 flex-col gap-8 overflow-y-auto py-2">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Sua análise está pronta!</h2>
                <p class="mt-1 text-[15px] text-slate-500">Aqui está a estimativa inicial do seu projeto.</p>
            </div>

            <div class="rounded-2xl border border-blue-600/20 bg-blue-600/5 p-6">
                <h2 class="mb-3 text-lg font-bold text-slate-800">Estimativa inicial</h2>
                <p class="text-[15px] text-slate-600"><strong>Prazo total:</strong> {{ $report['estimate_timeframe'] }}</p>
                <p class="mt-1 text-[15px] text-slate-600"><strong>Investimento total:</strong> {{ $report['estimate_investment'] }}</p>

                @if (count($report['delivery_stages']) > 0)
                    <div class="mt-4 space-y-3 border-t border-blue-600/10 pt-4">
                        <p class="text-xs font-bold tracking-wide text-blue-600 uppercase">Etapas de entrega</p>

                        @foreach ($report['delivery_stages'] as $stage)
                            <div wire:key="estimate-stage-{{ $loop->index }}" class="rounded-xl bg-white/60 p-3">
                                <p class="text-sm font-semibold text-slate-800">{{ $loop->iteration }}. {{ $stage['name'] }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $stage['description'] }}</p>
                                <p class="mt-2 text-xs text-slate-500">
                                    <strong>Prazo:</strong> {{ $stage['timeframe'] }} &middot; <strong>Investimento:</strong> {{ $stage['investment'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <p class="mt-4 text-xs text-slate-500">Esta é uma estimativa inicial, sujeita a ajustes após uma análise técnica detalhada.</p>
            </div>

            <button
                type="button"
                x-show="!expanded"
                x-on:click="expanded = true"
                class="inline-block self-start rounded-full border border-slate-800/15 px-6 py-3 text-center text-sm font-bold text-slate-800 transition-colors hover:border-blue-600"
            >
                Ver relatório completo
            </button>

            <div x-show="expanded" x-transition class="flex flex-col gap-8">
                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">Resumo executivo</h2>
                    <p class="text-[15px] leading-relaxed text-slate-600">{{ $report['executive_summary'] }}</p>
                </div>

                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">Problema identificado</h2>
                    <p class="text-[15px] leading-relaxed text-slate-600">{{ $report['problem'] }}</p>
                </div>

                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">Objetivos do projeto</h2>
                    <ul class="list-disc space-y-1 pl-5 text-[15px] leading-relaxed text-slate-600">
                        @foreach ($report['objectives'] as $objective)
                            <li wire:key="objective-{{ $loop->index }}">{{ $objective }}</li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="mb-3 text-lg font-bold text-slate-800">Funcionalidades</h2>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="mb-1.5 text-xs font-bold tracking-wide text-blue-600 uppercase">Essenciais</p>
                            <ul class="list-disc space-y-1 pl-5 text-sm text-slate-600">
                                @foreach ($report['features_essential'] as $feature)
                                    <li wire:key="feature-essential-{{ $loop->index }}">{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <p class="mb-1.5 text-xs font-bold tracking-wide text-blue-600 uppercase">Recomendadas</p>
                            <ul class="list-disc space-y-1 pl-5 text-sm text-slate-600">
                                @foreach ($report['features_recommended'] as $feature)
                                    <li wire:key="feature-recommended-{{ $loop->index }}">{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <p class="mb-1.5 text-xs font-bold tracking-wide text-blue-600 uppercase">Futuras</p>
                            <ul class="list-disc space-y-1 pl-5 text-sm text-slate-600">
                                @foreach ($report['features_future'] as $feature)
                                    <li wire:key="feature-future-{{ $loop->index }}">{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">Perfis de usuários</h2>
                    <p class="text-[15px] leading-relaxed text-slate-600">{{ implode(', ', $report['user_profiles']) }}</p>
                </div>

                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">Fluxo do processo</h2>
                    <p class="text-[15px] leading-relaxed text-slate-600"><strong>Hoje:</strong> {{ $report['current_flow'] }}</p>
                    <p class="mt-2 text-[15px] leading-relaxed text-slate-600"><strong>Depois:</strong> {{ $report['desired_flow'] }}</p>
                </div>

                @if (count($report['integrations']) > 0)
                    <div>
                        <h2 class="mb-2 text-lg font-bold text-slate-800">Integrações sugeridas</h2>
                        <p class="text-[15px] leading-relaxed text-slate-600">{{ implode(', ', $report['integrations']) }}</p>
                    </div>
                @endif

                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">Complexidade</h2>
                    <p class="text-[15px] leading-relaxed text-slate-600">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $report['complexity'])) }}</span>
                    </p>
                    <p class="mt-2 text-[15px] leading-relaxed text-slate-600">{{ $report['complexity_justification'] }}</p>
                </div>

                <div>
                    <h2 class="mb-2 text-lg font-bold text-slate-800">MVP sugerido</h2>
                    <p class="text-[15px] leading-relaxed text-slate-600">{{ $report['mvp'] }}</p>
                </div>

                @if (count($report['next_phases']) > 0)
                    <div>
                        <h2 class="mb-2 text-lg font-bold text-slate-800">Próximas fases</h2>
                        <ul class="list-disc space-y-1 pl-5 text-[15px] leading-relaxed text-slate-600">
                            @foreach ($report['next_phases'] as $phase)
                                <li wire:key="next-phase-{{ $loop->index }}">{{ $phase }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (count($report['open_questions']) > 0)
                    <div>
                        <h2 class="mb-2 text-lg font-bold text-slate-800">Pontos que ainda precisam ser esclarecidos</h2>
                        <ul class="list-disc space-y-1 pl-5 text-[15px] leading-relaxed text-slate-600">
                            @foreach ($report['open_questions'] as $question)
                                <li wire:key="open-question-{{ $loop->index }}">{{ $question }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-ui.button :href="route('contact.show')" variant="primary" class="self-start">Quero conversar com a equipe</x-ui.button>
            </div>
        </div>
    @elseif ($step === 'failed')
        <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-red-600/20 bg-red-600/5 px-6 py-10 text-center">
            <p class="text-lg font-semibold text-slate-800">Não conseguimos gerar sua análise agora.</p>
            <p class="mt-2 text-[15px] text-slate-600">Por favor, fale diretamente com nossa equipe que damos continuidade manualmente.</p>
            <x-ui.button :href="route('contact.show')" variant="primary" class="mt-6">Falar com a equipe</x-ui.button>
        </div>
    @endif
</div>
