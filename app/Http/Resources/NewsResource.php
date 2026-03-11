<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug'=> $this->slug,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'image' => $this->image ? (str_contains($this->image, 'http') ? $this->image : Storage::url($this->image)) : null,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'news_blocks' => NewsBlockResource::collection($this->whenLoaded('blocks')),
            'author' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
