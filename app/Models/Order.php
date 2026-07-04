<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'subtotal',
        'addons_total',
        'total',
        'currency',
        'status',
        'is_gift',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'address_line',
        'city',
        'postal_code',
        'gift_message',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'total' => 'decimal:2',
        'is_gift' => 'boolean',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addons()
    {
        return $this->hasMany(OrderAddon::class);
    }
}
