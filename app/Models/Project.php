<?php

namespace App\Models;

use App\Traits\QueryCrudBuilderTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Project extends Model
{
    use HasFactory, SoftDeletes, QueryCrudBuilderTrait, HasRoles;

    protected $fillable = [
        'asset_type_id',
        'name',
        'description',
        'location',
        'estimated_budget',
        'current_property_value',
        'calculated_value',
        'user_id',
        'purchase_price',
        'renovation_cost',
        'start_date',
        'end_date',
        'company_id'
    ];
    protected $searchColumns = [
        'asset_type_id',
        'name',
        'description',
        'location',
        'estimated_budget',
        'current_property_value',
        'purchase_price',
        'renovation_cost',
        'calculated_value',
        'user_id',
        'company_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function phases()
    {
        return $this->hasMany(Phase::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function messages()
    {
        return $this->hasMany(ProjectMessage::class);
    }
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function assignedUserProjects()
    {
        return $this->hasMany(AssignedUserProject::class);
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
