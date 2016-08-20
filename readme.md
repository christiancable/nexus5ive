# Nexus 5ive

Written by [Christian Cable](http://christiancable.co.uk).

This is the code that powers the _old-school style_ injoke of a BBS at [http://nexus5.org.uk](http://nexus5.org.uk). 

The web version of Nexus has run in one form or another since the ultra-futuristic year of 2001. It was inspired by the UCLAN CompSoc BBS of the early 90s.  

This current version is built using the [Laravel framework](https://laravel.com) by Taylor Otwell 


## Install Steps

* `composer install`
* `cp .evn.example .env`
* Edit .env; add your datbase info and sensible values for the NEXUS_* 
* `php artisan migrate`

[View the Roadmap](https://trello.com/b/yyIvw9fp/nexus)
