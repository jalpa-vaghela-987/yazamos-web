<?php

namespace App\Models;

use App\Traits\QueryCrudBuilderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class Phase extends Model
{
    use HasFactory, QueryCrudBuilderTrait, HasRoles;

    protected $fillable = [
        'project_id',
        'milestone_id',
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
        'custom_fields',
        'extra',
        'date_of_expense',
        'file',
        'file_name',
        'date_uploaded'
    ];
    protected $searchColumns = [
        'project_id',
        'milestone_id',
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
        'custom_fields',
        'date_of_expense',
        'file',
        'file_name',
        'date_uploaded'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'extra' => 'array',
        'planned_cost' => 'float',
        'actual_cost' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function entrepreneur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function phases()
    {
        return $this->hasMany(Phase::class);
    }
    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function phaseHistories()
    {
        return $this->hasMany(PhaseHistory::class);
    }

    // Get the parent phase
    public function parent()
    {
        return $this->belongsTo(Phase::class, 'parent_id');
    }

    // Get all child phases
    public function children()
    {
        return $this->hasMany(Phase::class, 'parent_id');
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function scopeFilterRecords(Builder $query, $input = [])
    {
        $filters = Arr::get($input, 'filters', []);
       
        foreach ($filters as $column => $value) {
            if ( $column === 'category_name' && !empty($value)) {
                $query->where('title', 'LIKE', '%'.$value.'%');
            }

            if ( $column === 'type' && !empty($value)) {
                $query->where('type', $value);
            }

            if ( $column === 'date_uploaded' && !empty($value)) {
                $query->where('date_uploaded', $value);
            }
        }

        return $query;
    }

}
