<?php

namespace App\Providers;

use App\Resources\ArticleCollection;
use App\Resources\Generic;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /*
         * Remove the following resources lines to wrap
         * the response values with a data attribute
         */
        Resource::withoutWrapping();
        ResourceCollection::withoutWrapping();

        Generic::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
