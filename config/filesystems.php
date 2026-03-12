<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | ✅ ARCHITECTURE FIX: Changed the default fallback from 'local' to 's3'.
    | This guarantees that any third-party packages or native Laravel functions 
    | that do not explicitly declare a disk will automatically route to S3.
    |
    */

    'default' => env('FILESYSTEM_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // Do not delete the local/public disks. Laravel requires them for 
        // internal framework operations like caching, test stubs, and temp file processing.
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            
            // ✅ ARCHITECTURE FIX 1: Explicitly set visibility to 'public'. 
            // Because your application uses `$file->url` to render images directly, 
            // the uploaded S3 objects must have public-read ACLs applied upon upload.
            'visibility' => 'public', 

            // ✅ ARCHITECTURE FIX 2: Set 'throw' to true.
            // In our Models (like User, Slide, Leader) we wrote robust `try/catch` blocks.
            // If 'throw' is false, Laravel will silently swallow AWS connection errors, 
            // returning `false` instead of triggering our `catch` block for fallback images.
            'throw' => true, 
            
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];