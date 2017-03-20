<?php
use Combustion\Assets\AssetsGateway;
use Combustion\Assets\ImageGateway;
use Combustion\Assets\FileGateway;
return [
    AssetsGateway::class  => [
        "drivers" => [
            ImageGateway::DOCUMENT_TYPE => [
                "config" => [
                    "mimes" =>
                        [
                            "image/jpeg",
                            "image/png"
                        ],
                    'sizes' => [
                        "large" => [
                            "x" => 700,
                            "y" => null
                        ],
                        "medium" => [
                            "x" => 344,
                            "y" => null
                        ],
                        "small" => [
                            "x" => 100,
                            "y" => null
                        ]
                    ]
                ],
                "class" => ImageGateway::class
            ]
        ]
    ],
    FileGateway::class => [
        'cloud_base_url'                => env('COULD_BASE_URL'),
        'cloud_folder'                  => env('CLOUD_FOLDER'),
        'local_driver'                  => 'local',
        'cloud_driver'                  => 's3',
        'local_document_folder'         => storage_path('app/documents'),
        'local_document_folder_name'    => 'documents',
        'keep_local_copy'               => false
    ],
];