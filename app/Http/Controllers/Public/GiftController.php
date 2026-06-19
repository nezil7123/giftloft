<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GiftController extends Controller
{
    public function index()
    {
        $gifts = Gift::where('status', 'available')
            ->with('event')
            ->latest()
            ->get();

        return Inertia::render('Public/Shop', [
            'gifts' => $gifts,
        ]);
    }

    public function show(Gift $gift)
    {
        return Inertia::render('Public/GiftDetail', [
            'gift' => $gift->load('event'),
            'isAuthenticated' => auth()->check(),
        ]);
    }
}
