<?php
namespace Nexus\Helpers;

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
        // @todo respect the value of the anaytics settings
        $return = "";

        if (env('GOOGLE_ANAYTICS_ACTIVATE') == 'true') {
            $return =  "onClick=\"ga('send', 'event', '$category', '$action', '$label');\"";
        }

        return $return;
    }
}
