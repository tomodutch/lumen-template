<?php

namespace App\Http\Controllers;

use App\Test;
use Illuminate\Http\Request;

class TestController extends Controller
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
        $query = Test::query();
        $total = $query->count();
        $tests = $query->skip($skip)->take($take)->get();

        return (new TestCollectionResource($tests))
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
        $test = Test::where('id', $id)->firstOrFail();

        return new TestResource($test);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $this->validate($request, $this->rules());

        /** @var  Test $test */
        $test = tap(new Test)->fill($attributes);
        $test->saveOrFail();

        return (new TestResource($test))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return  \Illuminate\Http\Response
     */
    public function destroy()
    {

    }

    public function rules()
    {
        return [
            'id' => [
                'required',
                new \App\Rules\ValidUUID(),
            ],
            'title' => [
                'required',
                'string',
            ],
        ];
    }
}
