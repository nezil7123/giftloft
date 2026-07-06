<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'mobile', 'password', 'avatar_url', 'bio', 'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code', 'profile_visible', 'google_id', 'google_token', 'google_refresh_token'])]
#[Hidden(['password', 'remember_token', 'google_token', 'google_refresh_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_visible' => 'boolean',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function giftsSent()
    {
        return $this->hasMany(Gift::class, 'buyer_id');
    }

    public function giftsReceived()
    {
        return $this->hasMany(Gift::class, 'recipient_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function puzzlesSent()
    {
        return $this->hasMany(Puzzle::class, 'sender_id');
    }

    public function puzzlesReceived()
    {
        return $this->hasMany(Puzzle::class, 'recipient_id');
    }

    public function puzzleSolutions()
    {
        return $this->hasMany(PuzzleSolution::class, 'solver_id');
    }

    public function likes()
    {
        return $this->hasMany(UserLike::class);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'followed_id');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function activityFeeds()
    {
        return $this->hasMany(ActivityFeed::class);
    }
}
