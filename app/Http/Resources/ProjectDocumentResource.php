<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDocumentResource extends JsonResource
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
            'category' => $this->title,
            'date_uploaded' => $this->date_uploaded,
            'file_name' => $this->file_name,
            'file' => ($this->file) ? asset('storage/' . $this->file) : null,
            'type' => $this->type
        ];
    }
}
