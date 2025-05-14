<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ActionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

final class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the user has the necessary permissions
        return auth()->user()?->can(ActionEnum::Update->value.' user') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user->email, 'email'),
            ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['required', 'exists:roles,id'],
        ];
    }
}
