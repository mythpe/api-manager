<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Api Manager Name
    |--------------------------------------------------------------------------
    |
    | This your name for authentication with your clients
    |
    */
    "name"    => "manager",

    /*
    |--------------------------------------------------------------------------
    | Api Manager Clients
    |--------------------------------------------------------------------------
    |
    | This array contains your clients list, so that you can synchronize data with them
    |
    |
    | Client Array config, Available options:
    |   - String "ARRAY-KEY" client name into your system
    |   - String secret
    |   - String base_url
    |   - Array options
    |   - Array models
    |
    | * secret: Client's authentication secret, which you can obtain from your client
    |
    | * base_url: Client api url. Example: http://127.0.0.1/api/v1 | https://127.0.0.1/api
    |
    | * options: Array of your client options. available options:
    |   - http: GuzzleHttp\Client options. See: http://docs.guzzlephp.org
    |
    | * models: Array of your models will be able to syc with specific client.
    |   - model options: [ Namespace => options ]
    |   - uri: The url Or prefix of model at client software. Example: BASE_URL/MODEL_URI. http://127.0.0.1/api/v1/user
    |   - transformer: This option will use automatically when you system sync 'send' model data to your client.
    |   You must make a new transformer for each client's model.
    |   Try command line: php artisan myth:make-api-transformer {name}
    |
    |  @important: your array key of client must be the name of client.
    |
    */
    "clients" => [
        "client-name" => [
            "secret"   => "secret",
            "base_url" => "http://127.0.0.1/api/v1",
            "models"   => [
                App\User::class => [
                    "uri"         => "user",
                    "transformer" => App\Api\UserApiTransformer::class,
                ],
            ],
            "options"  => [
                "http" => [],
            ],
        ],
    ],
];
