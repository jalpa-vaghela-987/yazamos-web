<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhaseHistoryResource extends JsonResource
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
            'project_id' => $this->project_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'stage' => $this->stage,
            'type' => $this->type,
            'progress' => $this->progress,
            'planned_cost' => $this->planned_cost,
            'actual_cost' => $this->actual_cost,
            'date_of_expense' => $this->date_of_expense,
            'file' => ($this->file) ? asset('storage/' . $this->file) : null,
        ];
    }
}
