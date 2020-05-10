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
        $udbFile = $userdir . '/NEXUS.UDB';
        if (file_exists($udbFile)) {
            $handle = fopen($udbFile, "rb");
            $udb = stream_get_contents($handle);
            fclose($handle);
            
            if (false !== $udb) {
                $user = Nexus2User::parseUDB($udb);
                $this->info("Found user {$user['Nick']}");

                $existingUser = User::where('username', $user['Nick'])->first();
                if (null === $existingUser) {
                    $this->info("Importing {$user['Nick']}");
                    $newUser = factory(User::class)->create([
                        'username'  => $user['Nick'],
                        'name'      => $user['RealName'],
                        'popname'   => $user['PopName'],
                    ]);

                    // get comments.txt
                    // get info.txt
                } else {
                    $this->alert("{$user['Nick']} already exists");
                }
            }
        }
    }
}
