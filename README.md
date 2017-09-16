# Generator
Generate resources, models, controllers, validation factories, tests and migrations

```sh
php artisan cmake:resource Post posts id:uuid:primary title:string body:text is_featured:boolean:nullable --softDeletes
```

# Options
```sh
Usage:
  cmake:resource [options] [--] <name> <plural> <fields> (<fields>)...

Arguments:
  name                  The name of the resource
  plural                The plural name of the resource
  fields                The fields that the resource should have

Options:
      --softDeletes     Implement soft deletes for the resource
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Generate an API resource including its model, resource, controller, factory and migrations
```

## Data types
All data types that are [supported in migrations](https://laravel.com/docs/5.5/migrations#columns) can be used with this generator.

Will generate the following files:
## Controller
```php
<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Post as PostResource;
use App\Resources\PostCollection as PostCollectionResource;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return  void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
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
        $query = Post::query();
        $total = $query->count();
        $posts = $query->skip($skip)->take($take)->get();

        return (new PostCollectionResource($posts))
            ->response()
            ->header('X-PAGINATION-TOTAL', $total)
            ->header('X-PAGINATION-SKIP', $skip)
            ->header('X-PAGINATION-TAKE', $take)
            ->header('X-PAGINATION-SUPPORT', true);
    }

    /**
     * Display the specified resource.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::where('id', $id)->firstOrFail();

        return new PostResource($post);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = keysToSnakeCase($this->validate($request, $this->rules()));

        /** @var  Post $post */
        $post = tap(new Post)->fill($attributes);
        $post->saveOrFail();

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $attributes = keysToSnakeCase($this->validate($request, $this->rules()));

        /** @var  Post $post */
        $post = Post::where('id', $id)->firstOrFail();
        $post->fill($attributes)->saveOrFail();

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::where('id', $id)->first();
        if ($post) {
            $post->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
            ],
            'body' => [
                'required',
                'string',
            ],
            'isFeatured' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
```

## Model
```php
<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    public $table = 'posts';

    use SoftDeletes;
   
    protected $fillable = [
        'title',
        'body',
        'is_featured',
    ];

    protected $dates = [
        'deleted_at',

    ];
}
```

## Migration
```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('title');
            $table->text('body');
            $table->boolean('is_featured')->nullable();

            $table->timestampsTz();

            $table->primary([
                'id',
            ]);

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
```

## Single Resource
```php
<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Post extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'isFeatured' => $this->is_featured,
            'createdAt' => $this->when($this->created_at, $this->created_at->toISO8601String(), null),
            'updatedAt' => $this->when($this->updated_at, $this->updated_at->toISO8601String(), null)
        ];
    }
}
```

## Collection Resource
```php
<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection;
    }
}
```

## Factory
```php
<?php

$factory->define(\App\Post::class, function (\Faker\Generator $faker) {
    return [
        'title' => $faker->word,
        'body' => $faker->word,
        'is_featured' => $faker->boolean,
    ];
});
```

## Test
```php
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
```

## Routes
```php
<?php

$router->get('/posts', 'PostController@index');
$router->get('/posts/{id}', 'PostController@show');
$router->put('/posts/{id}', 'PostController@update');
$router->delete('/posts/{id}', 'PostController@destroy');
$router->post('/posts', 'PostController@store');
```

# Project state
The project is still under active development and is not available via composer
