<?php

namespace Myth\Api\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Myth\Api\ApiWrapper;
use Myth\Api\Commands\MakeApiTransformerCommand;
use Myth\Api\Facades\Api;

class ApiServiceProvider extends ServiceProvider
{

    /** @var string[] $configs config files */
    protected $configs = [
        'myth-client',
        'myth-manager',
    ];
    protected $migrations = [
        '2020_04_02_045532_myth_api_client',
    ];

    /** @var string[] $commands commands list */
    protected $commands = [MakeApiTransformerCommand::class,];

    /**
     * Register services.
     * @return void
     */
    public function register()
    {
        foreach($this->configs as $config){
            $this->mergeConfigFrom(__DIR__."/../Configs/{$config}.php", $config);
        }

        $this->app->singleton(Api::class, function ($app) {
            $config = $app['config'];
            return new ApiWrapper($config['myth-manager'], $config['myth-client']);
        });
        $this->commands($this->commands);

        // AliasLoader::getInstance()->alias("Myth\Api", Api::class);
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {
        $publishes = [];
        foreach($this->configs as $config){
            $publishes[__DIR__."/../Configs/{$config}.php"] = config_path("{$config}.php");
        }
        foreach($this->migrations as $migration){
            $publishes[__DIR__."/../Migrations/{$migration}.php"] = database_path("{$migration}.php");
        }
        $this->publishes($publishes, "myth-api");
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [Api::class];
    }
}
