<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $guarded = [];

    public function plan()
    {
        return $this->hasOne(Plan::class);
    }
}
