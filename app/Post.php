<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;

class Post extends Model
{
    public $table = 'posts';

            use SoftDeletes;
    
            use Uuids;
        public $incrementing = false;
    
    protected $fillable = [
                                                                'title',
                                                'body',
                                                'is_featured',
                        ];

    protected $dates = [
                    'deleted_at',
        
                                                                                            ];
}
