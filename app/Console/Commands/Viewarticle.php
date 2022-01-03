<?php

namespace App\Console\Commands;

use App\Nexus2\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ViewArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus2:viewArticle {filename}';

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

        $article = new Article($filename);

        $this->info("Parsing $filename");
        
        if ($article->first()) {
            $this->info("from {$article->date()}\n");
        } else {
            $this->warn('No comments found in article');
        }

        $this->comment($article->preamble());

        foreach ($article->comments() as $comment) {
            $this->comment($comment['date']);
            $this->comment("({$comment['popname']}) {$comment['username']}");
            if ($comment['subject']) {
                $this->comment("Subject: " . $comment['subject']);
            }
            $this->comment("\n" . $comment['body']. "\n");
            $this->info('---');
        }
    }
}
