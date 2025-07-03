<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PhaseStage;
use Illuminate\Validation\Rule;

class UpdatePhaseTimeLineRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('phases')->ignore($this->title, 'title')
            ],
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'stage' => 'nullable|in:' . implode(',', PhaseStage::stages()),
            'planned_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'date_of_expense' => 'nullable|date',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        return $rules;
    }
}
