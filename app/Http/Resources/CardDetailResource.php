<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\UserResource;

class CardDetailResource extends JsonResource
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
            'user' => UserResource::make($this->user),
            'card_holder_name' => $this->card_holder_name,
            'card_no' => $this->card_no,
            'expiry_month' => $this->expiry_month,
            'expiry_year' => $this->expiry_year,
            'cvv' => $this->cvv,
            'is_active' => $this->is_active
        ];
    }
}
