<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum POST Size
    |--------------------------------------------------------------------------
    |
    | This value defines the maximum size (in bytes) for incoming POST requests.
    | Set to 0 to disable the limit. Make sure this is slightly less than your
    | PHP's 'post_max_size' setting.
    |
    */

    'max_post_size' => env('MAX_POST_SIZE', 128 * 1024 * 1024), // Default: 128MB
];