<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // // Renderer settings
        // 'renderer' => [
        //     'template_path' => __DIR__ . '/../templates/',
        // ],

        // Monolog settings
        'logger' => [
            'name' => 'sebuku-api',
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];
