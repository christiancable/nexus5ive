<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Nexus2\User;

class ViewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus2:viewUser {--userdir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a Nexus2 User';

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
        // are we importing a user?

        $userdir = $this->option('userdir');
        rtrim($userdir, '/');
        if (null != $userdir) {
            $this->viewUser($userdir);
        }
    }
    
    public function viewUser($userdir)
    {
        // NEXUS.UDB
        $udbFile = $userdir . '/NEXUS.UDB';
        if (!file_exists($udbFile)) {
            $this->alert("UserDataBase not found");
            return false;
        }

        $handle = fopen($udbFile, "rb");
        $udb = stream_get_contents($handle);
        fclose($handle);

        if (false === $udb) {
            $this->alert("Could not read UserDataBase at {$udbFile}");
            return false;
        }
        
        $existingUser =  User::parseUDB($udb);

        $this->info(print_r($existingUser, true));
    }
}
