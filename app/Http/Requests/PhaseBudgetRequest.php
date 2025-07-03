<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhaseBudgetRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'parent_id' => 'nullable|exists:phases,id',
            'title' => 'required|string|max:255|unique:phases,title',
            'description' => 'nullable|string',
            'planned_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'date_of_expense' => 'nullable|date',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        return $rules;
    }
}
