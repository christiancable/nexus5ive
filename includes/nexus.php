<?
namespace nexusfive;

/* 
facade for nexus - for getting information about the current state of the bbs
such as who is online etc
*/

use Exception;

class nexus 
{

    private $cfg;
    private $data;
    private $ui;

    function __construct($cfg=false, $data=false, $ui=false) {

        if ($cfg === false) {
            $globalConfig = new NxConfig();
            $this->cfg = $globalConfig->getConfig();
        } else {
            $this->cfg = $cfg;
        }

      if ($data === false) {
            $data = new NxConfig();
            $this->data = $datastore = new NxData($this->cfg);
        } else {
            $this->data = $data;
        }

        if ($ui === false) {
            $this->ui = new NxInterface($this->cfg);
        } else {
            $this->ui = $ui;
        }

    }

    public function getOnlineUsers($currentUserID, $include_self = false) {

        $onlineUsers = $this->data->readOnlineUsers($currentUserID, $include_self);

        return $onlineUsers;
    }
}