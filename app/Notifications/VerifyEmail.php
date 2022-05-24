<?php

namespace App\Notifications;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    /**
     * @inheritDoc
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }
}
