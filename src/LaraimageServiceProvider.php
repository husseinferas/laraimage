<?php

namespace HusseinFeras\Laraimage;

use Illuminate\Support\ServiceProvider;

class LaraimageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublishConfig();
    }

    /**
     * undocumented function
     *
     * @return void
     */
    protected function registerPublishConfig()
    {
        $configPath = __DIR__ . '/config/laraimage.php';
        $publishPath = $this->app->configPath('laraimage.php');

        $this->mergeConfigFrom($configPath, 'laraimage');
        $this->publishes([ $configPath => $publishPath ], 'config');
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
