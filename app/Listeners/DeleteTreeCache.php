<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;
use App\Events\TreeCacheBecameDirty;
use App\Models\Tree;

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
     */
    public function handle(TreeCacheBecameDirty $event): void
    {
        if (config('queue.default') === 'redis') {
            Redis::throttle('tree-cache-rebuild')->allow(1)->every(30)->then(function () {
                // Job logic...
                logger('QUEUE: processing TreeCacheBecameDirty');
                $this->executeJob();
            }, function () {
                // could not get lock, avoid adding to the queue
                logger('QUEUE: skipped TreeCacheBecameDirty due to throttling');

                return false;
            });
        } else {
            logger('QUEUE: use redis to throttle TreeCacheBecameDirty jobs');
            $this->executeJob();
        }
    }

    public function executeJob()
    {
        Tree::rebuild();
    }
}
