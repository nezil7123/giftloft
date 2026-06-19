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
        ];
    }
}
