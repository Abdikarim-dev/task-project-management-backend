<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    /**
     * @param  array<string, mixed>  $resource
     */
    public function __construct(
        mixed $resource,
        private readonly array $meta = []
    ) {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            (new UserResource($this->resource))->toArray($request),
            [
                'projects' => ProjectSummaryResource::collection($this->meta['projects'] ?? collect()),
                'tasks_by_status' => $this->meta['tasks_by_status'] ?? [],
                'projects_by_status' => $this->meta['projects_by_status'] ?? [],
                'tasks_count' => $this->meta['tasks_count'] ?? 0,
            ]
        );
    }
}
