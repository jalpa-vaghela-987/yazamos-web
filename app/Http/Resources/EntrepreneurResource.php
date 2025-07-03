<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EntrepreneurResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'email'              => $this->email,
            'address'            => $this->address,
            'phone_number'       => $this->phone_number,
            'email_verified_at'  => $this->email_verified_at,
            'two_factor_enabled' => !is_null($this->google2fa_secret),
            'roles'              => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),

            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'created_by'         => $this->creator?->name,
            'profile_photo_url'  => $this->profile_photo ? asset(Storage::url($this->profile_photo)) : null,
        ];
    }
}
