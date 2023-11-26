<?php
return [
    "gitee"  => [
        'client_id'    => env('login.gitee_client_id', ''),
        'client_secret'=>  env('login.gitee_client_secret', ''),
        'redirect_uri' =>  env('login.gitee_callback', ''),

    ],

    "github" => [
        'client_id'      =>  env('login.github_client_id', ''),
        'client_secret'  => env('login.github_client_secret', ''),
        'redirect_uri'   =>  env('login.github_callback', ''),
    ]
];
