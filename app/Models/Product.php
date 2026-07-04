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
        'gender',
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
            'fashion' => 'Fashion',
            'jewellery' => 'Jewellery',
            'gadgets' => 'Gadgets',
        ];
    }

    /**
     * Who a product is aimed at — used as a shop filter facet. Products with
     * a null/'unisex' gender are shown regardless of the active gender filter.
     *
     * @return array<string, string>
     */
    public static function genders(): array
    {
        return [
            'male' => 'Men',
            'female' => 'Women',
            'kids' => 'Kids',
            'unisex' => 'Unisex',
        ];
    }
}
