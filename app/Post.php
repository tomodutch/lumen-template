<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    public $table = 'posts';

    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'is_featured',
    ];

    protected $dates = [
        'deleted_at',

    ];
}
