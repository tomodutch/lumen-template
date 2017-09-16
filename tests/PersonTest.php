<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\Person as PersonResource;
use App\Resources\PersonCollection as PersonResourceCollection;
use App\Person;

class PersonTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        $persons = factory(Person::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJsonStringEqualsJsonString(
            json_encode(new PersonResourceCollection($persons)),
            $this->response->content());
    }

    /**
     * @test
     */
    public function show()
    {
        $person = factory(Person::class)->create();

        $this->json('GET', $this->route($person));

        $this->assertJsonStringEqualsJsonString(
            json_encode(new PersonResource($person)),
            $this->response->content()
        );
    }

    /**
     * @test
     */
    public function store()
    {
        $person = factory(Person::class)->make();
        $data = json_decode(json_encode(new PersonResource($person)), true);

        $this->json('POST', $this->route(), $data);

        $attributes = array_only($person->toArray(), $person->getFillable());
        $query = Person::query();
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
        $person = factory(Person::class)->create();
        $otherPerson = factory(Person::class)->make([
            'id' => $person->id
        ]);

        $data = json_decode(json_encode(new PersonResource($person)), true);
        $this->json('PUT', $this->route($person), $data);

        $this->assertEquals(
            $otherPerson->getFillable(),
            Person::where('id', $person->id)->firstOrFail()->getFillable()
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        $person = factory(Person::class)->create();

        $this->json('DELETE', $this->route($person));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull(Person::where('id', $person->id)->first());
    }

    private function route(Person $person = null)
    {
        return '/persons/' . optional($person)->id;
    }
}
