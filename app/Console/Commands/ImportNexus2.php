<?php
namespace App\Console\Commands;

use App\User;
use App\Comment;
use App\Nexus2\Helpers\Detect;
use RecursiveIteratorIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use App\Nexus2\Helpers\Highlighter;
use App\Nexus2\Models\User as Nexus2User;
use App\Nexus2\Models\Menu as Nexus2Menu;

class ImportNexus2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus2:import {--userdir=} {--bbsdir=}';

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
        $this->highlighter = new Highlighter();
    }

    private $hightlighter;

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
        $root = $this->option('userdir');
        if ($root) {
            $this->importUsers($root);
        }
        
        $root = $this->option('bbsdir');
        if ($root) {
            $this->importBBS($root);
        }
        

        /*
        Sections
        */
        
        
        /*
        Topics and Posts
        */
    }
    
    public function importBBS($root)
    {
        rtrim($root, '/');
        if (null != $root) {
            $this->comment("Importing Sections and Files from $root");
        }

        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );
    
        $paths = array($root);

        $articles = [];
        $menus = [];
        foreach ($iter as $path) {
            // file file is text
            if (is_file($path)) {
                $fileContent = file_get_contents($path->getPathName());
                $type = Detect::sniff($fileContent);

                switch ($type) {
                    case 'menu':
                        // menu detection is matching zip files and all sorts
                        // $this->comment($path->getPathName() . " $type");
                        $menus[] = new Nexus2Menu($fileContent, $path->getPathName());
                        break;
                    
                    case 'article':
                        // $this->comment($path->getPathName() . " $type");
                        // $articles[] = $fileContent;
                        break;
                    default:
                        # code...
                        break;
                }
              
                // if (Detect::isMenu($fileContent)) {
                // }
                // $this->comment($path->getFileName());

            
            }
        }
        
        $newUsers = [];
        foreach ($menus as $menu) {
            $this->info($menu->path);
            foreach ($menu->menus as $submenu) {
                $this->info(" [*] " . $submenu['name'] . " - " . $submenu['file']);
            }
            foreach ($menu->articles as $article) {
                $this->info(" - " . $article['name'] . " - " . $article['file']);
            }
            // $this->info("$user->username:");
            // if ($newUser = $this->importUser($user)) {
            //     $this->comment("added");
            //     $newUsers[] = $user;
            // } else {
            //     $this->line('already exists, skipping');
            // }

            $this->newLine();
        }
        
    }

    public function importUsers($root)
    {
        rtrim($root, '/');
        if (null != $root) {
            $this->comment("Importing Users from $root");
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
            'popname'       => $this->highlighter->highlight($user->popname),
            'name'          => $user->name,
            'email'         => $user->username . "@imported",
            'about'         => $this->highlighter->highlight($user->info),
            'totalVisits'   => $user->totalVisits,
            'totalPosts'    => $user->totalPosts,
            'latestLogin'   => $user->latestLogin,
            'created_at'    => $user->created_at,
            'legacy'        => true,
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
                            'name'     => '',
                            'popname'  => '',
                            'about'    => '',
                        ]
                    );
                    $author->save();
                }

                $user = User::where('username', $user->username)->first();

                Comment::create([
                    'user_id' => $user->id,
                    'author_id' => $author->id,
                    'text' =>  $this->highlighter->highlight($text),
                    'read' => true,
                ]);
                $commentCount++;
            }
        }
       
        return $commentCount;
    }
}
