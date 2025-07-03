<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PhaseStage;
use App\Enums\PhaseType;

class StorePhaseRequest extends FormRequest
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
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'parent_id' => 'nullable|exists:phases,id',
            'title' => 'required|string|max:255|unique:phases,title',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'stage' => 'nullable|in:' . implode(',', PhaseStage::stages()),
            'type' => 'required|in:' . implode(',', PhaseType::types()),
            'progress' => 'nullable|integer|min:0|max:100',
            'planned_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'order' => 'nullable|integer|min:0',
            'custom_fields' => 'nullable|array',
            'extra' => 'nullable|array',
            'extra.*.key' => 'required_with:extra|string',
            'extra.*.value' => 'nullable|string',
            'date_of_expense' => 'nullable|date',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        if($this->type == PhaseType::TIMELINE) {
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date|after_or_equal:start_date';
            $rules['stage'] = 'required|in:' . implode(',', PhaseStage::stages());
        } else {
            $rules['planned_cost'] = 'required|numeric|min:0';
            $rules['actual_cost'] = 'required|numeric|min:0';
        }

        return $rules;
    }
}
