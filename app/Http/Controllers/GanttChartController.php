<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class GanttChartController extends Controller
{
    public function getAllProjectsGanttData()
    {
        $projects = Project::with('phases.milestones')->get();
    
        $tasks = [];
    
        foreach ($projects as $project) {
            $tasks[] = [
                'label' =>  $project->name,
                'start' => $project->start_date,
                'end' => $project->end_date,
                'type' => 'project',
                'id' => 'project-' . $project->id,
            ];
    
            foreach ($project->phases as $phase) {
                $tasks[] = [
                    'label' =>  $phase->title,
                    'start' => $phase->start_date,
                    'end' => $phase->end_date,
                    'type' => 'phase',
                    'id' => 'phase-' . $phase->id,
                    'parent' => 'project-' . $project->id,
                ];
    
                foreach ($phase->milestones as $milestone) {
                    $tasks[] = [
                        'label' =>  $milestone->title,
                        'start' => $milestone->start_date ?? $milestone->due_date,
                        'end' => $milestone->end_date ?? $milestone->due_date,
                        'type' => 'milestone',
                        'id' => 'milestone-' . $milestone->id,
                        'parent' => 'phase-' . $phase->id,
                    ];
                }
            }
        }
    
        return response()->json(['data' => $tasks]);
    }
    
}



