<?php

namespace App\Console\Commands;

use App\Mode;
use App\User;
use App\Topic;
use App\Theme;
use Exception;
use App\Section;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

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
        $this->info('Creating administrator, default mode, section and first topic...');
        $administrator = User::first();

        if (!$administrator) {
            $this->info("Please enter in values for the administrator account. Don't worry You can change this later.");
            $username = $this->ask('Username');
            $email = $this->ask('Email Address');
            $password = $this->ask('Password');
            
            $administrator = User::factory()->make(
                [
                'username'      => $username,
                'name'          => 'Administrator',
                'email'         => $email,
                'password'      => Hash::make($password),
                'administrator' => true,
                ]
            );

            try {
                $administrator->save();
            } catch (Exception $e) {
                $this->error('Failed to add administrator ' . $e);
            }
        } else {
            $this->error('There is already a user account');
        }

        $mainMenu = Section::first();

        if (!$mainMenu) {
            $this->info("Please enter in values for the main menu. Don't worry You can change this later.");
            $title = $this->ask('Title');
            
            $mainMenu = Section::factory()->make(
                [
                'title' => $title
                ]
            );
            $mainMenu->moderator()->associate($administrator);
            $mainMenu->parent()->associate(null);

            try {
                $mainMenu->save();
            } catch (Exception $e) {
                $this->error('Failed to add main menu ' . $e);
            }
        } else {
            $this->error('There is already a main menu');
        }

        $firstTopic = Topic::first();

        if (!$firstTopic) {
            $this->info("Please enter in values for the first topic. Don't worry You can change this later.");
            $title = $this->ask('Title');

            $firstTopic = Topic::factory()->make(
                [
                'title' => $title
                ]
            );
            
            $firstTopic->section()->associate($mainMenu);

            try {
                $firstTopic->save();
            } catch (Exception $e) {
                $this->error('Failed to add first topic ' . $e);
            }
        } else {
            $this->error('There is already a topic');
        }

        $defaultMode = Mode::first();

        if (!$defaultMode) {
            $defaultMode = Mode::factory()->make(
                [
                    'name' => 'Default',
                    'welcome' => <<< HTML
 <span class="lead">There are those who believe that **spodding** here began out there, far across the network, with tribes of users who may have been the forefathers of the _Prestoneites_, or the _Facebookers_, or the _Twitters_.</span>

That they may have been the architects of the great forums, or the lost civilizations of _Monochrome_ or _anonyMUD_. Some believe that there may yet be brothers of man who even now fight to survive somewhere beyond the screen...                    
HTML,
                    'active' => true,
                    'override' => false
                ]
            );

            try {
                $defaultMode->theme()->associate(Theme::first());
                $defaultMode->save();
            } catch (Exception $e) {
                $this->error('Failed to add default mode ' . $e);
            }
        }
    }
}
