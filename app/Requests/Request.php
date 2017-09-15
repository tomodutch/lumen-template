<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;

class Request extends \Illuminate\Http\Request
{
    public function validate(array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($this->all(), $this->rules());

        $validator->validate();

        return $this->only(collect($this->rules())->keys()->map(function ($rule) {
            return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }
}
