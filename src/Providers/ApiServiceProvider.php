<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Myth\Api\ApiWrapper;
use Myth\Api\Commands\GetSecretCommand;
use Myth\Api\Commands\MakeClientTransformerCommand;
use Myth\Api\Commands\MakeManagerTransformerCommand;
use Myth\Api\Commands\MakeSecretCommand;
use Myth\Api\Facades\Api;
use Myth\Api\Middlewares\AuthenticateMiddleware;
use Myth\Api\Mixins\RequestMixin;

/**
 * Class ApiServiceProvider
 * @package Myth\Api\Providers
 */
class ApiServiceProvider extends ServiceProvider
{

    /** @var string[] config files */
    protected $configs = [
        'myth-client',
        'myth-manager',
    ];

    /** @var string[] migration files */
    protected $migrations = [
        '2020_04_02_045532_myth_api_manager',
        '2020_04_04_045532_myth_api_client',
    ];

    /** @var string[] $commands commands list */
    protected $commands = [
        MakeClientTransformerCommand::class,
        MakeManagerTransformerCommand::class,
        MakeSecretCommand::class,
        GetSecretCommand::class,
    ];

    /**
     * Register services.
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
        AliasLoader::getInstance()->alias("Myth\Api", Api::class);
        $this->app->make("Myth\\Api\\Controllers\\ApiClientController");
        $this->commands($this->commands);
        $this->app['router']->aliasMiddleware('myth.api.auth', AuthenticateMiddleware::class);
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [Api::class];
    }

    /**
     * Bootstrap services.
     * @return void
     * @throws \ReflectionException
     */
    public function boot()
    {
        $publishes = [];
        foreach($this->configs as $config){
            $publishes[__DIR__."/../Configs/{$config}.php"] = config_path("{$config}.php");
        }
        foreach($this->migrations as $migration){
            $publishes[__DIR__."/../Migrations/{$migration}.php"] = database_path("migrations/{$migration}.php");
        }
        $this->publishes($publishes, "myth-api");

        Request::mixin(new RequestMixin());
        Route::model('MythApiManagerModel', Api::class);
    }
}
