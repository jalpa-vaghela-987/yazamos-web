<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\CardDetailResource;
use Carbon\Carbon;

class UserActivePlanResource extends JsonResource
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
            'plan' => PlanResource::make($this->whenLoaded('plan')),
            'card_detail' => CardDetailResource::make($this->whenLoaded('cardDetail')),
            'is_subscribe' => $this->is_subscribe,
            'created_at' => $this->created_at,
            'expired_at' => Carbon::parse($this->created_at)->addMonth()->addDay()->format('Y-m-d'),
        ];
    }
}
