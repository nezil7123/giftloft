<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'name',
        'description',
        'visibility',
        'delivery_address',
        'slug',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'delivery_address' => 'array',
    ];

    /**
     * Hidden by default so the owner's home address never leaks through a
     * serialized wishlist (public pages, checkout, admin lists). The owner's
     * own edit screen calls makeVisible() explicitly.
     */
    protected $hidden = [
        'delivery_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }
}
