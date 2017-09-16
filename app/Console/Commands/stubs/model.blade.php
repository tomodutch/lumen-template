namespace App;


use Illuminate\Database\Eloquent\Model;

class {{$pascalCase}} extends Model
{
    protected $fillable = [
        @php
            /** @var \App\Console\Commands\DataType $dataType */
        @endphp
        @foreach ($dataTypes as $dataType)
            @unless (in_array($dataType, $primaryIdDataTypes))
                '{{$dataType->getName()}}',
            @endunless
        @endforeach
    ];
}
