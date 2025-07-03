<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

trait QueryCrudBuilderTrait
{
    public function scopeQueryCrud(Builder $query, array $input)
    {
        $orderBy = $input['orderBy'] ?? 'id';
        $order = $input['order'] ?? 'desc';
        $perPage = $input['perPage'] ?? 15;
        $searchTerm = $input['search'] ?? null;

        // Apply search filters
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                foreach ($this->searchColumns as $column) {
                    if (is_array($column)) {
                        $q->orWhereHas($column['relationship'], function ($qry) use ($searchTerm, $column) {
                            $qry->where($column['column'], 'LIKE', "%{$searchTerm}%");
                        });
                    } else {
                        $q->orWhere($column, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });
        }

        // Filter by service ID
        if (!empty($input['services_id'])) {
            $query->where('services_id', $input['services_id']);
        }

        // Filter by scheduled type (Daily or Hourly)
        if (!empty($input['scheduled_type'])) {
            $query->where('scheduled_type', $input['scheduled_type']);
        }

        // Handle custom ordering
        if ($orderBy === 'services') {
            $query->leftJoin('services', 'integrations.services_id', '=', 'services.id')
                ->orderBy("services.name", $order)
                ->select('integrations.*'); // Ensure only integrations columns are selected
        } elseif ($orderBy === 'type') {
            $query->orderByRaw("
                CASE
                    WHEN scheduled_type = 'D' THEN 'Daily'
                    ELSE 'Hourly'
                END $order
            ");
        } elseif ($orderBy === 'entrepreneur_projects') {
            $query->withCount('projects as entrepreneur_projects_count')
                  ->orderBy('entrepreneur_projects_count', $order);
        } elseif ($orderBy === 'assigned_projects') {
            $query->withCount(['assignedUserProjects as assigned_projects_count' => function($q) {
                $q->where('invitation_status', 'accepted');
            }])
                  ->orderBy('assigned_projects_count', $order);
        } elseif ($orderBy === 'role') {
            $query->whereHas('roles', function ($q) use ($order) {
                $q->orderBy('name',$order);
            });
        } else {
            $query->orderBy($orderBy, $order);
        }

        return $query->paginate($perPage);
    }



    public function scopeSearchRecords(Builder $query, $searchTerm = null, $searchColumns = [])
    {
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchColumns, $searchTerm) {
                foreach ($searchColumns as $column) {
                    if (is_array($column)) {
                        $q->orWhereHas($column['relationship'], function ($qry) use ($searchTerm, $column) {
                            $qry->where($column['column'], 'LIKE', "%{$searchTerm}%");
                        });
                    } else {
                        $q->orWhere($column, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });
        }
        return $query;
    }
}
