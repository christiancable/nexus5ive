<?php
namespace App\Console\Commands;

use App\User;
use App\Comment;
use App\Nexus2\Helpers\Detect;
use RecursiveIteratorIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use App\Nexus2\Models\User as Nexus2User;

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

        /*
        Users
        */
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
        $users = [];
        $errors = [];

        foreach ($iter as $path) {
            // file file is text
            if (is_file($path)) {
                if ('NEXUS.UDB' == strtoupper($path->getFileName())) {
                    $udb = file_get_contents($path->getPathName());

                    try {
                        $comments = file_get_contents($path->getPath() . DIRECTORY_SEPARATOR . "COMMENTS.TXT");
                    } catch (\Throwable $th) {
                        $comments = '';
                    }

                    try {
                        $info = file_get_contents($path->getPath() . DIRECTORY_SEPARATOR . "INFO.TXT");
                    } catch (\Throwable $th) {
                        $info = '';
                    }

                    try {
                        $users[] = new Nexus2User($udb, $info, $comments);
                    } catch (\Throwable $th) {
                        $errors[] = $path->getPath();
                    }
                }
            }
        }

        $newUsers = [];
        foreach ($users as $user) {
            $this->info("$user->username:");
            if ($newUser = $this->importUser($user)) {
                $this->comment("added");
                $newUsers[] = $user;
            } else {
                $this->line('already exists, skipping');
            }

            $this->newLine();
        }

        foreach ($newUsers as $user) {
            $count = $this->addCommentsForUser($user);
            $this->comment("Added $count comments for $user->username");
        }

        $this->newLine();
        $this->comment("Added " . count($newUsers) . " users");

        /*
        Sections
        */

        /*
        Topics and Posts
        */
    }


    public function importUser($user)
    {
        $existingUser = User::where('username', $user->username)->first();
        if ($existingUser) {
            return false;
        }

        $newUser = User::factory()->make(
            [
            'username'      => $user->username,
            'popname'       => $user->popname,
            'name'          => $user->name,
            'email'         => $user->username . "@imported",
            //@todo parse info for nx2 formatting
            'about'         => "$user->info\n\n-- legacy user added automatically by import --",
            'totalVisits'   => $user->totalVisits,
            'totalPosts'    => $user->totalPosts,
            'latestLogin'   => $user->latestLogin,
            'created_at'    => $user->created_at,
            ]
        );

        $newUser->save();
        return $newUser;
    }

    public function addCommentsForUser($user)
    {
        // valid nx5 comments need a user and text - nx2 allowed freetext so we need to be careful
        $pattern = '/{(.*)} : (.*)/m';
        $commentCount = 0;

        foreach ($user->comments as $comment) {
            preg_match_all($pattern, $comment, $matches, PREG_SET_ORDER, 0);
            if (count($matches) && 3 == count($matches[0])) {
                $username = $matches[0][1];
                $text = $matches[0][2];

                $author = User::where('username', $username)->first();
                if (!$author) {
                    $author = User::factory()->make(
                        [
                            'username' => $username,
                            'email'    => $username . "@imported",
                            'name'     => '-- legacy user added automatically by import --',
                            'popname'  => '-- legacy user added automatically by import --',
                            'about'    => '-- legacy user added automatically by import --',
                        ]
                    );
                    $author->save();
                }

                $user = User::where('username', $user->username)->first();

                Comment::create([
                    'user_id' => $user->id,
                    'author_id' => $author->id,
                    // @todo parse text for nx2 formatting
                    'text' => $text,
                    'read' => true,
                ]);
                $commentCount++;
            }
        }
       
        return $commentCount;
    }
}
