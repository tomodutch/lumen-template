<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Item as ItemResource;
use App\Resources\ItemCollection as ItemCollectionResource;

class ItemController extends Controller
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
        $query = Item::query();
        $total = $query->count();
        $items = $query->skip($skip)->take($take)->get();

        return (new ItemCollectionResource($items))
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
        $item = Item::where('id', $id)->firstOrFail();

        return new ItemResource($item);
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

        /** @var  Item $item */
        $item = tap(new Item)->fill($attributes);
        $item->saveOrFail();

        return (new ItemResource($item))
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

        /** @var  Item $item */
        $item = Item::where('id', $id)->firstOrFail();
        $item->fill($attributes)->saveOrFail();

        return new ItemResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::where('id', $id)->first();
        if ($item) {
            $item->delete();
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
