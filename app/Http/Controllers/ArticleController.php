<?php

namespace App\Http\Controllers;

use App\Article;
use App\Resources\ArticleCollection as ArticleCollectionResource;
use App\Resources\Article as ArticleResource;
use Illuminate\Http\Request;
use App\Requests\StoreArticle as StoreArticleRequest;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function getMany(Request $request)
    {
        $take = $request->get('take', 10);
        $skip = $request->get('skip', 0);

        /**
         * Naive implementation of pagination
         * You might run into performance issues when the skip is high
         * For a solution see (https://explainextended.com/2009/10/23/mysql-order-by-limit-performance-late-row-lookups/)
         */
        $query = Article::query();
        $total = $query->count();
        $articles = $query->skip($skip)->take($take)->get();

        return (new ArticleCollectionResource($articles))
            ->response()
            ->header('X-PAGINATION-TOTAL', $total)
            ->header('X-PAGINATION-SKIP', $skip)
            ->header('X-PAGINATION-TAKE', $take)
            ->header('X-PAGINATION-SUPPORT', true);
    }

    public function getOne($id)
    {
        $article = Article::where('id', $id)->firstOrFail();

        return new ArticleResource($article);
    }

    public function create(StoreArticleRequest $request)
    {
        /** @var Article $article */
        $article = tap(new Article)->fill($request->validate());
        $article->saveOrFail();

        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(StoreArticleRequest $request, $id)
    {
        $attributes = $request->validate();

        $article = Article::where('id', $id)->firstOrFail();
        $article->fill($attributes)->saveOrFail();

        return new ArticleResource($article);
    }

    public function delete($id)
    {
        $article = Article::where('id', $id)->first();
        if ($article) {
            $article->delete();
        }

        return response('', Response::HTTP_NO_CONTENT);
    }
}
