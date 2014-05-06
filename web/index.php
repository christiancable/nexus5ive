<?
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
    '/messages/:number' => 'nexusfive\nxMessageHandler',
    '/messages/' => 'nexusfive\nxMessageHandler'
    )
);
