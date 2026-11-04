<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum Users
    |--------------------------------------------------------------------------
    |
    | Cap on how many users may exist at once (demo / starter overflow control).
    | Exposed to the SPA via window.AppConfig. When creating a user would exceed
    | this limit, the oldest non-protected user is removed.
    |
    */

    'max' => (int) env('USERS_MAX', 10),

];
