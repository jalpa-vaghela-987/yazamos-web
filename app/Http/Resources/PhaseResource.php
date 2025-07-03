<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MilestoneResource;

class PhaseResource extends JsonResource
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
            'project_id' => $this->project_id,
            'milestone_id' => $this->milestone_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'stage' => $this->stage,
            'type' => $this->type,
            'progress' => $this->progress,
            'planned_cost' => $this->planned_cost,
            'actual_cost' => $this->actual_cost,
            'order' => $this->order,
            'custom_fields' => $this->custom_fields,
            'extra' => $this->extra,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'date_of_expense' => $this->date_of_expense,
            'file' => ($this->file) ? asset('storage/' . $this->file) : null,
            'milestones' => MilestoneResource::collection($this->whenLoaded('milestones')),
        ];
    }
}
