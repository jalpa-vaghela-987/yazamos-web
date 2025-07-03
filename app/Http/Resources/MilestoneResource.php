<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MilestoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phase_id' => $this->phase_id,
            'title' => $this->title,
            'stage_color' => $this->stage_color,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'progress' => $this->progress,
            'planned_cost' => $this->planned_cost,
            'actual_cost' => $this->actual_cost,
            'order' => $this->order,
            'custom_fields' => $this->custom_fields,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'phase' => new PhaseResource($this->whenLoaded('phase')),
        ];
    }
}
