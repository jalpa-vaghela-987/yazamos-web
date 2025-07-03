<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AdminUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'email'                 => $this->email,
            'address'               => $this->address,
            'phone_number'          => $this->phone_number,
            'email_verified_at'      => $this->email_verified_at,
            'two_factor_enabled'    => !is_null($this->google2fa_secret),
            'created_by'            => $this->creator?->name,
            'profile_photo_url'      => $this->profile_photo ? asset(Storage::url($this->profile_photo)) : null,
            'role'                  => $this->getRoleNames()->first(),
            'role_id'               => $this->roles()->first()?->id,
            'project_ids'           => $this->assignedUserProjects->pluck('project_id'),
            'entrepreneur_projects' => $this->projects,
            'assigned_projects'     => $this->assignedUserProjects->where('invitation_status', 'accepted'),
            'country_code'          => $this->country_code,
            'projects'              => ProjectResource::collection($this->projects)
        ];
    }
}
