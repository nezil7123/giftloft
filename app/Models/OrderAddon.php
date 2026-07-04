<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gift_addon_id',
        'type',
        'name',
        'image',
        'price',
        'note',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function giftAddon()
    {
        return $this->belongsTo(GiftAddon::class);
    }
}
