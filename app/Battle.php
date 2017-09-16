<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;

class Battle extends Model
{
    public $table = 'battles';

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
