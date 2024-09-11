<?php

namespace App\Listeners;

use App\Events\MostRecentPostForSectionBecameDirty;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Section;

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
     * @return void
     */
    public function handle(MostRecentPostForSectionBecameDirty $event)
    {
        Section::forgetMostRecentPostAttribute($event->section_id);
    }
}
