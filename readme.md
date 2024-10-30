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


![Build Status](https://github.com/christiancable/nexus5ive/workflows/Tests/badge.svg?branch=master)

## Install Steps

- `composer install`
- `cp .evn.example .env`
- Edit .env; add your datbase info and sensible values for the NEXUS\_\*
- `php artisan migrate`
- `yarn`
- `yarn run production`
- `php artisan nexus:install`

[View the Roadmap](https://github.com/christiancable/nexus5ive/projects/3)

## Themes

Nexus supports bootstrap themes.

These can be externally hosted css files (such as the excellent ones at [Bootswatch](https://bootswatch.com/)) or built within nexus. Nexus ships with a Default theme and an example 'Excelsior' theme.

To enable the Excelsior theme
`php artisan nexus:theme add --name=Excelsior --path='/css/excelsior.css'`

To add external themes from Bootswatch...

```
php artisan nexus:theme add --name=darkly  --path='https://bootswatch.com/4/darkly/bootstrap.min.css'
php artisan nexus:theme add --name=slate   --path='https://bootswatch.com/4/slate/bootstrap.min.css'
php artisan nexus:theme add --name=united  --path='https://bootswatch.com/4/united/bootstrap.min.css'
php artisan nexus:theme add --name=solar   --path='https://bootswatch.com/4/solar/bootstrap.min.css'
php artisan nexus:theme add --name=sketchy --path='https://bootswatch.com/4/sketchy/bootstrap.min.css'
php artisan nexus:theme add --name=materia --path='https://bootswatch.com/4/materia/bootstrap.min.css'
php artisan nexus:theme add --name=minty   --path='https://bootswatch.com/4/minty/bootstrap.min.css'
```

Themes can be removed by

```
php artisan nexus:theme remove --name=minty
```

Users who use a removed theme have their theme set to the default.

## Development

Nexus uses [Laravel Sail](https://laravel.com/docs/11.x/sail) for development.

A number of useful tasks are included to aid in development. Prefix the commands below with `./vendon/bin/sail` where required.

### Tests

PHP and javascript tests are provided. Coverage is nowhere near complete. Pull requests here are _extremely_ welcome.

- `sail artisan test` - unit and some feature tests
- `sail artisan dusk` - browser based tests

### Static Analysis

Static analysis is provided using the [larastan](https://github.com/larastan/larastan) package. To run an analysis of the code run:

`sail npm run larastan`

### Coding Standards

PHP is written to confirm to Laravel's coding standards. To check the status of all the files within `app` run:

`sail pint app`
