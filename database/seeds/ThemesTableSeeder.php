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
        // default
        App\Theme::firstOrCreate([
            'name' => 'Default',
            'path' => '/css/app.css'
        ]);

        // spooky
        App\Theme::firstOrCreate([
            'name' =>'Halloween',
            'path' => '/css/spooky.css'
        ]);

        // excelsior
          App\Theme::firstOrCreate([
            'name' =>'Excelsior',
            'path' => '/css/excelsior.css'
        ]);
    }
}
