<?php

namespace App\Listeners;

use App\Tree;
use App\Events\TreeCacheBecameDirty;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteTreeCache implements ShouldQueue
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
     * @param  TreeCacheBecameDirty  $event
     * @return void
     */
    public function handle(TreeCacheBecameDirty $event)
    {
        Redis::throttle('tree-cache-rebuild')->allow(1)->every(30)->then(function () {
            // Job logic...
            logger('QUEUE: adding TreeCacheBecameDirty');
            Tree::rebuild();
        }, function () {
            // could not get lock, avoid adding to the queue
            logger('QUEUE: avoiding adding TreeCacheBecameDirty');
            return false;
        });
    }
}
