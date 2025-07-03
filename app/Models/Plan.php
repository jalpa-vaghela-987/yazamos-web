<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryCrudBuilderTrait;

class Plan extends Model
{
    protected $guarded = [];

    use QueryCrudBuilderTrait;

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
