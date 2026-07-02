<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * List and search platform users.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->withCount(['events', 'wishlists', 'giftsSent'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => ['q' => $q],
        ]);
    }

    /**
     * Grant or revoke admin access.
     */
    public function toggleAdmin(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'You cannot change your own admin access.');

        // is_admin is deliberately not mass-assignable — set it explicitly.
        $user->forceFill(['is_admin' => ! $user->is_admin])->save();

        return back()->with('success', $user->is_admin ? "{$user->name} is now an admin." : "Admin access revoked for {$user->name}.");
    }

    /**
     * Delete a user account (cascades to their events, wishlists, gifts).
     */
    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'You cannot delete your own account from the admin panel.');

        $user->delete();

        return back()->with('success', 'Account deleted.');
    }
}
