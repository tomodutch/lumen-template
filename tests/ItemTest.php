<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\Item as ItemResource;
use App\Resources\ItemCollection as ItemResourceCollection;
use App\Item;

class ItemTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        $items = factory(Item::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJsonStringEqualsJsonString(
            json_encode(new ItemResourceCollection($items)),
            $this->response->content());
    }

    /**
     * @test
     */
    public function show()
    {
        $item = factory(Item::class)->create();

        $this->json('GET', $this->route($item));

        $this->assertJsonStringEqualsJsonString(
            json_encode(new ItemResource($item)),
            $this->response->content()
        );
    }

    /**
     * @test
     */
    public function store()
    {
        $item = factory(Item::class)->make();
        $data = json_decode(json_encode(new ItemResource($item)), true);

        $this->json('POST', $this->route(), $data);

        $attributes = array_only($item->toArray(), $item->getFillable());
        $query = Item::query();
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
        $item = factory(Item::class)->create();
        $otherItem = factory(Item::class)->make([
            'id' => $item->id
        ]);

        $data = json_decode(json_encode(new ItemResource($item)), true);
        $this->json('PUT', $this->route($item), $data);

        $this->assertEquals(
            $otherItem->getFillable(),
            Item::where('id', $item->id)->firstOrFail()->getFillable()
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        $item = factory(Item::class)->create();

        $this->json('DELETE', $this->route($item));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull(Item::where('id', $item->id)->first());
    }

    private function route(Item $item = null)
    {
        return '/items/' . optional($item)->id;
    }
}
