<?php

namespace App\Models;

use App\Enums\PageStatus;
use Database\Factories\PortfolioItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property PageStatus $status
 * @property-read Service|null $service
 */
class PortfolioItem extends Model
{
    /** @use HasFactory<PortfolioItemFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'service_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'external_url',
        'meta_title',
        'meta_description',
        'canonical',
        'robots',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @param  Builder<PortfolioItem>  $query
     * @return Builder<PortfolioItem>
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
