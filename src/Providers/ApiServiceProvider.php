<?php

namespace Myth\Api\Providers;

use Illuminate\Support\ServiceProvider;
use Myth\Api\ApiWrapper;
use Myth\Api\Commands\MakeTransformerCommand;
use Myth\Api\Facades\Api;
use Myth\Api\Facades\Manager;

class ApiServiceProvider extends ServiceProvider
{

    /** @var string[] $configs config files */
    protected $configs = [
        'myth-client',
        'myth-manager',
    ];

    /** @var string[] $commands commands list */
    protected $commands = [MakeTransformerCommand::class,];

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
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {
        $publishes = [];
        foreach($this->configs as $config){
            $publishes[__DIR__."/../Config/{$config}.php"] = config_path("{$config}.php");
        }
        // $this->publishes([
        //     __DIR__."/../Config/mythclient.php" => config_path("mythclient.php"),
        //
        //     __DIR__."/../Migrations/2020_03_28_171806_myth_api_client.php" => database_path("migrations/2020_03_28_171806_myth_api_client.php"),
        // ], "mythclient");
        // dd($this->app);
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [Manager::class];
    }
}
