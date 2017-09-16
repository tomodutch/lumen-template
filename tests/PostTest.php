<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Resources\Post as PostResource;
use App\Resources\PostCollection as PostResourceCollection;
use App\Post;

class PostTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index()
    {
        $posts = factory(Post::class, 5)->create();

        $this->json('GET', $this->route());

        $this->assertJson(
            $this->response->content(),
            json_encode(new PostResourceCollection($posts)));
    }

    /**
     * @test
     */
    public function show()
    {
        $post = factory(Post::class)->create();

        $this->json('GET', $this->route($post));

        $this->assertJson(
            $this->response->content(),
            json_encode(new PostResource($post))
        );
    }

    /**
     * @test
     */
    public function store()
    {
        $post = factory(Post::class)->make();
        $data = json_decode(json_encode(new PostResource($post)), true);

        $this->json('POST', $this->route(), $data);

        $this->assertJson(
            $this->response->content(),
            json_encode(new PostResource($post))
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
        $post = factory(Post::class)->create();
        $otherPost = factory(Post::class)->make([
            'id' => $post->id
        ]);

        $data = json_decode(json_encode(new PostResource($post)), true);
        $this->json('PUT', $this->route($post), $data);

        $this->assertJson(
            $this->response->content(),
            json_encode(new PostResource($otherPost))
        );
    }

    /**
     * @test
     */
    public function destroy()
    {
        $post = factory(Post::class)->create();

        $this->json('DELETE', $this->route($post));

        $this->assertResponseStatus(\Illuminate\Http\Response::HTTP_NO_CONTENT);
        $this->assertNull(Post::where('id', $post->id)->first());
    }

    private function route(Post $post = null)
    {
        return '/posts/' . optional($post)->id;
    }
}
