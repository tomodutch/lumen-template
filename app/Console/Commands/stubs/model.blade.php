namespace App;


use Illuminate\Database\Eloquent\Model;
@if ($implementSoftDeletes)
use Illuminate\Database\Eloquent\SoftDeletes;
@endif

class {{$pascalCase}} extends Model
{
    public $table = '{{$plural}}';

    @if ($implementSoftDeletes)
        use SoftDeletes;
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
}
