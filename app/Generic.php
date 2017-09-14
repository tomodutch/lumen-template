<?php
namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class Generic extends Model
{
    protected $table = null;
    protected $fillable = ['http', 'message', 'errors', 'code'];
    protected $attributes = ['http' => Response::HTTP_OK];
}
