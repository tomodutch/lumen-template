<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class PieterCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}