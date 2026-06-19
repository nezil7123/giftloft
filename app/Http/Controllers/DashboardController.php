<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gift;
use App\Models\Message;
use App\Models\Puzzle;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Dashboard', [
            'user' => $user,
            'stats' => [
                'events' => Event::where('user_id', $user->id)->count(),
                'wishlists' => Wishlist::where('user_id', $user->id)->count(),
                'gifts' => Gift::where('buyer_id', $user->id)
                    ->orWhere('recipient_id', $user->id)
                    ->count(),
                'messages' => Message::where('sender_id', $user->id)->orWhere('recipient_id', $user->id)->count(),
                'puzzles' => Puzzle::where('sender_id', $user->id)->orWhere('recipient_id', $user->id)->count(),
            ],
        ]);
    }
}
