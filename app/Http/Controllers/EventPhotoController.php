<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventPhotoController extends Controller
{
    /**
     * Maximum number of gallery photos per event.
     */
    public const MAX_PHOTOS = 24;

    /**
     * Upload one or more gallery photos.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $existing = $this->photos($event);
        $remaining = max(0, self::MAX_PHOTOS - count($existing));

        $request->validate([
            'photos' => ['required', 'array', 'max:'.max(1, $remaining)],
            'photos.*' => ['image', 'mimes:jpeg,jpg,png,webp,gif', 'max:8192'],
        ], [], [
            'photos.max' => "You can store up to ".self::MAX_PHOTOS." photos.",
        ]);

        $added = [];
        foreach (array_slice($request->file('photos'), 0, $remaining) as $file) {
            $name = Str::random(20).'.'.$file->extension();
            $file->storeAs("events/{$event->id}", $name, 'public');
            $added[] = "/storage/events/{$event->id}/{$name}";
        }

        $event->update(['photos' => array_values([...$existing, ...$added])]);

        return back()->with('success', count($added).' '.Str::plural('photo', count($added)).' added.');
    }

    /**
     * Delete a single gallery photo.
     */
    public function destroy(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $path = $request->validate(['path' => ['required', 'string']])['path'];

        $photos = array_values(array_filter($this->photos($event), fn ($p) => $p !== $path));
        $this->deleteFile($path);

        $event->update(['photos' => $photos]);

        return back()->with('success', 'Photo removed.');
    }

    /**
     * Reorder the gallery (e.g. choose which photo is the cover).
     */
    public function reorder(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['string'],
        ])['order'];

        $current = $this->photos($event);
        // Keep only paths that actually belong to this event, in the requested order.
        $reordered = array_values(array_filter($order, fn ($p) => in_array($p, $current, true)));
        // Append any that were omitted, so nothing is silently lost.
        $reordered = array_values(array_unique([...$reordered, ...$current]));

        $event->update(['photos' => $reordered]);

        return back()->with('success', 'Photos reordered.');
    }

    /**
     * @return array<int, string>
     */
    protected function photos(Event $event): array
    {
        return array_values(array_filter((array) ($event->photos ?? [])));
    }

    /**
     * Remove the underlying file for a stored public URL path.
     */
    protected function deleteFile(string $path): void
    {
        $relative = Str::after($path, '/storage/');
        if ($relative !== $path && Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
