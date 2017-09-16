<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function validate(Request $request, array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        return $request->only(collect($rules)->keys()->map(function ($rule) {
            return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }
}
