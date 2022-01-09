<?php

namespace App\Console\Commands;

use App\User;
use App\Comment;
use App\Section;
use App\Nexus2\Models\Menu;
use App\Nexus2\Models\Article;
use App\Nexus2\Helpers\Detect;
use RecursiveIteratorIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use App\Nexus2\Helpers\Highlighter;
use App\Nexus2\Models\User as Nexus2User;
use App\Nexus2\Models\Menu as Nexus2Menu;
use Illuminate\Support\Str;

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
    public function filePathKey($filename, $root)
    {
        // if filename starts with a slash then return filename
        // else return $root + $filename
    }

    /*

    idea 
    - import all the users
    - get all the files
    - ignore the obvious ones
    - try to make menus for the rest
        - import the successful menus
        - for each menu import the articles
        - for each article import any new users
    - run out of memory
    */
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
        $ignoredExtensions = ['.PAI', '.ZIP', '.EXE', '.BAT', '.$$$', '.COM', '.QQQ', '.BAK'];
        foreach ($iter as $path) {
            if (is_file($path)) {
                if (Str::endsWith(Str::upper($path->getFileName()), $ignoredExtensions)) {
                    // skip ignored files
                    continue;
                };
                try {
                    $menu = new Menu($path, $root);
                } catch (\Throwable $th) {
                    $this->error($th->getMessage());
                }

                // if we actually have a menu then assume that it at least has menus or articles
                $count = $menu->articles() + $menu->menus();
                if ($count) {
                    $menus[$menu->key()] = $menu;
                }
            }
        }

        if (count($menus)) {
            foreach ($menus as $menu) {
                // $this->info($menu->key());
            }
            $this->info("Found " . count($menus) . " menus");
        }

        foreach ($menus as $menu) {
            foreach ($menu->articles() as $articlelink) {
                // dd($articlelink);
                try {
                    $article = new Article($articlelink['file']);
                    // $this->info("+ {$articlelink['file']}");
                    $articles[$articlelink['file']] = $article;
                } catch (\Throwable $th) {
                    //throw $th;
                    // couldn't import article
                    $this->warn($menu->key() . ' -- ' . $articlelink['file'] . ' ' . $th->getMessage());
                }
            }

            foreach ($menu->menus() as $menulink) {
                // dd($menulink);
                // check if we have that menu already
            }
        }

        $this->info("Imported " . count($articles) . " articles");

        
    }

    /**
     * importMenu
     *
     * @param  mixed $menu
     * @param  mixed $title
     * @return void
     *
     * @todo
     *  create section
     *  create topics
     *  find parent for section
     */
    public function importMenu($menu, $title = '')
    {
        $menu->title = $title;
        // xdebug_break();

        $this->info(" [*] " . $menu->title . " - " . $menu->path);
        xdebug_break();
        $section = new Section([
            'title' => $menu->title
        ]);

        $section->moderator()->associate(User::first());
        $section->parent()->associate(Section::first());

        $section->save();

        return;
        // foreach ($menu->articles as $article) {
        //     $this->info(" [-] " . $article['name']);
        // }

        // foreach ($menu->menus as $submenu) {
        //     xdebug_break();
        //     // $this->info(" [-] " . $article['name']);
        // }

        // $sect

        // return true;
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
