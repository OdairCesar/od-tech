<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\State;
use App\Services\Seo\ContentComposer;
use App\Services\Seo\StructuredDataService;
use App\Services\Tools\ToolDefinition;
use App\Services\Tools\ToolRegistry;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /** @var list<string> */
    private const SERVICE_COLORS = ['bg-blue-600', 'bg-slate-800', 'bg-emerald-600', 'bg-blue-700', 'bg-emerald-700', 'bg-slate-700'];

    public function __construct(
        private readonly ContentComposer $composer,
        private readonly StructuredDataService $structuredData,
        private readonly ToolRegistry $toolRegistry,
    ) {}

    public function index(): View
    {
        $services = Service::query()->active()->orderBy('name')->take(6)->get()
            ->values()
            ->map(fn (Service $service, int $index): array => [
                'title' => $service->title,
                'desc' => $this->composer->compose($service->subtitle),
                'icon' => $service->icon,
                'bg' => self::SERVICE_COLORS[$index % count(self::SERVICE_COLORS)],
                'url' => route('services.show', $service),
            ]);

        $states = State::query()->active()
            ->with(['cities' => fn ($query) => $query->active()->orderByDesc('population')])
            ->orderBy('name')
            ->get()
            ->filter(fn (State $state): bool => $state->cities->isNotEmpty())
            ->values();

        $tools = collect($this->toolRegistry->all())
            ->map(fn (ToolDefinition $tool): array => [
                'title' => $tool->title,
                'tagline' => $tool->tagline,
                'url' => route('tools.show', $tool->slug),
            ]);

        return view('pages.home', [
            'services' => $services,
            'states' => $states,
            'tools' => $tools,
            'jsonLd' => [$this->structuredData->organization()],
        ]);
    }
}
