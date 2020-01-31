<?php
namespace App\Helpers;

/*
    google analytics methods
*/

class GoogleAnalyticsHelper
{
    /**
     * returns an onclick event snippet suitable for a href
     **/
    public static function onClickEvent($category, $label, $action = 'Click')
    {
        $return = "";

        if (env('GOOGLE_ANAYTICS_ACTIVATE') == 'true') {
            $return =  "onClick=\"ga('send', 'event', '$category', '$action', '$label');\"";
        }

        return $return;
    }
}
