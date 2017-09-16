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

        $this->assertJson(
            $this->response->content(),
            json_encode(new {{$pascalCase}}ResourceCollection(${{$camelCase}}s)));
    }

    /**
     * @test
     */
    public function show()
    {
        ${{$camelCase}} = factory({{$pascalCase}}::class)->create();

        $this->json('GET', $this->route(${{$camelCase}}));

        $this->assertJson(
            $this->response->content(),
            json_encode(new {{$pascalCase}}Resource(${{$camelCase}}))
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

        $this->assertJson(
            $this->response->content(),
            json_encode(new {{$pascalCase}}Resource(${{$camelCase}}))
        );
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

        $this->assertJson(
            $this->response->content(),
            json_encode(new {{$pascalCase}}Resource($other{{$pascalCase}}))
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
