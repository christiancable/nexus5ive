<?php

namespace App\Listeners;

use App\Tree;
use App\Events\TreeCacheBecameDirty;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteTreeCache
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
        Tree::rebuild();
    }
}
