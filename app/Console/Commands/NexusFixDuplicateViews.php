<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NexusFixDuplicateViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:fixDuplicateViews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =
        'Remove duplicate views from bug in previous nexus. Not for normal use. Bug fix https://trello.com/c/arfNHMgT';

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
        $this->info('Looking for Duplicates...');
        $users = \App\User::all();

        foreach ($users as $user) {
            $sortedViews = $user->views->sortBy('topic_id');
            $previousView = null;
            foreach ($sortedViews as $view) {
                if ($previousView) {
                    if ($view->topic_id === $previousView->topic_id) {
                        $this->info("Cur\t".$view);
                        $this->info("Pre\t".$previousView."\n");
                        $view->delete();
                        $view = null;
                    }
                }
                $previousView = $view;
            }
        }
    }
}
