<?php

namespace App\Console\Commands;

use App\User;
use App\Theme;
use Illuminate\Console\Command;

class ThemeRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:remove
            {name : The name of the theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove themes';

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

    private function setThemeUsersToDefault($theme)
    {
        $defaultTheme = Theme::firstOrFail();
        foreach ($theme->users as $user) {
            $this->info('Updating theme to default for: ' . $user->username);
            $user->theme_id = $defaultTheme->id;
            $user->save();
        }
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
            
            if ($existingTheme['name'] == 'Default') {
                $this->error('You cannot remove the default theme');
                return;
            }

            $this->info('Removing Theme: ' . $existingTheme['name']);
            $this->info(' - css: ' . $existingTheme['path']);

            // show how many users use this theme
            $themeUsersCount = User::where('theme_id', $existingTheme['id'])->select('id')->count();
            $this->info($existingTheme['name'] . ' is used by ' . $themeUsersCount. ' users');
            if ($this->confirm('Do you wish to continue?')) {

                // move existing users of these theme to the default
                $this->setThemeUsersToDefault($existingTheme);
                $existingTheme->delete();
            }
        } else {
            $this->error('This theme does not exist');
        }
    }
}
