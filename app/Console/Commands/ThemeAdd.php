<?php

namespace App\Console\Commands;

use App\Theme;
use Illuminate\Console\Command;

class ThemeAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:add
            {name : The name of the theme}
            {path : the path or url of the css file for the theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add and update themes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function addTheme()
    {
        $arguments = $this->arguments();
        $theme = factory(Theme::class)->make([
                    'path' => $arguments['path'],
                    'name'=> $arguments['name']
        ]);
        $theme->save();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $existingTheme = Theme::where('name', $arguments['name'])->first();

        if ($existingTheme) {
            $this->error('This theme already exists');
            if ($this->confirm('do you want to replace the existing theme?')) {
                $existingTheme->path = $arguments['path'];
                $existingTheme->save();
            }
        } else {
            $this->info('Adding new Theme: ' . $arguments['name']);
            $this->info(' - css: ' . $arguments['path']);

            if ($this->confirm('Do you wish to continue?')) {
                $this->addTheme();
            }

        }
    }
}
