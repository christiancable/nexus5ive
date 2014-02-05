<?php
# this is for custom site defines

putenv("TZ=GB");

define('DEFAULT_TEMPLATE', 'crystal');
define('TEMPLATE_HOME', '../templates/');
define('SYSOP_NAME', "Fraggle");
define('SYSOP_MAIL', 'sysop@nexus5.org.uk');
define('SYSOP_ID', '1');
define('MYSQL_USER', 'nexus');
define('MYSQL_PASSWORD', 'mWZdQ9pZ');
#define('MYSQL_SERVER','localhost:/tmp/mysql.cable109');
define('MYSQL_SERVER', 'localhost');
define('MYSQL_DATABASE', 'nexus');
define('BBS_NAME', 'Nexus');
define('ERROR_MAIL', '1');
define('MAX_MSG_SIZE', '600000');
// min age for BBS signups
define('BBS_AGE', '16');
// do we require sysop to validate new accounts
define('AUTO_VALIDATE_USERS', false);
// site capped limit for search results
define('SEARCH_LIMIT', 100);
// max time in which a user has the ability to alter their post in seconds
define('MAX_EDIT_TIME', 300);
