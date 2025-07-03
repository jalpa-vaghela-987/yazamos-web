<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class Message extends Model
{
    protected $fillable = [
        'sender_id', 'receiver_id', 'message', 'is_read', 'project_id', 'file', 'phase_id', 'subject', 'response','receiver_type'
    ];

     const RECEIVER_TYPES = [
        0 => 'All',
        1 => 'investor',
        2 => 'tenant',
        3 => 'admin',
        4 => 'entrepreneur',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }

    public function scopeFilterRecords(Builder $query, $input = [])
    {
        $filters = Arr::get($input, 'filters', []);

        foreach ($filters as $column => $value) {

            if ($column === 'role' && !empty($value)) {
                $query->whereHas('receiver', function ($q) use ($value) {
                    $q->whereHas('roles', function ($q2) use ($value) {
                        $q2->where('name', $value);
                    });
                });
            }

            if ( $column === 'subject' && !empty($value)) {
                $query->where('subject', $value);
            }

            if ( $column === 'message' && !empty($value)) {
                $query->where('message', $value);
            }
        }

        return $query;
    }
}
