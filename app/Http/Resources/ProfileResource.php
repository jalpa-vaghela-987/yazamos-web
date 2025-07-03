<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
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
            'address'              => $this->address,
            'company_name'              => $this->company_name,
            'two_factor_enabled' => !!($this->google2fa_secret),
            'google2fa_secret' => $this->google2fa_secret,
            'roles'              => $this->getRoleNames(),
            'permissions'        => $this->getAllPermissions()->pluck('name'),
            'profile_photo'      => $this->profile_photo ? asset(Storage::url($this->profile_photo)) : null,
        ];
    }
}
