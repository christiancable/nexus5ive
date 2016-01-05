# Nexus 5ive

Written by Christian Cable.

This is the code that powers the in-joke of a BBS at [http://nexus5.org.uk](http://nexus5.org.uk)

It's built using the [Laravel framework](https://laravel.com) by Taylor Otwell 

It is **nowhere near complete**.

## Install Steps

* `composer install`
* `cp .evn.example .env`
* Edit .env; add your datbase info and sensible values for the NEXUS_* 
* `php artisan migrate`
* `php artisan nexus:install`

[View the Roadmap](https://trello.com/b/yyIvw9fp/nexus)