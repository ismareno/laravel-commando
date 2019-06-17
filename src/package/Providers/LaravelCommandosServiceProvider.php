<?php

namespace Vkovic\LaravelCommandos\Providers;

use App\Console\Commands\DbDrop;
use Illuminate\Support\ServiceProvider;
use Vkovic\LaravelCommandos\Console\Commands\Database\DbCreate;

class LaravelCommandosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        $this->commands([
            // Database related commands
            DbCreate::class,
            //DbDrop::class,
            // Other commands
        ]);
    }
}