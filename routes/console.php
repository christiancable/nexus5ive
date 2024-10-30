<?php

use Illuminate\Support\Facades\Schedule;

// remove old unverified users
Schedule::command('nexus:unverified --confirm')->daily();

// rebuild the search tree cache if it needs it or now
Schedule::command('nexus:rebuildtreecache')->daily();
