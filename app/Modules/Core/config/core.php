<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated IP addresses or CIDR ranges of the reverse proxies placed
    | in front of the application. Forwarded headers are ignored unless the
    | connection comes from one of them, so this stays empty by default.
    | The wildcard "*" is rejected because it would trust any client.
    |
    */

    'trusted_proxies' => env('TRUSTED_PROXIES', ''),

];
