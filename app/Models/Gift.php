<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'recipient_id',
        'wishlist_item_id',
        'event_id',
        'payment_id',
        'amount',
        'currency',
        'status',
        'message',
        'delivery_address',
        'claimed_at',
        'delivered_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'delivery_address' => 'array',
        'claimed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function wishlistItem()
    {
        return $this->belongsTo(WishlistItem::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
