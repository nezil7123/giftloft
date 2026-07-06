<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserSearchController extends Controller
{
    /**
     * Find a person by their exact email address, phone number, or username.
     *
     * Exact-match only, by design: you can look someone up if you already
     * know their contact detail or handle, but you can't browse or
     * enumerate users. Hidden profiles (profile_visible = false) are
     * never returned.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $match = null;

        if ($q !== '') {
            $normalized = preg_replace('/[\s\-().]/', '', $q);
            $handle = mb_strtolower(ltrim($q, '@')); // allow "@sarah-james" too

            $match = User::query()
                ->where('profile_visible', true)
                ->where(function ($query) use ($q, $normalized, $handle) {
                    $query->whereRaw('lower(email) = ?', [mb_strtolower($q)])
                        ->orWhere('mobile', $q)
                        ->orWhere('mobile', $normalized)
                        ->orWhere('username', $handle);
                })
                ->first(['id', 'name', 'username', 'avatar_url']);
        }

        return Inertia::render('People/Find', [
            'query' => $q,
            'searched' => $q !== '',
            'match' => $match,
        ]);
    }
}
