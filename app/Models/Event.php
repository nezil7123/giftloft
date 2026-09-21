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
        // New events go live immediately — a freshly created event whose share
        // link 404s for guests is the more surprising default.
        'status' => 'published',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }

    /**
     * Headline numbers the host needs: who is coming, how many people that adds
     * up to, the veg/non-veg split, and how many need a room.
     *
     * @return array<string, int>
     */
    public function rsvpSummary(): array
    {
        $rsvps = $this->relationLoaded('rsvps') ? $this->rsvps : $this->rsvps()->get();
        $attending = $rsvps->where('status', Rsvp::STATUS_ATTENDING);

        return [
            'responses' => $rsvps->count(),
            'attending' => $attending->count(),
            'not_attending' => $rsvps->where('status', Rsvp::STATUS_NOT_ATTENDING)->count(),
            'head_count' => (int) $attending->sum('party_size'),
            'veg' => $attending->where('meal_preference', Rsvp::MEAL_VEG)->count(),
            'non_veg' => $attending->where('meal_preference', Rsvp::MEAL_NON_VEG)->count(),
            'accommodation' => $attending->where('needs_accommodation', true)->count(),
        ];
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
