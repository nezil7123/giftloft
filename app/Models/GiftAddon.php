<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'description',
        'image',
        'price',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function orderAddons()
    {
        return $this->hasMany(OrderAddon::class);
    }

    /**
     * Gift customization categories — keys are stored, values are display labels.
     *
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            'packaging' => 'Packaging',
            'message_sticker' => 'Message sticker on box',
            'custom_card' => 'Custom card inside',
        ];
    }
}
