<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/*
    send flash messages to the session
*/

class FlashHelper
{
    public static function showAlert($message, $level = 'info')
    {
        $headerMessage = [
            'body' => Str::markdown($message),
            'level' => $level,
        ];
        Session::flash('headerAlert', $headerMessage);
    }
}
