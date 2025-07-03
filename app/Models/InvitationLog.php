<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryCrudBuilderTrait;

class InvitationLog extends Model
{
    use QueryCrudBuilderTrait;

    protected $fillable = [
        'user_id',
        'phone_number',
        'message',
        'status',
        'sent_at',
    ];
    protected $searchColumns = [
        'user_id',
        'phone_number',
        'message',
        'status',
        'sent_at',
    ];

    protected $dates = [
        'sent_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
