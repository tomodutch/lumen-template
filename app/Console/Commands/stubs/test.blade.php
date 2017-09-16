use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\{{$pascalCase}} as {{$pascalCase}}Resource;
use App\Resources\{{$pascalCase}}Collection as {{$pascalCase}}ResourceCollection;
use App\{{$pascalCase}};

class {{$pascalCase}}Test extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        ${{$camelCase}}s = factory({{$pascalCase}}::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJsonStringEqualsJsonString(
            json_encode(new {{$pascalCase}}ResourceCollection(${{$plural}})),
            $this->response->content());
    }

    /**
     * @test
     */
    public function show()
    {
        ${{$camelCase}} = factory({{$pascalCase}}::class)->create();

        $this->json('GET', $this->route(${{$camelCase}}));

        $this->assertJsonStringEqualsJsonString(
            json_encode(new {{$pascalCase}}Resource(${{$camelCase}})),
            $this->response->content()
        );
    }

    /**
     * @test
     */
    public function store()
    {
        ${{$camelCase}} = factory({{$pascalCase}}::class)->make();
        $data = json_decode(json_encode(new {{$pascalCase}}Resource(${{$camelCase}})), true);

        $this->json('POST', $this->route(), $data);

        $attributes = array_only(${{$camelCase}}->toArray(), ${{$camelCase}}->getFillable());
        $query = {{$pascalCase}}::query();
        foreach ($attributes as $key => $value) {
            $query->where($key, $value);
        }

        $this->assertNotNull($query->first());
    }

    /**
     * @test
     */
    public function validation()
    {
        $this->json('POST', $this->route(), []);

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function update()
    {
        ${{$camelCase}} = factory({{$pascalCase}}::class)->create();
        $other{{$pascalCase}} = factory({{$pascalCase}}::class)->make([
            'id' => ${{$camelCase}}->id
        ]);

        $data = json_decode(json_encode(new {{$pascalCase}}Resource(${{$camelCase}})), true);
        $this->json('PUT', $this->route(${{$camelCase}}), $data);

        $this->assertEquals(
            $other{{$pascalCase}}->getFillable(),
            {{$pascalCase}}::where('id', ${{$camelCase}}->id)->firstOrFail()->getFillable()
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        ${{$camelCase}} = factory({{$pascalCase}}::class)->create();

        $this->json('DELETE', $this->route(${{$camelCase}}));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull({{$pascalCase}}::where('id', ${{$camelCase}}->id)->first());
    }

    private function route({{$pascalCase}} ${{$camelCase}} = null)
    {
        return '/{{$camelCase}}s/' . optional(${{$camelCase}})->id;
    }
}
