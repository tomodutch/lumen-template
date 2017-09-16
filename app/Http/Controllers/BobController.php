<?php

namespace App\Http\Controllers;

use App\Bob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Bob as BobResource;
use App\Resources\BobCollection as BobCollectionResource;

class BobController extends Controller
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
        $query = Bob::query();
        $total = $query->count();
        $bobs = $query->skip($skip)->take($take)->get();

        return (new BobCollectionResource($bobs))
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
        $bob = Bob::where('id', $id)->firstOrFail();

        return new BobResource($bob);
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

        /** @var  Bob $bob */
        $bob = tap(new Bob)->fill($attributes);
        $bob->saveOrFail();

        return (new BobResource($bob))
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
        $attributes = $this->validate($request, $this->rules());

        /** @var  Bob $bob */
        $bob = Bob::where('id', $id)->firstOrFail();
        $bob->fill($attributes)->saveOrFail();

        return new BobResource($bob);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bob = Bob::where('id', $id)->first();
        if ($bob) {
            $bob->delete();
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
        ];
    }
}
