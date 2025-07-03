<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
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
        $tenantId = (int)$this->route('tenant');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                "unique:users,email" . ($tenantId ? ",$tenantId" : '')
            ],
//            'permission' => 'required|array',
            'address' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                "unique:users,phone_number" . ($tenantId ? ",$tenantId" : ''),
            ],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
