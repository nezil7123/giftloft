<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(array_keys(Product::categories()))],
            'gender' => ['nullable', Rule::in(array_keys(Product::genders()))],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'product_url' => ['nullable', 'url', 'max:2048'],
            'emoji' => ['nullable', 'string', 'max:16'],
            'accent' => ['required', Rule::in(['indigo', 'rose', 'amber', 'violet', 'emerald', 'sky', 'neutral'])],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
