<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapApiAppClienteRoutes();

        $this->mapApiDashboardRoutes();

        $this->mapApiSiteRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $apiVersion = "v1";
        Route::prefix('api/' . $apiVersion)
            ->middleware('api')
            ->as('api.')
            ->namespace($this->namespace."\\API")
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiAppClienteRoutes()
    {
        Route::prefix('api/appCliente')
            ->middleware('api')
            ->as('api.app_cliente')
            ->namespace($this->namespace.'\\API')
            ->group(base_path('routes/api.app_cliente.php'));
    }

    /**
     * Define the "api" routes for the site.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiSiteRoutes()
    {
        Route::prefix('api/site')
            ->middleware('api')
            ->as('api.site')
            ->namespace($this->namespace.'\\API')
            ->group(base_path('routes/api.site.php'));
    }

    /**
     * Define the "api" routes for the application.
     */
    protected function mapApiDashboardRoutes()
    {
        $apiVersion = "v1";
        Route::prefix('api/dashboard/' . $apiVersion)
            ->middleware('api')
            ->as('api.dashboard')
            ->namespace($this->namespace."\\API")
            ->group(base_path('routes/api.dashboard.php'));
    }
}