<?php

return [

  
    /*
    |--------------------------------------------------------------------------
    | Nexus Coinfiguration 
    |--------------------------------------------------------------------------
    |
    | Misc Nexus configuration values
    | added here in 5.2 see
    | https://laravel.com/docs/5.2/configuration#environment-configuration
    |
    */

    'admin_email' => env('NEXUS_ADMIN_EMAIL', ''),
    'bootstrap_theme' => env('NEXUS_BOOTSTRAP_THEME'),
    'allow_registrations' => env('NEXUS_ALLOW_REGISTRATIONS', false),
    'recent_activity' => env('NEXUS_RECENT_ACTIVITY'),
    'name' => env('NEXUS_NAME'),
    'timezone' => env('NEXUS_TIMEZONE'),
    'recent_edit' => env('NEXUS_RECENT_EDIT'),
    'pagination' => env('NEXUS_PAGINATION'),
    'special_event' => env('NEXUS_SPECIAL_EVENT', ''),
    'notification_check_interval' => env('NEXUS_NOTIFICATION_CHECK_INTERVAL', 2000),
    'google_anaytics_activate' => env('GOOGLE_ANAYTICS_ACTIVATE', false),
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
];
