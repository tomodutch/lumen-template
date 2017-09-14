<?php
namespace App\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;

class Generic extends Resource
{
    public function toArray($request)
    {
        return [
            'code' => $this->when(isset($this->code), $this->code),
            'errors' => $this->when(is_array($this->errors), $this->errors),
            'message' => $this->when(isset($this->message), $this->message, Response::$statusTexts[$this->http]),
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->http);
    }
}