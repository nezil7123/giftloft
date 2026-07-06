<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/',
                'not_in:admin,shop,cart,help,people,login,register,dashboard,events,wishlists,orders,gifts,templates,profile,checkout',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * Usernames are stored lowercase so lookups stay case-insensitive.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('username')) {
            $this->merge(['username' => mb_strtolower(trim($this->input('username')))]);
        } elseif ($this->has('username')) {
            $this->merge(['username' => null]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Usernames can only use lowercase letters and numbers, with single dashes or underscores between them.',
            'username.not_in' => 'That username is reserved.',
        ];
    }
}
