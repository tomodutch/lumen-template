namespace App;


use Illuminate\Database\Eloquent\Model;

class {{$pascalCase}} extends Model
{
    public $table = '{{$plural}}';

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
        @foreach ($dataTypes as $dataType)
            @if ($dataType->isDate())
                '{{$dataType->getName()}}',
            @endif
        @endforeach
    ];
}
