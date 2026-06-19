<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'title',
        'description',
        'puzzle_type',
        'payload',
        'difficulty',
        'is_active',
        'reward_points',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function solutions()
    {
        return $this->hasMany(PuzzleSolution::class);
    }
}
