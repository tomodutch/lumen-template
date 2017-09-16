<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class PersonCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}