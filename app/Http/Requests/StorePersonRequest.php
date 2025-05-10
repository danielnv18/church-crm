<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CivilStatus;
use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // General info
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'civil_status' => ['nullable', Rule::enum(CivilStatus::class)],
            'dob' => ['nullable', 'date'],

            // Spiritual information
            'membership_at' => ['nullable', 'date'],
            'baptized_at' => ['nullable', 'date'],
            'saved_at' => ['nullable', 'date'],
            'testimony' => ['nullable', 'string', 'max:1000'],

            // Contact information
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'alternate_phone' => ['nullable', 'string'],
            'address_line_1' => ['nullable', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
        ];
    }
}
