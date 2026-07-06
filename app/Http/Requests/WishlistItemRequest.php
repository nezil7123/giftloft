<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Items are sourced from the catalog, so title/price/image/link are
        // fixed. The owner may only adjust quantity, their note, and whether
        // guests can gift the item.
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'note' => ['nullable', 'string', 'max:1000'],
            'is_giftable' => ['required', 'boolean'],
        ];
    }
}
