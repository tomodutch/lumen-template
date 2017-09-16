<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Person as PersonResource;
use App\Resources\PersonCollection as PersonCollectionResource;

class PersonController extends Controller
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
        $query = Person::query();
        $total = $query->count();
        $persons = $query->skip($skip)->take($take)->get();

        return (new PersonCollectionResource($persons))
        ->response()
        ->header('X-PAGINATION-TOTAL', $total)
        ->header('X-PAGINATION-SKIP', $skip)
        ->header('X-PAGINATION-TAKE', $take)
        ->header('X-PAGINATION-SUPPORT', true);
    }

    /**
    * Display the specified resource.
    *
    * @param    mixed  $id
    * @return  \Illuminate\Http\Response
    */
    public function show($id)
    {
        $person = Person::where('id', $id)->firstOrFail();

        return new PersonResource($person);
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

        /** @var  Person $person */
        $person = tap(new Person)->fill($attributes);
        $person->saveOrFail();

        return (new PersonResource($person))
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

        /** @var  Person $person */
        $person = Person::where('id', $id)->firstOrFail();
        $person->fill($attributes)->saveOrFail();

        return new PersonResource($person);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::where('id', $id)->first();
        if ($person) {
            $person->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function rules() {
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
