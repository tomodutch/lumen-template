<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Pieter extends Resource
{
    public function toArray($request)
    {
        return [
                                                            'id' => $this->id,
                                                                'title' => $this->title,
                                                                'body' => $this->body,
                                                                'isFeatured' => (bool)$this->is_featured,
                                        'createdAt' => $this->when($this->created_at, function() {
                return $this->created_at->toISO8601String();
            }, null),
            'updatedAt' => $this->when($this->updated_at, function() {
                return $this->updated_at->toISO8601String();
            }, null)
        ];
    }
}