<?php
namespace Nexus\Helpers;

/* 
    send flash messages to the session
*/

class FlashHelper
{
    public static function showAlert($message, $level = 'info')
    {
        $headerMessage = [
            'body'  => $message,
            'level' => $level
        ];
        \Session::flash('headerAlert', $headerMessage);
    }
}
