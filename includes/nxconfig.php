<?php

namespace nexusfive;

class NxConfig
{
    public $configuration = array(
        'DEFAULT_TEMPLATE'      => 'crystal',
        'TEMPLATE_HOME'         => '../templates/',
        'SYSOP_NAME'            => 'Fraggle',
        'SYSOP_MAIL'            => 'sysop@nexus5.org.uk',
        'SYSOP_ID'              => '1',
        'dbUser'                => 'root',
        'dbPassword'            => '',
        'dbServer'              => '127.0.0.1',
        'dbDatabase'            => 'nexus',
        'BBS_NAME'              => 'Nexus',
        'ERROR_MAIL'            =>'1',
        'MAX_MSG_SIZE'          => '600000',
        'BBS_AGE'               => '16',
        'AUTO_VALIDATE_USERS'   => false,
        'SEARCH_LIMIT'          => 100,
        'MAX_EDIT_TIME'         => 300
    );

    public function __construct()
    {
        putenv("TZ=GB");
    }

    public function getConfig()
    {
        return $this->configuration;
    }
}
