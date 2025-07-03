<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserinfoResources extends JsonResource
{
    public function toArray($request)
    {
        $latestTransaction = $this->transactions->sortByDesc('created_at')->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'country_code' => $this->country_code,
            'profile_photo' => $this->profile_photo,
            'address' => $this->address,
            'company_name' => $this->company_name,
            'email_verified_at' => $this->email_verified_at,
            'is_paid' => $this->is_paid,

            // Projects
            'owned_projects_count' => $this->projects()->count(),
            'assigned_projects_count' => $this->assignedUserProjects->count(),

            // Subscription Info
            'subscription' => $latestTransaction ? [
                'transaction_id' => $latestTransaction->transaction_id,
                'amount' => $latestTransaction->amount,
                'status' => $latestTransaction->status,
                'is_renew' => $latestTransaction->is_renew,
                'is_subscribe' => $latestTransaction->is_subscribe,
                'message' => $latestTransaction->message,
                'plan' => $latestTransaction->plan ? [
                    'id' => $latestTransaction->plan->id,
                    'name' => $latestTransaction->plan->name,
                    'price' => $latestTransaction->plan->price,
                    'description' => $latestTransaction->plan->description,
                    'duration' => $latestTransaction->plan->duration,
                ] : null,
            ] : null,
        ];
    }
}
