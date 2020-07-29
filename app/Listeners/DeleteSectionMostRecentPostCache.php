<?php

namespace App\Listeners;

use App\Section;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\MostRecentPostForSectionBecameDirty;

class DeleteSectionMostRecentPostCache
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MostRecentPostForSectionBecameDirty $event
     * @return  void
     */
    public function handle(MostRecentPostForSectionBecameDirty $event)
    {
        xdebug_break();
        Section::forgetMostRecentPostAttribute($event->section_id);
    }
}
