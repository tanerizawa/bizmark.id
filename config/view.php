<?php

$defaultCompiled = storage_path('framework/views');

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env('VIEW_COMPILED_PATH', $defaultCompiled),
];
