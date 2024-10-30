<?php

use Illuminate\Support\Facades\Schedule;

/*
to add to server 
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
*/
// remove old unverified users
Schedule::command('nexus:unverified --confirm')->daily();

// rebuild the search tree cache if it needs it or now
Schedule::command('nexus:rebuildtreecache')->daily();
