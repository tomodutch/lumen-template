namespace App\Http\Controllers;

use App\{{$pascalCase}};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\{{$pascalCase}} as {{$pascalCase}}Resource;
use App\Resources\{{$pascalCase}}Collection as {{$pascalCase}}CollectionResource;

class {{$pascalCase}}Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $take = $request->get('take', 10);
        $skip = $request->get('skip', 0);

        /**
        * Naive implementation of pagination
        * You might run into performance issues when the skip is high
        * For a solution see (https://explainextended.com/2009/10/23/mysql-order-by-limit-performance-late-row-lookups/)
        */
        $query = {{$pascalCase}}::query();
        $total = $query->count();
        ${{$plural}} = $query->skip($skip)->take($take)->get();

        return (new {{$pascalCase}}CollectionResource(${{$plural}}))
        ->response()
        ->header('X-PAGINATION-TOTAL', $total)
        ->header('X-PAGINATION-SKIP', $skip)
        ->header('X-PAGINATION-TAKE', $take)
        ->header('X-PAGINATION-SUPPORT', true);
    }

    /**
    * Display the specified resource.
    *
    * @param  mixed  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        ${{$camelCase}} = {{$pascalCase}}::where('id', $id)->firstOrFail();

        return new {{$pascalCase}}Resource(${{$camelCase}});
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $this->keysToSnakeCase(
            $this->validate($request, $this->rules()));

        /** @var {{$pascalCase}} ${{$camelCase}} */
        ${{$camelCase}} = tap(new {{$pascalCase}})->fill($attributes);
        ${{$camelCase}}->saveOrFail();

        return (new {{$pascalCase}}Resource(${{$camelCase}}))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $attributes = $this->keysToSnakeCase(
            $this->validate($request, $this->rules()));

        /** @var {{$pascalCase}} ${{$camelCase}} */
        ${{$camelCase}} = {{$pascalCase}}::where('id', $id)->firstOrFail();
        ${{$camelCase}}->fill($attributes)->saveOrFail();

        return new {{$pascalCase}}Resource(${{$camelCase}});
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ${{$camelCase}} = {{$pascalCase}}::where('id', $id)->first();
        if (${{$camelCase}}) {
            ${{$camelCase}}->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function rules() {
        return [
            @php
                /** @var \App\Console\Commands\DataType $dataType */
            @endphp
            @foreach ($dataTypes as $dataType)
                @unless (in_array($dataType, $primaryIdDataTypes->toArray()))
                    '{{camel_case($dataType->getName())}}' => [
                        @foreach ($dataType->getRules() as $rule)
                            @if (str_contains($rule, ['::', 'new ']))
                                {!! $rule !!},
                            @else
                                '{{$rule}}',
                            @endif
                        @endforeach
                    ],
                @endunless
            @endforeach
        ];
    }
    @if ($inlineSnakeCase)
        public function keysToSnakeCase(array $input)
        {
            $snakeCased = [];

            foreach ($input as $key => $value) {
                $snakeCased[snake_case($key)] = $value;
            }

            return $snakeCased;
        }
    @endif
    @if ($inlineValidate)
        public function validate(Request $request, array $rules = [], array $messages = [], array $customAttributes = []): array
        {
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = Validator::make($request->all(), $rules);

            $validator->validate();

            return $request->only(collect($rules)->keys()->map(function ($rule) {
                return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
            })->unique()->toArray());
        }
    @endif
}
