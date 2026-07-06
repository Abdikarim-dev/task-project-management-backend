<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
  /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_projects' => $this->resource['total_projects'],
            'active_projects' => $this->resource['active_projects'],
            'completed_projects' => $this->resource['completed_projects'],
            'total_tasks' => $this->resource['total_tasks'],
            'active_tasks' => $this->resource['active_tasks'],
            'completed_tasks' => $this->resource['completed_tasks'],
            'overdue_tasks' => $this->resource['overdue_tasks'],
            'tasks_by_status' => $this->resource['tasks_by_status'],
            'recent_tasks' => TaskResource::collection(collect($this->resource['recent_tasks'])),
        ];
    }
}
