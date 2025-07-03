<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardDetail extends Model
{
    protected $fillable = [
        'user_id', 'card_holder_name', 'card_no', 'expiry_month', 'expiry_year', 'cvv', 'is_active'
    ];

    const ACTIVE = 1;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->hasMany(Transation::class);
    }
}
