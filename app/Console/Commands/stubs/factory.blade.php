$factory->define(\App\{{$pascalCase}}::class, function(\Faker\Generator $faker) {
    return [
        @php
        /** @var \App\Console\Commands\DataType $dataType */
        @endphp
        @foreach ($dataTypes as $dataType)
            @unless ($dataType->isPrimaryKey())
                @if ($dataType->isUuid())
                    '{{$dataType->getName()}}' => $faker->uuid,
                @elseif ($dataType->isBoolean())
                    '{{$dataType->getName()}}' => $faker->boolean,
                @elseif ($dataType->isNumeric())
                    '{{$dataType->getName()}}' => $faker->randomNumber(),
                @elseif ($dataType->isString())
                    '{{$dataType->getName()}}' => $faker->word,
                @elseif ($dataType->isDate())
                    '{{$dataType->getName()}}' => $faker->date(\Carbon\Carbon::ISO8601),
                @else
                    '{{$dataType->getName()}}' => $faker->word,
                @endif
            @endunless
        @endforeach
    ];
});