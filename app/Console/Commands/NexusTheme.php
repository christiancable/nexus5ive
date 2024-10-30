<?php

namespace App\Console\Commands;

use App\Models\Theme;
use App\Models\User;
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

    public $defaultTheme;

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

            return 1;
        }

        $themeName = $this->option('name');
        $existingTheme = Theme::where('name', $themeName)->first();

        if ($existingTheme) {
            $this->error('This theme already exists');

            if ($existingTheme->id == $this->defaultTheme->id) {
                $this->error('This theme already exists and cannot be replaced as it is the default');

                return 1;
            }

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

    public function setThemeUsersToDefault(Theme $theme)
    {
        foreach ($theme->users as $user) {
            $this->info('Updating theme to default for: '.$user->username);
            $user->theme_id = $this->defaultTheme->id;
            $user->save();
        }
    }

    public function handleRemove()
    {
        if ($this->option('name') === null) {
            $this->error('Missing option --name');

            return 1;
        }

        $themeName = $this->option('name');
        $existingTheme = Theme::where('name', $themeName)->first();

        if (! $existingTheme) {
            $this->error('Theme does not exist');

            return 1;
        }

        if ($existingTheme->id == $this->defaultTheme->id) {
            $this->error('You cannot remove the default theme');

            return 1;
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
        try {
            $this->defaultTheme = Theme::firstOrFail();
        } catch (\Throwable $th) {
            $this->error('Default theme missing. Try nexus:install to set a default theme first');

            return 1;
        }

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

        return 1;
    }
}
