<?php

// namespace App\Nexus2\Console\Commands;
namespace App\Console\Commands;

use App\Nexus2\Models\User;
use App\Nexus2\Helpers\Detect;
use RecursiveIteratorIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;

class ImportNexus2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus2:import {--rootdir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports nexus2 data';

    // /**
    //  * Create a new command instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $root = $this->option('rootdir');
        rtrim($root, '/');
        if (null != $root) {
            echo "path is $root\n";
        }

        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        $paths = array($root);
        $map = [];

        foreach ($iter as $path) {
            // file file is text
            if (is_file($path)) {
                $file = file_get_contents($path);
                $type = Detect::sniff($file);
                switch ($type) {
                    case 'article':
                        $map[$type][] = $path;
                        break;
                    case 'menu':
                        $map[$type][] = $path;
                        
                        break;
                    
                    default:
                        # code...
                        break;
                }
                var_dump($map);
            }
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

        $newUser = User::importUserDataBase($udb);
        if (false === $newUser) {
            $existingUser =  User::parseUDB($udb);
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
            $newUser->about = User::parseInfo($infoFile);
        }

        $newUser->save();
        $this->info("Imported {$newUser->username}");
    }
}
