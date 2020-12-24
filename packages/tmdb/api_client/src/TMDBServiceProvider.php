<?php

namespace TMDB\ApiClient;

use Illuminate\Support\ServiceProvider;
use TMDB\ApiClient\TMDBController;
class TMDBServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //register TMDB Api controller
        $this->app->bind('TMDB\ApiClient\TMDBController', function($params) {

            $baseUrl = $params['baseUrl'];
            $api_key = $params['api_key'];
            
            return new TMDBController($baseUrl, $api_key);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
