<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PhaseStage;
use Illuminate\Validation\Rule;

class PhaseTimeLineRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'parent_id' => 'nullable|exists:phases,id',
            'title' =>  [
                'required',
                'string',
                'max:255',
                Rule::unique('phases')->where(function ($query) {
                    return $query->where('project_id', $this->project_id);
                }),
            ],
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'stage' => 'nullable|in:' . implode(',', PhaseStage::stages()),
            'planned_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'date_of_expense' => 'nullable|date',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'milestone_id' => 'nullable|exists:milestones,id',

        ];

        return $rules;
    }
}
