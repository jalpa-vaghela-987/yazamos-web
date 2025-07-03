<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProjectCategoryResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssetTypeResource;

class ProjectResource extends JsonResource
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
            'asset_type' => $this->assetType->name ?? null,
            'asset_type_id' => $this->assetType ?? null,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'current_property_value' => $this->current_property_value,
            'purchase_price' => $this->purchase_price,
            'renovation_cost' => $this->renovation_cost,
            'wedge' => $this->current_property_value - ($this->purchase_price + $this->renovation_cost),
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset(Storage::url($image->image)) ?? null,
                    'flag' => $image->flag,
                ];
            }),
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
                'role' => $this->user->getRoleNames()->first() ?? null,
            ],
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'company' => [
                'id' => $this->company->id ?? null,
                'name' => $this->company->name ?? null,
            ],
        ];
    }
}
