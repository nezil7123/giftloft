<?php

namespace App\Http\Requests;

use App\Support\EventTemplates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventDesignRequest extends FormRequest
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
            'template' => ['required', Rule::in(EventTemplates::websiteKeys())],
            'invitation_template' => ['required', Rule::in(EventTemplates::invitationKeys())],

            'template_data' => ['nullable', 'array'],
            'template_data.hosts' => ['nullable', 'string', 'max:255'],
            'template_data.tagline' => ['nullable', 'string', 'max:255'],
            'template_data.dress_code' => ['nullable', 'string', 'max:255'],
            'template_data.rsvp_note' => ['nullable', 'string', 'max:1000'],
            'template_data.venue_note' => ['nullable', 'string', 'max:600'],
            'template_data.travel' => ['nullable', 'string', 'max:600'],
            'template_data.stay' => ['nullable', 'string', 'max:600'],

            'template_data.schedule' => ['nullable', 'array', 'max:20'],
            'template_data.schedule.*.time' => ['nullable', 'string', 'max:100'],
            'template_data.schedule.*.title' => ['nullable', 'string', 'max:150'],
            'template_data.schedule.*.detail' => ['nullable', 'string', 'max:300'],

            'template_data.faqs' => ['nullable', 'array', 'max:20'],
            'template_data.faqs.*.question' => ['nullable', 'string', 'max:200'],
            'template_data.faqs.*.answer' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
