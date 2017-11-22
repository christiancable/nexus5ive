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

        // spectrum
          App\Theme::firstOrCreate([
            'name' =>'8 Bit',
            'path' => '/css/8bit.css'
        ]);
    }
}
