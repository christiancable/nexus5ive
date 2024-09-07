<?php

namespace App\Listeners;

use App\Section;

class DeleteSectionMostRecentPostCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Section::forgetMostRecentPostAttribute($event->section_id);
    }
}
