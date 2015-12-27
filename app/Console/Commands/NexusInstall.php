<?php

namespace Nexus\Console\Commands;

use Illuminate\Console\Command;

class NexusInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates an admin user, main menu and sample topic';

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
        // we are assuming that the sysop is always the first user
        $this->info('Creating administrator, default section and first topic...');
        $user = \Nexus\User::first();

        if (!$user) {
            $this->info("Please enter in values for the administrator account. Don't worry You can change this later.");
            $username = $this->ask('Username');
            $email = $this->ask('Email Address');
            $password = $this->ask('Password');
            
            $administrator = new \Nexus\User;
            $administrator->username = $username;
            $administrator->name = 'Administrator';
            $administrator->email = $email;
            $administrator->password = $password;
            $administrator->administrator = true;

            try {
                $administrator->save();
            } catch (\Exception $e) {
                        $this->error('Failed to add administrator ' . $e);
            }
        } else {
            $this->error('There is already a user account');
        }

        $section = \Nexus\Section::first();

        if (!$section) {
            $this->info("Please enter in values for the main menu. Don't worry You can change this later.");
            $title = $this->ask('Title');
            
            $mainmenu = new \Nexus\Section;
            $mainmenu->title = $title;
            $mainmenu->user_id = $administrator->id;

            try {
                $mainmenu->save();
            } catch (\Exception $e) {
                        $this->error('Failed to add main menu ' . $e);
            }
        } else {
            $this->error('There is already a main menu');
        }

        $topic = \Nexus\Topic::first();

        if (!$topic) {
            $this->info("Please enter in values for the first topic. Don't worry You can change this later.");
            $title = $this->ask('Title');
            
            $firstTopic = new \Nexus\Topic;
            $firstTopic->title = $title;
            $firstTopic->section_id = $mainmenu->id;

            try {
                $firstTopic->save();
            } catch (\Exception $e) {
                        $this->error('Failed to add first topic ' . $e);
            }
        } else {
            $this->error('There is already a topic');
        }
    }
}
