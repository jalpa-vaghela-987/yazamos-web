<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_category_id',
        'name',
        'description',
        'location',
        'estimated_budget',
        'current_property_value',
        'calculated_value',
        'entrepreneur_id',
        'purchase_price',
        'renovation_cost',
        'changed_at',
    ];
}