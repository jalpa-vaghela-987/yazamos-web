<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
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
            'two_factor_enabled' => !!($this->google2fa_secret),
            'phone_number'       => $this->phone_number,
            'roles'              => $this->getRoleNames(),
            'permissions'        => $this->getAllPermissions()->pluck('name'),
            'role'               => $this->getRoleNames()->first(),
            'profile_photo'      => $this->profile_photo ? asset(Storage::url($this->profile_photo)) : null,
        ];
    }
}
