<?php

namespace App\Actions\ServiceCluster;

use App\Models\ServiceCluster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class GenerateUniqueServiceClusterSlug
{
    public function __invoke(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (
            ServiceCluster::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn (Builder $query): Builder => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
