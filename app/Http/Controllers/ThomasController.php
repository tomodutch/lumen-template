<?php

namespace App\Http\Controllers;

use App\Thomas;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Thomas as ThomasResource;
use App\Resources\ThomasCollection as ThomasCollectionResource;

class ThomasController extends Controller
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
        $query = Thomas::query();
        $total = $query->count();
        $thomass = $query->skip($skip)->take($take)->get();

        return (new ThomasCollectionResource($thomass))
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
        $thomas = Thomas::where('id', $id)->firstOrFail();

        return new ThomasResource($thomas);
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

        /** @var  Thomas $thomas */
        $thomas = tap(new Thomas)->fill($attributes);
        $thomas->saveOrFail();

        return (new ThomasResource($thomas))
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

        /** @var  Thomas $thomas */
        $thomas = Thomas::where('id', $id)->firstOrFail();
        $thomas->fill($attributes)->saveOrFail();

        return new ThomasResource($thomas);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $thomas = Thomas::where('id', $id)->first();
        if ($thomas) {
            $thomas->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function rules()
    {
        return [
            'id' => [
                'required',
                'string',
                new \App\Rules\ValidUUID(),
            ],
            'title' => [
                'required',
                'string',
            ],
            'age' => [
                'required',
                'numeric',
            ],
            'date_of_birth' => [
                'required',
                'date',
            ],
        ];
    }
}
