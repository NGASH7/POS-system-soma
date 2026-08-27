<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure the authenticated user is the one updating their own profile
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // ADDED: Validation for the profile photo
            'profile_photo' => [
                'nullable',              // The field is optional
                'image',                // Must be a valid image file (jpeg, png, bmp, gif, svg, or webp)
                'mimes:jpeg,png,jpg,gif', // Specific allowed extensions
                'max:2048'              // Maximum file size in kilobytes (2MB)
            ],
        ];
    }
}