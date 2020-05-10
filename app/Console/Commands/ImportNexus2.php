<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use App\Helpers\Nexus2\User as Nexus2User;

class ImportNexus2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:importNexus2 {--userdir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports nexus2 data';

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
            $this->importUser($userdir);
        }
    }
    
    public function importUser($userdir)
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

        $newUser = Nexus2User::importUserDataBase($udb);
        if (false === $newUser) {
            $existingUser =  Nexus2User::parseUDB($udb);
            $this->alert("User already exists: {$existingUser['Nick']}");
            return false;
        }

        // INFO.TXT
        $infoFile = $userdir . '/INFO.TXT';
        if (!file_exists($infoFile)) {
            $this->line("No InfoText found for {$newUser->username}");
        } else {
            $handle = fopen($infoFile, "rb");
            $infoFile = stream_get_contents($handle);
            fclose($handle);
            $newUser->about = Nexus2User::parseInfo($infoFile);
        }

        $newUser->save();
        $this->info("Imported {$newUser->username}");
    }
}
