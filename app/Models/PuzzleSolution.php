<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuzzleSolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'puzzle_id',
        'solver_id',
        'answer',
        'is_correct',
        'attempts',
        'solved_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'attempts' => 'integer',
        'solved_at' => 'datetime',
    ];

    public function puzzle()
    {
        return $this->belongsTo(Puzzle::class);
    }

    public function solver()
    {
        return $this->belongsTo(User::class, 'solver_id');
    }
}
