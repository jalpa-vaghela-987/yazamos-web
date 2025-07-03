<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'receiver_id' => 'nullable|exists:users,id',
            'receiver_type' => 'nullable|in:0,1,2,3',
            'message' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
            'phase_id' => 'nullable|exists:phases,id',
            'subject' => 'required|string',
        ];
    }
}
