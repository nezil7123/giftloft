<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Gift;
use App\Models\Puzzle;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Dashboard', [
            'user' => $request->user(),
            'stats' => [
                'users' => User::count(),
                'events' => Event::count(),
                'gifts' => Gift::count(),
                'puzzles' => Puzzle::count(),
            ],
        ]);
    }
}
