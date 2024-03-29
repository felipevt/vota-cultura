<?php

namespace App\Modules\Localidade\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Modules\Localidade\Http\Controllers';

    public function map()
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::group([
            'namespace'  => $this->namespace,
            'prefix'     => 'api',
        ], function ($router) {
            require module_path('localidade', 'Routes/api.php', 'app');
        });
    }
}
