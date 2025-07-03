<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\CardDetailResource;

class TransactionResource extends JsonResource
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
            'transaction_id' => $this->transaction_id,
            'transaction_data' => json_decode($this->transaction_data),
            'amount' => $this->amount,
            'status' => $this->status,
            'is_renew' => $this->is_renew,
            'message' => $this->message,
            'is_subscribe' => $this->is_subscribe,
            'created_at' => $this->created_at
        ];
    }
}
