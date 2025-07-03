<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryCrudBuilderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Transaction extends Model
{
    use HasFactory, HasRoles, QueryCrudBuilderTrait;

    const SUBSCRIBE = 1, UNSUBSCRIBE = 0;
    const PENDING_RENEW = 0, RENEW = 1;
    
    protected $fillable = [
        'user_id',
        'plan_id',
        'card_detail_id',
        'transaction_id',
        'transaction_data',
        'amount',
        'status',
        'is_renew',
        'message',
        'is_subscribe',
        'created_at'
    ];

    protected $searchColumns = [
        'user_id',
        'plan_id',
        'card_detail_id',
        'transaction_id',
        'transaction_data',
        'amount',
        'status',
        'is_renew',
        'message',
        'is_subscribe'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function cardDetail()
    {
        return $this->belongsTo(CardDetail::class);
    }
}
