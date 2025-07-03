<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    protected $fillable = [
        'name',
        'user_id'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
