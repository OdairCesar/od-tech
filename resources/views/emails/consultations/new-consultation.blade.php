<x-mail::message>
# Nova consultoria de IA concluída

**Nome:** {{ $consultation->name }}

**E-mail:** {{ $consultation->email }}

@if ($consultation->phone)
**Telefone:** {{ $consultation->phone }}
@endif

@if ($consultation->report)
**Resumo executivo:**

{{ $consultation->report['executive_summary'] }}

**Complexidade:** {{ $consultation->report['complexity'] }}

**Estimativa:** {{ $consultation->report['estimate_timeframe'] }} — {{ $consultation->report['estimate_investment'] }}
@endif

O relatório completo pode ser consultado no painel administrativo, em Consultorias de IA.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
