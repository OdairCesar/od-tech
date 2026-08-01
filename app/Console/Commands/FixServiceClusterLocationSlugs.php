<?php

namespace App\Console\Commands;

use App\Actions\ServiceCluster\GenerateUniqueServiceClusterSlug;
use App\Models\ServiceCluster;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('app:fix-service-cluster-location-slugs {--dry-run : List the clusters that would change without saving anything} {--force : Skip the confirmation prompt}')]
#[Description('Regenerate service cluster slugs that were built from a title still containing {cidade}/{uf}/{regiao} tokens')]
class FixServiceClusterLocationSlugs extends Command
{
    public function handle(GenerateUniqueServiceClusterSlug $generateSlug): int
    {
        $clusters = ServiceCluster::query()
            ->where(function ($query) {
                $query->where('title', 'like', '%{cidade}%')
                    ->orWhere('title', 'like', '%{uf}%')
                    ->orWhere('title', 'like', '%{regiao}%');
            })
            ->get();

        if ($clusters->isEmpty()) {
            $this->info('No service cluster titles contain location tokens.');

            return self::SUCCESS;
        }

        $candidates = $clusters->filter(fn (ServiceCluster $cluster): bool => Str::slug($cluster->title) === $cluster->slug);

        $skipped = $clusters->count() - $candidates->count();

        if ($skipped > 0) {
            $this->warn("Skipping {$skipped} cluster(s) whose slug no longer matches Str::slug(title) — looks manually edited already.");
        }

        if ($candidates->isEmpty()) {
            $this->info('Nothing to fix.');

            return self::SUCCESS;
        }

        $rows = $candidates->map(function (ServiceCluster $cluster) use ($generateSlug): array {
            $newSlug = $generateSlug($this->stripLocationTokens($cluster->title), ignoreId: $cluster->id);

            return [$cluster->id, $cluster->title, $cluster->slug, $newSlug];
        });

        $this->table(['ID', 'Title', 'Current slug', 'New slug'], $rows->all());

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Apply the new slugs above? Existing links to the current slugs will 404.')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        foreach ($rows as [$id, , , $newSlug]) {
            ServiceCluster::query()->whereKey($id)->update(['slug' => $newSlug]);
        }

        $this->info("Updated {$rows->count()} cluster slug(s).");

        return self::SUCCESS;
    }

    private function stripLocationTokens(string $title): string
    {
        $stripped = str_replace(['{cidade}', '{uf}', '{regiao}'], '', $title);
        $stripped = preg_replace('/\s+(em|na|no|nas|nos|de|do|da|para)\s*$/iu', '', $stripped);
        $stripped = trim((string) $stripped);

        return $stripped !== '' ? $stripped : $title;
    }
}
