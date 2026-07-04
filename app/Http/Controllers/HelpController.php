<?php

namespace App\Http\Controllers;

use App\Support\HelpGuides;
use Inertia\Inertia;

class HelpController extends Controller
{
    /**
     * "How it works" hub — links out to every guide.
     */
    public function index()
    {
        return Inertia::render('Help/Index', [
            'guides' => HelpGuides::all(),
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
