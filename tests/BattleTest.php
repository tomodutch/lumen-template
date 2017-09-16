<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\Battle as BattleResource;
use App\Resources\BattleCollection as BattleResourceCollection;
use App\Battle;

class BattleTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        $battles = factory(Battle::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJsonStringEqualsJsonString(
            json_encode(new BattleResourceCollection($battles)),
            $this->response->content());
    }

    /**
     * @test
     */
    public function show()
    {
        $battle = factory(Battle::class)->create();

        $this->json('GET', $this->route($battle));

        $this->assertJsonStringEqualsJsonString(
            json_encode(new BattleResource($battle)),
            $this->response->content()
        );
    }

    /**
     * @test
     */
    public function store()
    {
        $battle = factory(Battle::class)->make();
        $data = json_decode(json_encode(new BattleResource($battle)), true);

        $this->json('POST', $this->route(), $data);

        $attributes = array_only($battle->toArray(), $battle->getFillable());
        $query = Battle::query();
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
        $battle = factory(Battle::class)->create();
        $otherBattle = factory(Battle::class)->make([
            'id' => $battle->id
        ]);

        $data = json_decode(json_encode(new BattleResource($battle)), true);
        $this->json('PUT', $this->route($battle), $data);

        $this->assertEquals(
            $otherBattle->getFillable(),
            Battle::where('id', $battle->id)->firstOrFail()->getFillable()
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        $battle = factory(Battle::class)->create();

        $this->json('DELETE', $this->route($battle));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull(Battle::where('id', $battle->id)->first());
    }

    private function route(Battle $battle = null)
    {
        return '/battles/' . optional($battle)->id;
    }
}
