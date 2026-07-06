<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartCheckoutRequest extends FormRequest
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
            ...self::addonRules(),
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:30'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:20'],
            'gift_message' => ['nullable', 'string', 'max:500'],
            'message_sticker_note' => ['nullable', 'string', 'max:200', 'required_with:message_sticker_addon_id'],
            'custom_card_note' => ['nullable', 'string', 'max:500', 'required_with:custom_card_addon_id'],

            // Razorpay handshake fields — present once the widget completes.
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['nullable', 'string'],
        ];
    }

    /**
     * The fields that determine the payable total (gift addons). Shared with
     * the submit-time order endpoint so the Razorpay order is always created
     * from a validated, server-priced selection.
     *
     * @return array<string, mixed>
     */
    public static function addonRules(): array
    {
        return [
            'is_gift' => ['sometimes', 'boolean'],
            'packaging_addon_id' => ['nullable', 'integer', Rule::exists('gift_addons', 'id')->where(fn ($q) => $q->where('type', 'packaging')->where('is_active', true))],
            'message_sticker_addon_id' => ['nullable', 'integer', Rule::exists('gift_addons', 'id')->where(fn ($q) => $q->where('type', 'message_sticker')->where('is_active', true))],
            'custom_card_addon_id' => ['nullable', 'integer', Rule::exists('gift_addons', 'id')->where(fn ($q) => $q->where('type', 'custom_card')->where('is_active', true))],
        ];
    }
}
