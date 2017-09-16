<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Post extends Resource
{
    public function toArray($request)
    {
        return [
                                                            'id' => $this->id,
                                                                'title' => $this->title,
                                                                'body' => $this->body,
                                                                'isFeatured' => $this->is_featured,
                                        'createdAt' => $this->when($this->created_at, $this->created_at->toISO8601String(), null),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at->toISO8601String(), null)
        ];
    }
}