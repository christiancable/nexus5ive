<?php

namespace App\Console\Commands;

use App\Nexus2\Models\Menu;
use App\Nexus2\Models\Article;
use Illuminate\Console\Command;

class ViewMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus2:viewMenu
                            {filename}
                            {--bbsroot= : BBS root directory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a Nexus2 Article';

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
        $filename = $this->argument('filename');

        $bbsroot = $this->option('bbsroot');

        try {
            $menu = new Menu($filename, $bbsroot);
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            die();
        }

        $this->info("Parsing $filename");

        $this->newLine();
        $this->comment("Heading");
        $this->info($menu->heading());
        $this->newLine();

        $this->newLine();
        $this->comment("Title");
        $this->info($menu->title());
        $this->newLine();

        $this->comment("Moderators");
        foreach ($menu->owners() as $owner) {
            $this->info($owner);
        }

        $this->newLine();
        $this->comment("Articles");
        foreach ($menu->articles() as $articlelink) {
            $this->info("[{$articlelink['shortcut']}]\t{$articlelink['name']}");
            $this->info("\t\tfile: {$articlelink['file']}");
            $this->info("\t\tread: {$articlelink['read']}\t write:{$articlelink['write']}");
            $this->newLine();
        }

        $continue = true;
        if ($this->confirm('Display Articles?') && $continue) {
            foreach ($menu->articles() as $articlelink) {
                $this->info($articlelink['name']);
                $article = new Article($articlelink['file']);
                $this->comment($article->preamble());

                foreach ($article->comments() as $comment) {
                    $this->comment($comment['date']);
                    $this->comment("({$comment['popname']}) {$comment['username']}");
                    if ($comment['subject']) {
                        $this->comment("Subject: " . $comment['subject']);
                    }
                    $this->comment("\n" . $comment['body'] . "\n");
                    $this->info('---');
                }
                if ($this->confirm('Next article?')) {
                    $continue = true;
                } else {
                    $continue = false;
                }
            }
        }

        
        $this->comment("Menus");
        foreach ($menu->menus() as $menulink) {
            $this->info("[{$menulink['shortcut']}]\t{$menulink['name']}");
            $this->info("\t\tfile: {$menulink['file']}");
            $this->info("\t\tread: {$menulink['read']}");
            $this->newLine();
        }

        // $this->info(print_r($menu->raw(), $menu->debug()));
        // $this->info(print_r($menu->articles()));
        // $this->info(print_r($menu->menus()));
    }
}
