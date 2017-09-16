<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\Group as GroupResource;
use App\Resources\GroupCollection as GroupResourceCollection;
use App\Group;

class GroupTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        $groups = factory(Group::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJsonStringEqualsJsonString(
            json_encode(new GroupResourceCollection($groups)),
            $this->response->content());
    }

    /**
     * @test
     */
    public function show()
    {
        $group = factory(Group::class)->create();

        $this->json('GET', $this->route($group));

        $this->assertJsonStringEqualsJsonString(
            json_encode(new GroupResource($group)),
            $this->response->content()
        );
    }

    /**
     * @test
     */
    public function store()
    {
        $group = factory(Group::class)->make();
        $data = json_decode(json_encode(new GroupResource($group)), true);

        $this->json('POST', $this->route(), $data);

        $attributes = array_only($group->toArray(), $group->getFillable());
        $query = Group::query();
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
        $group = factory(Group::class)->create();
        $otherGroup = factory(Group::class)->make([
            'id' => $group->id
        ]);

        $data = json_decode(json_encode(new GroupResource($group)), true);
        $this->json('PUT', $this->route($group), $data);

        $this->assertEquals(
            $otherGroup->getFillable(),
            Group::where('id', $group->id)->firstOrFail()->getFillable()
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        $group = factory(Group::class)->create();

        $this->json('DELETE', $this->route($group));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull(Group::where('id', $group->id)->first());
    }

    private function route(Group $group = null)
    {
        return '/groups/' . optional($group)->id;
    }
}
