# Nexus 5ive

                       ÛÛ
    ÛÛÛ   ÛÛ   ÛÛÛÛÛÛÛ   ÛÛ   ÛÛ   ÛÛ   ÛÛ   ÛÛÛÛÛÛÛ
    ÛÛÛÛ  ÛÛ°  ÛÛ°°°°°°   ÛÛ ÛÛ°°  ÛÛ°  ÛÛ°  ÛÛ°°°°°°       
    ÛÛ°ÛÛ ÛÛ°  ÛÛÛÛ        ÛÛÛ°°   ÛÛ°  ÛÛ°  ÛÛÛÛÛÛÛ   5     
    ÛÛ° ÛÛÛÛ°  ÛÛ°°°      ÛÛ°ÛÛ    ÛÛ°  ÛÛ°   °°°°ÛÛ°       
    ÛÛ°  ÛÛÛ°  ÛÛÛÛÛÛÛ   ÛÛ°° ÛÛ    ÛÛÛÛÛ°°  ÛÛÛÛÛÛÛ°       
     °°   °°°   °°°°°°°   °°   ÛÛ    °°°°°    °°°°°°°
                                °°


Written by [Christian Cable](http://christiancable.co.uk).

This is the code that powers the _old-school style_ injoke of a BBS at [http://nexus5.org.uk](http://nexus5.org.uk). 

The web version of Nexus has run in one form or another since the ultra-futuristic year of _2001_. It was inspired by the [UCLAN](http://www.uclan.ac.uk) CompSoc BBS of the mid-90s where I happily misspent much of my university days.  

This current version is built using the [Laravel framework](https://laravel.com) by Taylor Otwell 

## Build Status

[![Build Status](https://travis-ci.org/christiancable/nexus5ive.svg?branch=master)](https://travis-ci.org/christiancable/nexus5ive.svg?branch=master)

## Install Steps

* `composer install`
* `cp .evn.example .env`
* Edit .env; add your datbase info and sensible values for the NEXUS_* 
* `php artisan migrate`
* `yarn`
* `yarn run production`

[View the Roadmap](https://github.com/christiancable/nexus5ive/projects/2)

## Themes

Nexus comes with a number of different colour schemes. 
Install these with the command

`php artisan db:seed --class=ThemesTableSeeder`
