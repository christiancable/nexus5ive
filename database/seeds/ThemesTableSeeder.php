<?php

use Illuminate\Database\Seeder;

class ThemesTableSeeder extends Seeder
{
    /**
     * Create the default theme
     *
     * @return void
     */
    public function run()
    {
        $defaultTheme = factory(App\Theme::class)->create();
        $defaultTheme->name = 'default'; 
        $defaultTheme->path = '/css/app.css'; 
        $defaultTheme->save(); 
    }
}
