<?php

namespace App\Models;

use App\Traits\QueryCrudBuilderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class PhaseHistory extends Model
{
    protected $fillable = [
        'phase_id',
        'project_id',
        'parent_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'progress',
        'stage',
        'type',
        'planned_cost',
        'actual_cost',
        'order',
        'date_of_expense',
        'file'
    ];
    protected $searchColumns = [
        'phase_id',
        'project_id',
        'parent_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
        'progress',
        'stage',
        'type',
        'planned_cost',
        'actual_cost',
        'order',
        'date_of_expense',
        'file'
    ];

    protected $casts = [
        'planned_cost' => 'float',
        'actual_cost' => 'float',
    ];

    public function phases()
    {
        return $this->belongsTo(Phase::class, 'phase_id');
    }
}
