<?php

namespace App\Console\Commands;

use App\Theme;
use App\User;
use Illuminate\Console\Command;

class NexusTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:theme
            {function : "add" or "remove" theme}
            {--name=  : The name of the theme}
            {--path=  : the path or url of the css file for the theme, only needed for add}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add, remove and update themes';

    protected $defaultTheme;

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
        $theme = Theme::factory()->make([
            'path' => $this->option('path'),
            'name' => $this->option('name'),
        ]);
        $theme->save();
    }

    public function handleAdd()
    {
        if ($this->option('path') === null) {
            $this->error('Missing option --path');

            return;
        }

        $themeName = $this->option('name');
        $existingTheme = Theme::where('name', $themeName)->first();

        if ($existingTheme) {
            $this->error('This theme already exists');
            if ($this->confirm('do you want to replace the existing theme?')) {
                $existingTheme->path = $this->option('path');
                $existingTheme->save();
            }
        } else {
            $this->info('Adding new Theme: '.$this->option('name').' - '.$this->option('path'));

            if ($this->confirm('Do you wish to continue?')) {
                $this->addTheme();
            }
        }
    }

    private function setThemeUsersToDefault($theme)
    {
        $defaultTheme = Theme::firstOrFail();
        foreach ($theme->users as $user) {
            $this->info('Updating theme to default for: '.$user->username);
            $user->theme_id = $defaultTheme->id;
            $user->save();
        }
    }

    public function handleRemove()
    {
        if ($this->option('name') === null) {
            $this->error('Missing option --name');

            return;
        }

        $themeName = $this->option('name');
        $existingTheme = Theme::where('name', $themeName)->first();

        if (! $existingTheme) {
            $this->error('Theme does not exist');
        }

        if ($existingTheme['name'] == 'Default') {
            $this->error('You cannot remove the default theme');

            return;
        }

        $this->info('Removing Theme: '.$existingTheme['name'].' - '.$existingTheme['path']);

        // show how many users use this theme
        $themeUsersCount = User::where('theme_id', $existingTheme['id'])->select('id')->count();

        $this->info($existingTheme['name'].' is used by '.$themeUsersCount.' users');
        if ($this->confirm('Do you wish to continue?')) {
            // move existing users of these theme to the default
            $this->setThemeUsersToDefault($existingTheme);
            $existingTheme->delete();
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $function = $this->argument('function');

        if ($function === 'add') {
            $this->handleAdd();

            return;
        }

        if ($function === 'remove') {
            $this->handleRemove();

            return;
        }

        $this->error('Specify *add* or *remove*');
    }
}
