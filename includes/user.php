<?php
namespace nexusfive;

/* I guess this should be a facade for user activities but I'm not clear what, if anything, is needed yet */

use Exception;

class user 
{
 
    public $user_id;
    public $user_name;
    public $user_email; 
    public $user_comment; 
    public $user_popname;
    public $user_password;
    public $user_realname;
    public $user_priv;
    public $user_display;
    public $user_backwards; 
    public $user_sysop; 
    public $user_location; 
    public $user_theme;
    public $user_totaledits;
    public $user_totalvisits;
    public $user_status; 
    public $user_banned;
    public $user_film; 
    public $user_band; 
    public $user_sex; 
    public $user_town; 
    public $user_age;
    public $user_ipaddress; 
    public $user_no_pictures; 
    public $mojo;
    public $user_hideemail; 

    private $cfg;
    private $data;
    private $ui;

    function __construct($userID, $cfg=false, $data=false, $ui=false) {

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

        if(!$userData = $this->data->readUserInfo($userID)) {
            throw new Exception('Invalid User');
        }

        foreach ($userData as $key => $value) {
            $this->$key = $value;
        }
    }

    public function deleteMessages($messages) {
         $this->data->deleteMessages($this->user_id, $messages);
    }

    public function readInstantMessages() {
        $messages = $this->data->readInstantMessages($this->user_id);

        return $messages;
    }

    public function sendMessage($recipient, $message) {

        $this->data->createMessage($recipient, $this->user_id, $message); 

    }

    public function updateCurrentActivity($location) {

        $this->data->updateUserLocation($this->user_id, $location);
        $this->data->setUserOnline($this->user_id);
    }

    
}