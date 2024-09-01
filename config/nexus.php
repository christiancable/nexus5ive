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
    'allow_registrations' => env('NEXUS_ALLOW_REGISTRATIONS', false),
    'recent_activity' => env('NEXUS_RECENT_ACTIVITY'),
    'name' => env('NEXUS_NAME'),
    'timezone' => env('NEXUS_TIMEZONE'),
    'recent_edit' => env('NEXUS_RECENT_EDIT'),
    'pagination' => env('NEXUS_PAGINATION', 10),
    'comment_pagination' => env('NEXUS_COMMENT_PAGINATION', 50),
    'notification_check_interval' => env('NEXUS_NOTIFICATION_CHECK_INTERVAL', 2000),
    'google_anaytics_activate' => env('GOOGLE_ANAYTICS_ACTIVATE', false),
    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
    'placeholder_image' => env('NEXUS_PLACEHOLDER_IMAGE', false),
    'logo_image' => env('NEXUS_LOGO_IMAGE'),
    'subtitle' => env('NEXUS_SUBTITLE', ''),
    'log_verified_user_level' => env('NEXUS_LOG_VERIFIED_USER_LEVEL', 'alert'),
];
