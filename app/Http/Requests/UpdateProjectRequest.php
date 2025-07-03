<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can add authorization logic here if needed
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'current_property_value' => 'nullable|numeric',
            'renovation_cost' => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric|min:0',
            'images' => 'required|array',
            'images.*.file' => 'nullable|image|max:2048',
            'images.*.flag' => 'nullable|string|in:before,after',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }
}
