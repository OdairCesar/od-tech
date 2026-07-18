<?php

namespace App\Models;

use App\Enums\PageStatus;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $benefits
 * @property array<int, array{question: string, answer: string}> $faq
 * @property array<int, string> $keywords
 * @property PageStatus $status
 */
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'slug',
        'name',
        'title',
        'subtitle',
        'description',
        'benefits',
        'faq',
        'keywords',
        'hero_image',
        'icon',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'faq' => 'array',
            'keywords' => 'array',
            'status' => PageStatus::class,
        ];
    }

    /**
     * @return HasMany<LandingPage, $this>
     */
    public function landingPages(): HasMany
    {
        return $this->hasMany(LandingPage::class);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
