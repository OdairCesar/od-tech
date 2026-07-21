<x-layout.app title="Fale com a OD Tec" description="Conte um pouco sobre a sua empresa e o que você precisa — a resposta é rápida e sem compromisso.">
    <x-ui.breadcrumb :items="[
        ['label' => 'Início', 'url' => route('home')],
        ['label' => 'Contato'],
    ]" />

    <section class="px-5 py-20 sm:px-8 lg:px-14 lg:py-28">
        <div class="mx-auto max-w-xl">
            <x-ui.section-title as="h1" eyebrow="Contato" class="mb-10">Vamos construir seu próximo produto digital?</x-ui.section-title>

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="flex flex-col gap-5">
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-800">Nome</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                        class="w-full rounded-xl border border-slate-800/15 px-4 py-3 text-[15px]">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-800">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-slate-800/15 px-4 py-3 text-[15px]">
                    @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-800">Telefone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                        class="w-full rounded-xl border border-slate-800/15 px-4 py-3 text-[15px]">
                    @error('phone') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company" class="mb-1.5 block text-sm font-semibold text-slate-800">Empresa</label>
                    <input id="company" name="company" type="text" value="{{ old('company') }}"
                        class="w-full rounded-xl border border-slate-800/15 px-4 py-3 text-[15px]">
                    @error('company') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="message" class="mb-1.5 block text-sm font-semibold text-slate-800">Mensagem</label>
                    <textarea id="message" name="message" rows="5" required
                        class="w-full rounded-xl border border-slate-800/15 px-4 py-3 text-[15px]">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="inline-block rounded-full bg-gradient-to-br from-blue-600 via-emerald-500 to-blue-600 px-8 py-4 text-center text-base font-bold text-white">
                    Enviar mensagem
                </button>
            </form>
        </div>
    </section>
</x-layout.app>
