<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

return [
    "name" => "manager",

    "clients" => [
        "client-name" => [
            "secret"   => "secret",
            "base_url" => "http://127.0.0.1/api/v1",
            "models"   => [
                \App\User::class => [
                    "uri"         => "test",
                    "transformer" => App\Api\UserApiTransformer::class,
                ],
            ],
            "options"  => [
                "http" => [
                    "headers" => ["accept" => "text"],
                ],
            ],
        ],
    ],
];
