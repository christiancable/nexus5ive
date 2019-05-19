<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TopicJumpCacheBecameDirty;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteTopicJumpCache
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
     * @param  TopicJumpCacheBecameDirty  $event
     * @return void
     */
    public function handle(TopicJumpCacheBecameDirty $event)
    {
        Log::debug("Deleting TopicJump Cache");
        Cache::forget('topicIndex');

    }
}
