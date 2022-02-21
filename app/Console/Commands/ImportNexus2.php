<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\User;
use App\Post;
use App\Topic;
use App\Comment;
use App\Section;
use Illuminate\Support\Str;
use App\Nexus2\Models\Menu;
use App\Nexus2\Models\Article;
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
    public function filePathKey($filename, $root)
    {
        // if filename starts with a slash then return filename
        // else return $root + $filename
    }

    /*

    idea 
    Users:
        [x] import all the users from USR
        [x] import all the comments for the users
        [x] import all the users only found in the comments
    Files:
        - get all the files
        - ignore the obvious ones
        - try to make menus for the rest
            - import the successful menus
            - for each menu import the articles
                - for each article import each post
                - import any unknown users
    What new things do we need in nexus5?:
        [ ] preamble for topics


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
                    continue;
                }


                // if we actually have a menu then assume that it at least has menus or articles
                $count = $menu->articles() + $menu->menus();
                if ($count) {
                    $menus[$menu->key()] = $menu;
                }
            }
        }

        if (count($menus)) {
            $this->info("Found " . count($menus) . " menus");
        }
        
        //@todo deal with links between menus
        foreach ($menus as $menu) {
            // create menu
            $this->info($menu->key());
            $this->info($menu->heading());
            $this->info($menu->title());
            $section = new Section();         
            $section->parent()->associate(Section::first());
            $section->moderator()->associate(User::first());
            
            $section->title = $menu->key(); //@todo section name
            $section->intro = $menu->heading();
            $section->save();

            // add articles for menu
            foreach ($menu->articles() as $articlelink) {
                try {
                    $article = new Article($articlelink['file']);
                    $articles[$articlelink['file']] = $article;
                } catch (\Throwable $th) {
                    //throw $th;
                    // couldn't import article
                    $this->warn($menu->key() . ' -- ' . $articlelink['file'] . ' ' . $th->getMessage());
                    continue;
                }
                $this->info("+ {$articlelink['file']}");
                $topic = new Topic([
                    'title' => $article->path(), //@todo topic title
                    'intro' => $article->preamble(),
                ]);

                $topic->section()->associate($section);
                $topic->save();

                foreach ($article->comments() as $comment) {

                    
                    $post = new Post([
                        'title' => $comment['subject'],
                        'text' => $comment['body'],
                        'popname' => $comment['popname'],
                    ]);

                    $dateStr = $comment['date']['date'] . " " . $comment['date']['time'];
                    try {
                        $post->time = Carbon::createFromFormat('d/m/Y G:i:s', $dateStr);
                    } catch (\Throwable $th) {
                    }
                    // get post author
                    $author = User::where('username', $comment['username'])->first();
                    if (!$author) {
                        $author = User::factory()->make(
                            [
                                'username' => $comment['username'],
                                'email'    => $comment['username'] . "@imported",
                                'name'     => '',
                                'popname'  => '',
                                'about'    => '',
                                'legacy'   => true,
                            ]
                        );
                        $author->save();
                    }
                    $post->author()->associate($author);
                    $post->topic()->associate($topic);

                    $post->save();
                }
            }
        }
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
                        $users[] = new Nexus2User($path->getPath());
                    } catch (\Throwable $th) {
                        $errors[] = $path->getPath();
                    }
                }
            }
        }
        // dd($errors, $users);
        $newUsers = [];
        foreach ($users as $user) {
            $this->info("{$user->username()}:");
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
            $this->comment("Added $count comments for {$user->username()}");
        }

        $this->newLine();
        $this->comment("Added " . count($newUsers) . " users");
    }
    public function importUser($user)
    {
        $existingUser = User::where('username', $user->username())->first();
        if ($existingUser) {
            return false;
        }

        $newUser = User::factory()->make(
            [
            'username'      => $user->username(),
            'popname'       => $this->highlighter->highlight($user->popname()),
            'name'          => $user->realName(),
            'email'         => $user->username() . "@imported",
            'about'         => $this->highlighter->highlight($user->info()),
            'totalVisits'   => $user->noOfTimesOn(),
            'totalPosts'    => $user->noOfEdits(),
            // 'latestLogin'   => $user->lastOn(),
            // 'created_at'    => $user->created(),
            'legacy'        => true,
            ]
        );

        $newUser->save();
        return $newUser;
    }

    public function addCommentsForUser($legacyUser)
    {
        $commentCount = 0;

        foreach ($legacyUser->comments() as $comment) {

            $username = $comment["username"];
            $text = $comment["body"];
    

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
            
            $user = User::where('username', $legacyUser->username())->first();

            Comment::create([
                'user_id' => $user->id,
                'author_id' => $author->id,
                'text' =>  $this->highlighter->highlight($text),
                'read' => true,
            ]);
            $commentCount++;
        }

        return $commentCount;
    }
}
