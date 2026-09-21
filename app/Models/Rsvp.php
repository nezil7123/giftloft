<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    use HasFactory;

    public const STATUS_ATTENDING = 'attending';
    public const STATUS_NOT_ATTENDING = 'not_attending';

    public const MEAL_VEG = 'veg';
    public const MEAL_NON_VEG = 'non_veg';

    public const STATUSES = [self::STATUS_ATTENDING, self::STATUS_NOT_ATTENDING];
    public const MEALS = [self::MEAL_VEG, self::MEAL_NON_VEG];

    protected $fillable = [
        'event_id', 'user_id', 'name', 'email', 'phone',
        'status', 'party_size', 'meal_preference', 'needs_accommodation', 'note',
    ];

    protected $casts = [
        'needs_accommodation' => 'boolean',
        'party_size' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAttending(): bool
    {
        return $this->status === self::STATUS_ATTENDING;
    }

    public function scopeAttending($query)
    {
        return $query->where('status', self::STATUS_ATTENDING);
    }
}
