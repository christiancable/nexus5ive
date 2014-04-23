<?php

namespace nexusfive\Test;

use nexusfive\NxData;
use nexusfive\NxConfig;

class NxDataTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateLastActiveTimeUpdatesLastActiveTime()
    {
        // TODO: make this test something when we have the ability to look at stuff - 22/04/2014
        $cfg = new NxConfig();
        $datastore = new NxData($cfg->getConfig());
 
        $datastore->updateLastActiveTime(666);

        $this->assertTrue(true);

    }
}
