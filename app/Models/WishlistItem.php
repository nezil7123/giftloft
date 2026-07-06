<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'user_id',
        'product_id',
        'title',
        'product_url',
        'image_url',
        'price',
        'quantity',
        'claimed_quantity',
        'note',
        'is_giftable',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'claimed_quantity' => 'integer',
        'is_giftable' => 'boolean',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }
}
