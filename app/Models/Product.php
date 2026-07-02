<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'price',
        'image_url',
        'product_url',
        'emoji',
        'accent',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Use the slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Curated catalog categories — keys are stored, values are display labels.
     *
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'wedding' => 'Wedding gifts',
            'birthday' => 'Birthday gifts',
            'baby' => 'Baby gifts',
            'home' => 'Home & living',
            'experiences' => 'Experiences',
        ];
    }
}
