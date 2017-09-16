<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\Pieter as PieterResource;
use App\Resources\PieterCollection as PieterResourceCollection;
use App\Pieter;

class PieterTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        $pieters = factory(Pieter::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJsonStringEqualsJsonString(
            json_encode(new PieterResourceCollection($pieters)),
            $this->response->content());
    }

    /**
     * @test
     */
    public function show()
    {
        $pieter = factory(Pieter::class)->create();

        $this->json('GET', $this->route($pieter));

        $this->assertJsonStringEqualsJsonString(
            json_encode(new PieterResource($pieter)),
            $this->response->content()
        );
    }

    /**
     * @test
     */
    public function store()
    {
        $pieter = factory(Pieter::class)->make();
        $data = json_decode(json_encode(new PieterResource($pieter)), true);

        $this->json('POST', $this->route(), $data);

        $attributes = array_only($pieter->toArray(), $pieter->getFillable());
        $query = Pieter::query();
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
        $pieter = factory(Pieter::class)->create();
        $otherPieter = factory(Pieter::class)->make([
            'id' => $pieter->id
        ]);

        $data = json_decode(json_encode(new PieterResource($pieter)), true);
        $this->json('PUT', $this->route($pieter), $data);

        $this->assertEquals(
            $otherPieter->getFillable(),
            Pieter::where('id', $pieter->id)->firstOrFail()->getFillable()
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        $pieter = factory(Pieter::class)->create();

        $this->json('DELETE', $this->route($pieter));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull(Pieter::where('id', $pieter->id)->first());
    }

    private function route(Pieter $pieter = null)
    {
        return '/pieters/' . optional($pieter)->id;
    }
}
