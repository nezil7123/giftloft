<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftAddonRequest;
use App\Models\GiftAddon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GiftAddonController extends Controller
{
    /**
     * List all gift customization addons (packaging, message sticker, custom card).
     */
    public function index()
    {
        return Inertia::render('Admin/GiftAddons', [
            'addons' => GiftAddon::query()
                ->orderBy('type')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('type'),
            'types' => GiftAddon::types(),
        ]);
    }

    /**
     * Add a new addon.
     */
    public function store(GiftAddonRequest $request)
    {
        $data = $request->validated();
        unset($data['image'], $data['remove_image']);

        $addon = GiftAddon::create([
            ...$data,
            'sort_order' => $data['sort_order'] ?? (GiftAddon::max('sort_order') + 1),
        ]);

        if ($request->hasFile('image')) {
            $addon->update(['image' => $this->storeImage($request->file('image'))]);
        }

        return back()->with('success', 'Addon added.');
    }

    /**
     * Update an addon (price changes apply to future orders only).
     */
    public function update(GiftAddonRequest $request, GiftAddon $giftAddon)
    {
        $data = $request->validated();
        $removeImage = (bool) ($data['remove_image'] ?? false);
        unset($data['image'], $data['remove_image']);

        if ($request->hasFile('image')) {
            $this->deleteImage($giftAddon->image);
            $data['image'] = $this->storeImage($request->file('image'));
        } elseif ($removeImage) {
            $this->deleteImage($giftAddon->image);
            $data['image'] = null;
        }

        $giftAddon->update($data);

        return back()->with('success', 'Addon updated.');
    }

    /**
     * Remove an addon from the catalog.
     */
    public function destroy(GiftAddon $giftAddon)
    {
        $this->deleteImage($giftAddon->image);
        $giftAddon->delete();

        return back()->with('success', 'Addon removed.');
    }

    protected function storeImage(UploadedFile $file): string
    {
        $name = Str::random(20).'.'.$file->extension();
        $file->storeAs('gift-addons', $name, 'public');

        return "/storage/gift-addons/{$name}";
    }

    protected function deleteImage(?string $url): void
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
