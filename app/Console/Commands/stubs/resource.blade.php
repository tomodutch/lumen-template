namespace App\Resources;


use Illuminate\Http\Resources\Json\Resource;

class {{$pascalCase}} extends Resource
{
    public function toArray($request)
    {
        return [
            @php
                /** @var \App\Console\Commands\DataType $dataType */
            @endphp
            @foreach ($dataTypes as $dataType)
                '{{camel_case($dataType->getName())}}' => $this->{{$dataType->getName()}},
            @endforeach
            'createdAt' => $this->when($this->created_at, $this->created_at->toISO8601String(), null),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at->toISO8601String(), null)
        ];
    }
}