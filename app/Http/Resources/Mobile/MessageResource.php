<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\UserResource;
use App\Http\Resources\ProjectDocumentResource;
use App\Http\Resources\PhaseCategoryResource;
use App\Http\Resources\Mobile\ProjectResource;
use Illuminate\Support\Facades\Storage;

class MessageResource extends JsonResource
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
            'subject' => $this->subject,
            'message' => $this->message,
            'sender' => new UserResource($this->whenLoaded('sender')),
            'receiver' => new UserResource($this->whenLoaded('receiver')),
            'created_at' => $this->created_at,
            'receiver_type' => $this->receiver_type,
            'receiver_type_text' => $this->getReceiverTypeText($this->receiver_type),
            'file' => $this->file ? asset(Storage::url($this->file)) : null,
            'document' => new ProjectDocumentResource($this->whenLoaded('phase')),
            'category' => new PhaseCategoryResource($this->whenLoaded('phase')),
            'project' => new ProjectResource($this->whenLoaded('project'))
        ];
    }
    private function getReceiverTypeText($type)
    {
        return match ((int) $type) {
            0 => 'All',
            1 => 'Investor',
            2 => 'Tenant',
            3 => 'Admin',
            default => null,
        };
    }
}
