<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dingo\Api\Transformer\Factory as TransformerFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
<<<<<<< Updated upstream
        //
=======
        $requestString = Request::method() . ' ' . Request::fullUrl();

        Log::debug(
            ' >>> ' . $requestString,
            Request::input());

        $t = app(TransformerFactory::class);

        $t->register('App\Model\Request', 'App\Transformer\Base');

        App::terminating(function() use ($requestString) {
            Log::debug(' <<< ' . $requestString);
        });
>>>>>>> Stashed changes
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
<<<<<<< Updated upstream
        //
=======
		if ($this->app->environment() == 'local') {
			$this->app->register(\Iber\Generator\ModelGeneratorProvider::class);
		}
>>>>>>> Stashed changes
    }
}
