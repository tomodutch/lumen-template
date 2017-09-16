<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}