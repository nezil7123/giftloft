<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'message' => ['nullable', 'string', 'max:500'],
            // When the wishlist owner saved a delivery address, the gifter
            // can ship there without knowing it — the server fills it in.
            'use_saved_address' => ['nullable', 'boolean'],
            'recipient_name' => ['required_unless:use_saved_address,true', 'nullable', 'string', 'max:255'],
            'address_line' => ['required_unless:use_saved_address,true', 'nullable', 'string', 'max:255'],
            'city' => ['required_unless:use_saved_address,true', 'nullable', 'string', 'max:120'],
            'postal_code' => ['required_unless:use_saved_address,true', 'nullable', 'string', 'max:20'],
            // Razorpay handshake fields — present once the (stubbed) widget completes.
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['nullable', 'string'],
        ];
    }
}
