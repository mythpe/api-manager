<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

return [

    /**
     * The strength of the security is generated based on this key. You have the option What to place here
     * @String
     */
    "secret"           => "",

    /**
     * File system disk
     * @Illuminate\Support\Facades\Storage
     */
    'file_system_disk' => 'local',

    /**
     * List of managers will be make connection with application
     * array["manager-name" = String[] application entries'Models']
     * Example:
     * [ 'manager-name' => [ "\\App\\User", "FullNamespace\\namespace" ]  ]
     */
    "managers"         => [
        "manager-name" => ["App\\User"],
    ],
];
