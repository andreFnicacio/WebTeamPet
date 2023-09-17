<?php

namespace Modules\Vindi\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Vindi\Services\VindiService;

class VindiServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Vindi';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'vindi';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerCommands();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->app->singleton(VindiService::class, function ($app) {
            return new VindiService(
                strval(config('services.vindi.url')),
                strval(config('services.vindi.token'))
            );
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $commands = collect(glob(base_path(sprintf('%s/*/Console/*.php', config('modules.namespace')))))
            ->map(function ($item) {
                preg_match(sprintf("/%s.*/", config('modules.namespace')), $item, $matches);
                return str_replace(['/', '.php'], ["\\", ''], $matches[0]);
            })->toArray();

        $this->commands($commands);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
