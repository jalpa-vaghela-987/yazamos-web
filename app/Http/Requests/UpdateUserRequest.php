<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Role;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', Rule::unique('users')->ignore($this->user)],
            'role_id' => 'required|exists:roles,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'exists:projects,id',
            'country_code' => 'required|string',
            'reassign_projects' => 'nullable|array',
            'reassign_projects.*' => 'exists:projects,id',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $roleId = $this->input('role_id');

            if ($roleId) {
                $role = Role::find($roleId);

                if ($role && strtolower($role->name) !== 'entrepreneur') {
                    if (
                        !$this->filled('project_ids') ||
                        !is_array($this->input('project_ids')) ||
                        count($this->input('project_ids')) === 0
                    ) {
                        $validator->errors()->add(
                            'project_ids',
                            'The project id field is required.'
                        );
                    }
                }
            }
        });
    }
}
