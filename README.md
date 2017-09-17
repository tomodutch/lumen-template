# Generator
Generate resources, models, controllers, validation factories, tests and migrations

```sh
php artisan cmake:resource Group groups id:increments name:string:required:between[5,255] description:text --softDeletes
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

use App\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Group as GroupResource;
use App\Resources\GroupCollection as GroupCollectionResource;

class GroupController extends Controller
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
        $query = Group::query();
        $total = $query->count();
        $groups = $query->skip($skip)->take($take)->get();

        return (new GroupCollectionResource($groups))
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
        $group = Group::where('id', $id)->firstOrFail();

        return new GroupResource($group);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $this->keysToSnakeCase(
            $this->validate($request, $this->rules()));

        /** @var  Group $group */
        $group = tap(new Group)->fill($attributes);
        $group->saveOrFail();

        return (new GroupResource($group))
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
        $attributes = $this->keysToSnakeCase(
            $this->validate($request, $this->rules()));

        /** @var  Group $group */
        $group = Group::where('id', $id)->firstOrFail();
        $group->fill($attributes)->saveOrFail();

        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Group::where('id', $id)->first();
        if ($group) {
            $group->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'between:5,255',
            ],
            'description' => [
                'required',
                'string',
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

class Group extends Model
{
    public $table = 'groups';

    use SoftDeletes;


    protected $fillable = [
        'name',
        'description',
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

class CreateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');

            $table->timestampsTz();
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
        Schema::drop('groups');
    }
}
```

## Single Resource
```php
<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\Resource;

class Group extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'createdAt' => $this->when($this->created_at, function () {
                return $this->created_at->toISO8601String();
            }, null),
            'updatedAt' => $this->when($this->updated_at, function () {
                return $this->updated_at->toISO8601String();
            }, null)
        ];
    }
}
```

## Collection Resource
```php
<?php

namespace App\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupCollection extends ResourceCollection
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

$factory->define(\App\Group::class, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->word,
    ];
});
```

## Test
```php
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
```

## Routes
```php
$router->get('/groups', 'GroupController@index');
$router->get('/groups/{id}', 'GroupController@show');
$router->put('/groups/{id}', 'GroupController@update');
$router->delete('/groups/{id}', 'GroupController@destroy');
$router->post('/groups', 'GroupController@store');
```

# Project state
The project is still under active development and is not available via composer
