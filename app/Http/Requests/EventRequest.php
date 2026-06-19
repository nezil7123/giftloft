<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    /**
     * Authorization is handled by the controller via the EventPolicy.
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
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(self::types()))],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'cover_photo_url' => ['nullable', 'url', 'max:2048'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_public' => ['required', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ends_at.after_or_equal' => 'The end date must be the same as or after the start date.',
            'type.in' => 'Please choose a valid event type.',
        ];
    }

    /**
     * Supported event types — keys are stored, values are display labels.
     *
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            'wedding' => 'Wedding',
            'birthday' => 'Birthday',
            'engagement' => 'Engagement',
            'anniversary' => 'Anniversary',
            'baby_shower' => 'Baby Shower',
            'proposal' => 'Proposal',
            'graduation' => 'Graduation',
            'housewarming' => 'Housewarming',
            'other' => 'Other',
        ];
    }
}
