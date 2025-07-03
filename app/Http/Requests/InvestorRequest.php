<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = (int)$this->route('investor');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                "unique:users,email" . ($id ? ",$id" : '')
            ],
            'permission' => 'required|array',
            'address' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                "unique:users,phone_number" . ($id ? ",$id" : ''),
            ],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

        ];
    }
}
