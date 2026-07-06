<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WishlistRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'visibility' => ['required', Rule::in(['public', 'private', 'hidden'])],
            'event_id' => [
                'nullable',
                // The linked event must belong to the current user.
                Rule::exists('events', 'id')->where('user_id', $this->user()->id),
            ],
            'active' => ['required', 'boolean'],
            // Optional saved shipping address — if a street address is given,
            // the fields needed to actually deliver become required too.
            'delivery_address' => ['nullable', 'array'],
            'delivery_address.recipient_name' => ['nullable', 'required_with:delivery_address.address_line', 'string', 'max:255'],
            'delivery_address.address_line' => ['nullable', 'string', 'max:255'],
            'delivery_address.city' => ['nullable', 'required_with:delivery_address.address_line', 'string', 'max:120'],
            'delivery_address.postal_code' => ['nullable', 'required_with:delivery_address.address_line', 'string', 'max:20'],
            'delivery_address.phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Treat an address with no street line as "no saved address", so
     * blanking the form clears it.
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (array_key_exists('delivery_address', $data) && blank($data['delivery_address']['address_line'] ?? null)) {
            $data['delivery_address'] = null;
        }

        return $key === null ? $data : data_get($data, $key, $default);
    }
}
