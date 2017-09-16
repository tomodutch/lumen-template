<?php

namespace App\Http\Controllers;

use App\Battle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Resources\Battle as BattleResource;
use App\Resources\BattleCollection as BattleCollectionResource;

class BattleController extends Controller
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
        $query = Battle::query();
        $total = $query->count();
        $battles = $query->skip($skip)->take($take)->get();

        return (new BattleCollectionResource($battles))
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
        $battle = Battle::where('id', $id)->firstOrFail();

        return new BattleResource($battle);
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

        /** @var  Battle $battle */
        $battle = tap(new Battle)->fill($attributes);
        $battle->saveOrFail();

        return (new BattleResource($battle))
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

        /** @var  Battle $battle */
        $battle = Battle::where('id', $id)->firstOrFail();
        $battle->fill($attributes)->saveOrFail();

        return new BattleResource($battle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    mixed $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $battle = Battle::where('id', $id)->first();
        if ($battle) {
            $battle->delete();
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
