<?php

namespace Consul\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ConsulServiceProvider
 *
 * @package Consul\Providers
 */
class ConsulServiceProvider extends ServiceProvider
{
    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->offerPublishing();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerConfiguration();
        parent::register();
    }

    /**
     * Offer resources for publishing
     * @return void
     */
    protected function offerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/consul.php'     =>  config_path('consul.php'),
            ], 'consul-config');
        }
    }

    /**
     * Register package configuration
     * @return void
     */
    protected function registerConfiguration(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/consul.php', 'consul');
    }
}
