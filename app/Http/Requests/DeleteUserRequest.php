<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class DeleteUserRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = []; // Start with empty rules

        if ($this->input('role') && $this->input('role') == 'entrepreneur') {
                if($this->input('entrepreneur_total_projects') > 0) {
                    $rules['user_id'] = 'required|exists:users,id';
                }
        }

        return $rules;
    }
}
