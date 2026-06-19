<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'description',
        'location',
        'venue',
        'cover_photo_url',
        'photos',
        'template',
        'invitation_template',
        'template_data',
        'starts_at',
        'ends_at',
        'is_public',
        'status',
        'share_code',
    ];

    protected $casts = [
        'photos' => 'array',
        'template_data' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected $attributes = [
        'template' => 'classic',
        'invitation_template' => 'elegant',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }
}
