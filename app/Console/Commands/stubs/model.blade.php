namespace App;


use Illuminate\Database\Eloquent\Model;

class {{$modelName}} extends Model
{
    @if ($primaryIdDataType)
        protected $primaryKey = 'id';
    @endif

    protected $fillable = [
        @php
            /** @var \App\Console\Commands\DataType $dataType */
        @endphp
        @foreach ($dataTypes as $dataType)
            @unless ($dataType == $primaryIdDataType)
                '{{$dataType->getName()}}',
            @endunless
        @endforeach
    ];
}
