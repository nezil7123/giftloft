<?php

namespace App\Http\Requests\Admin;

use App\Models\GiftAddon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GiftAddonRequest extends FormRequest
{
    /**
     * Authorization is handled by the admin route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(GiftAddon::types()))],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'is_default' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
