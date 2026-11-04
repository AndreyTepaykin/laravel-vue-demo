<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Field Length Limits
    |--------------------------------------------------------------------------
    |
    | These limits are enforced by Form Request validation and exposed to the
    | SPA (see resources/views/spa.blade.php) for matching HTML minlength /
    | maxlength attributes. Keep max values aligned with database column sizes
    | where they apply. Password max follows bcrypt's effective input limit.
    |
    */

    'min' => [
        'name' => (int) env('FIELD_MIN_NAME', 1),
        'email' => (int) env('FIELD_MIN_EMAIL', 3),
        'password' => (int) env('FIELD_MIN_PASSWORD', 8),
        'password_reset_token' => (int) env('FIELD_MIN_PASSWORD_RESET_TOKEN', 1),
        'role' => (int) env('FIELD_MIN_ROLE', 1),
    ],

    'max' => [
        'name' => (int) env('FIELD_MAX_NAME', 255),
        'email' => (int) env('FIELD_MAX_EMAIL', 255),
        'password' => (int) env('FIELD_MAX_PASSWORD', 72),
        'password_reset_token' => (int) env('FIELD_MAX_PASSWORD_RESET_TOKEN', 255),
        'role' => (int) env('FIELD_MAX_ROLE', 32),
    ],

];
