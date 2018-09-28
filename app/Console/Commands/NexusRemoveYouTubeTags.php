<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class NexusRemoveYouTubeTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:removeyoutubetags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unwanted [youtube-] tags from posts';

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
        $unwantedTags = [
            '[youtube-]',
            '[YOUTUBE-]',
            '[YouTube-]',
            '[-youtube]',
            '[-YOUTUBE]',
            '[-YouTube]'
        ];

        $replacementCount = 0;
        foreach ($unwantedTags as $unwantedTag) {
            $affected = DB::update(
                'UPDATE posts SET text = REPLACE(text, :tag, "") WHERE text LIKE :search;',
                [
                    'tag' => $unwantedTag,
                    'search' => "%$unwantedTag%",
                ]
            );
            $replacementCount = $replacementCount + $affected;
        }

        $this->info("Removed $replacementCount tags");
    }
}
