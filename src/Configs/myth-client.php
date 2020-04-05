<?php

return [

    /**
     * The strength of the security is generated based on this key. You have the option What to place here
     * @String
     */
    "secret"           => env('APP_NAME', 'MyTh'),

    /**
     * File system disk
     * @Illuminate\Support\Facades\Storage
     */
    'file_system_disk' => 'local',


    /*
    |--------------------------------------------------------------------------
    | Api Client Managers
    |--------------------------------------------------------------------------
    |
    | This array contains your Managers list
    |
    |
    | Manager Array config, Available options:
    |   - String "ARRAY-KEY" manager name
    |   - Array models
    |   - Array options
    |
    | * options: Array of your manager options. available options:
    |
    | * models: Array of your models will be able to syc with specific manager.
    |   - model options: [ Namespace => options ]
    |   - uri: The url Or prefix of model at your software. Example: BASE_URL/MODEL_URI. http://127.0.0.1/api/v1/user
    |   - transformer: This option will use automatically when your system sync model data with manager.
    |   You must make a new transformer for each manager's model.
    |   Try command line: php artisan myth:make-manager-transformer {name}
    |
    |  @important: your array key of manager must be the name of manager.
    |
    */
    "managers"         => [
        "manager-name" => [
            "models"  => [
                \App\User::class => [
                    "uri"         => "user",
                    "transformer" => "UserTransformer",
                ],
            ],
            "options" => [],
        ],
    ],
];
