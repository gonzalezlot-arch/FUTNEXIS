<?php

namespace App\Modules\Core\Support;

class AppUrl
{
    /**
     * Build an absolute link from the configured application URL, never from the request host.
     *
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = []): string
    {
        return rtrim((string) config('app.url'), '/').route($name, $parameters, false);
    }
}
