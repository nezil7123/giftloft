<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared helpers for storing/removing single-file event photos
 * (cover photo, venue photo) on the "public" disk.
 */
trait StoresEventPhotos
{
    protected function storeEventFile(Event $event, UploadedFile $file): string
    {
        $name = Str::random(20).'.'.$file->extension();
        $file->storeAs("events/{$event->id}", $name, 'public');

        return "/storage/events/{$event->id}/{$name}";
    }

    protected function deleteEventFile(?string $url): void
    {
        if (! $url) {
            return;
        }

        $relative = Str::after($url, '/storage/');
        if ($relative !== $url && Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
