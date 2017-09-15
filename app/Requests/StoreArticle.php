<?php

namespace App\Requests;

use App\Rules\ValidUUID;

class StoreArticle extends Request
{
    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'someId' => ['sometimes', new ValidUUID()]
        ];
    }
}
