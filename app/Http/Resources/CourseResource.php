<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => (float) $this->price,
            'teacher_id' => $this->teacher_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}
