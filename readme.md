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

This is the code that powers the _old-school style_ injoke of a BBS at [https://nexus5.org.uk](https://nexus5.org.uk). 

The web version of Nexus has run in one form or another since the ultra-futuristic year of _2001_. It was inspired by the [UCLAN](https://www.uclan.ac.uk) CompSoc BBS of the mid-90s where I happily misspent much of my university days.  

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

Nexus supports bootstrap themes. 
These can be externally hosted css files (such as the excellent ones at [Bootswatch](https://bootswatch.com/)) or built within nexus. Nexus ships with a Default theme and an example 'Excelsior' theme.

To enable the Excelsior theme
`php artisan theme:add Excelsior /css/excelsior.css`

To add external themes from Bootswatch...
```
php artisan theme:add darkly https://bootswatch.com/4/darkly/bootstrap.min.css
php artisan theme:add slate https://bootswatch.com/4/slate/bootstrap.min.css
php artisan theme:add united https://bootswatch.com/4/united/bootstrap.min.css
php artisan theme:add solar https://bootswatch.com/4/solar/bootstrap.min.css
php artisan theme:add sketchy https://bootswatch.com/4/sketchy/bootstrap.min.css
php artisan theme:add materia https://bootswatch.com/4/materia/bootstrap.min.css
php artisan theme:add minty https://bootswatch.com/4/minty/bootstrap.min.css
```  
Themes can be removed by 

```
php artisan theme:remove minty
```
Users who use a removed theme have their theme set to the default.

## Development 

A number of useful tasks are included to aid in development.

### Tests

PHP and javascript tests are provided. Coverage is nowhere near complete. Pull requests here are _extremely_ welcome.

##### PHP

PHP testing for unit and features are written using phpunit

`yarn phpunit` or with coverage map `yarn coverage`

The tests are found in _/tests_

#### Javascript

JS tests are written using mocha.

The tests are found in _/test/js/_

### Static Analysis

Static analysis is provided using the [larastan](https://medium.com/@nunomaduro/introducing-larastan-alpha-c7582ff366a6) package. To run an analysis of the code run:

`yarn larastan`

### Coding Standards

PHP is written to confirm to PSR2. To check the status of all the files within `app` run:

`yarn phpcs`

