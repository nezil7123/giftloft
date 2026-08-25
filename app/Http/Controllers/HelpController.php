<?php

namespace App\Http\Controllers;

use App\Support\HelpGuides;
use Inertia\Inertia;

class HelpController extends Controller
{
    /**
     * "How it works" hub — links out to every guide.
     *
     * The wishlist/gifting guides are left out of this listing while that
     * side of the product is hidden for the phase 1 launch (see
     * PublicLayout/AuthenticatedLayout/Dashboard). The guides themselves
     * stay reachable directly at /help/{slug} — only the hub listing hides
     * them, matching the "nav links only" hide everywhere else.
     */
    public function index()
    {
        return Inertia::render('Help/Index', [
            'guides' => HelpGuides::visible(),
        ]);
    }

    /**
     * A single illustrated how-to guide.
     */
    public function show(string $guide)
    {
        $data = HelpGuides::find($guide);

        abort_if(! $data, 404);

        return Inertia::render('Help/Guide', [
            'guide' => $data,
        ]);
    }
}
