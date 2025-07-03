<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedUserProject extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'invitation_status',
        'role'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
