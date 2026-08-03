<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->getFirstMediaUrl('avatar') ?: null,
            'avatar_thumb_url' => $this->getFirstMediaUrl('avatar', 'thumb') ?: null,
            'certificates' => $this->getMedia('certificates')->map(fn($media) => [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
                'size' => $media->size,
            ]) ?: [],
            'roles' => $this->roles->pluck('name'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}