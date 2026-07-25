<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductImportRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'default_category' => ['required', Rule::in(array_keys(Product::categories()))],
            'default_gender' => ['nullable', Rule::in(array_keys(Product::genders()))],
        ];
    }
}
