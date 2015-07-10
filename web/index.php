<?php
/*
url dispatch for nexusfive

- cfcable
*/

namespace nexusfive;

require '../vendor/autoload.php';

use Toro;
use ToroHook;

ToroHook::add("404", function() {
    echo "Not found";
});

// ToroHook::add("before_handler", function())

Toro::serve(
    array(
    '/messages/:number'          => 'nexusfive\nxMessageHandler',
    '/messages/'                 => 'nexusfive\nxMessageHandler',
    '/messages/count'            => 'nexusfive\nxMessageCountHandler',
    '/users/'                    => 'nexusfive\nxUsersHandler',
    '/topic/:number/:number'     => 'nexusfive\nxTopicHandler',
    '/topic/:number'             => 'nexusfive\nxTopicHandler',
    '/search/pictures/'          => 'nexusfive\nxSearchHandler'
    )
);


/*

who's online

/users

examine user

/user
/user/<user id>
/user/<user id>/edit


topics
------

/topic/<topic id>
/topic/<topic id>/<start message> - start at a given message
/topic/<topic id>/edit
/topic/<topic id>/delete

/topic/new/<parent id>


messages
--------

/messages/ - show user to user messages
/messages/count - show the number of messages only to use in ajax calls


sections
---------

/section/<section_id>
/section/<section_id>/edit
/section/<section_id/delete
/section/<section_id>/new - add a subsection
*/