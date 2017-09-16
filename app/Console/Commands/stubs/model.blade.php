namespace App;


use Illuminate\Database\Eloquent\Model;
@if ($implementSoftDeletes)
use Illuminate\Database\Eloquent\SoftDeletes;
@endif
@if ($useUuidAsPrimaryKey)
use Ramsey\Uuid\Uuid;
@endif

class {{$pascalCase}} extends Model
{
    public $table = '{{$plural}}';

    @if ($implementSoftDeletes)
        use SoftDeletes;
    @endif

    @if ($useUuidAsPrimaryKey)
        public $incrementing = false;
    @endif

    protected $fillable = [
        @php
            /** @var \App\Console\Commands\DataType $dataType */
        @endphp
        @foreach ($dataTypes as $dataType)
            @unless (in_array($dataType, $primaryIdDataTypes->toArray()))
                '{{$dataType->getName()}}',
            @endunless
        @endforeach
    ];

    protected $dates = [
        @if ($implementSoftDeletes)
            'deleted_at',
        @endif

        @foreach ($dataTypes as $dataType)
            @if ($dataType->isDate())
                '{{$dataType->getName()}}',
            @endif
        @endforeach
    ];
    @if ($useUuidAsPrimaryKey)
    /**
    * Boot function from laravel.
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            @if (class_exists('Ramsey\Uuid\Uuid'))
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            @else
                // $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
                throw new \Exception('Generate a UUID when saving this model');
            @endif
        });
    }
    @endif
}
