<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Group extends Resource
{
    public function toArray($request)
    {
        return [
                                                            'id' => $this->id,
                                                                'name' => $this->name,
                                        'createdAt' => $this->when($this->created_at, function() {
                return $this->created_at->toISO8601String();
            }, null),
            'updatedAt' => $this->when($this->updated_at, function() {
                return $this->updated_at->toISO8601String();
            }, null)
        ];
    }
}