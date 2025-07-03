<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\PhaseHistoryResource;

class PhaseTimelineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'stage' => $this->stage,
            'description' => $this->description,
            'planned_cost' => $this->planned_cost,
            'actual_cost' => $this->actual_cost,
            'date_of_expense' => $this->date_of_expense,
            'children' => PhaseTimelineResource::collection($this->whenLoaded('children')),
            'histories' => PhaseHistoryResource::collection($this->whenLoaded('phaseHistories')),
            'milestone' => $this->whenLoaded('milestone') ? new MilestoneResource($this->milestone) : null
        ];
    }
}
