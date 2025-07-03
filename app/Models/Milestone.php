<?php

namespace App\Models;

use App\Enums\PhaseStage;
use App\Traits\QueryCrudBuilderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Milestone extends Model
{
    use HasFactory,QueryCrudBuilderTrait,HasRoles;

    protected $fillable = [
        'phase_id',
        'project_id',
        'title',
        'description',
        'status',
        'date',
        'start_date',
        'end_date',
        'progress',
        'planned_cost',
        'actual_cost',
        'order',
        'custom_fields',
        'color'
    ];

    protected $searchColumns = [

        'phase_id',
        'title',
        'description',
        'status',
        'due_date',
        'start_date',
        'end_date',
        'progress',
        'planned_cost',
        'actual_cost',
        'order',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'planned_cost' => 'float',
        'actual_cost' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getStageColorAttribute()
    {
        return PhaseStage::getColor($this->color);
    }

    public static function getStagesWithColors()
    {
        return PhaseStage::getAllStagesWithColors();
    }
}
