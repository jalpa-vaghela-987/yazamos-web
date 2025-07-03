<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
//use App\Http\Resources\PhaseBudgetResource;
use App\Http\Resources\Mobile\PhaseHistoryResource;

class PhaseBudgetResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'planned_cost' => $this->planned_cost,
            'actual_cost' => $this->actual_cost,
            'extra_cost' => abs($this->planned_cost - $this->actual_cost),
            'date_of_expense' => $this->date_of_expense,
            'file' => ($this->file) ? asset('storage/' . $this->file) : null,
            'children' => PhaseBudgetResource::collection($this->whenLoaded('children')),
            'histories' => PhaseHistoryResource::collection($this->whenLoaded('phaseHistories'))
        ];
    }
}
