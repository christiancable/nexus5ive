<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TreeHelper;

class NexusRebuildTreeCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:rebuildtreecache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rebuild the tree cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->line('Rebuilding Tree Cache');
        TreeHelper::rebuild();
    }
}
