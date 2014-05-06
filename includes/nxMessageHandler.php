<?
namespace nexusfive;

class nxMessageHandler 
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

    public function get($user_id = false) {
       

        // if we have $user_id  we set that as the default message send choice
      
        /* is this session start stuff boiler plate we can hide in away? */

        session_start();

        // check login 
        if (isset($_SESSION['current_id'])) {
            $this->data->updateLastActiveTime($_SESSION['current_id']);
        } else {
            //eject_user();
        }

        // get info about the current user
        if (!$user = $this->data->readUserInfo($_SESSION['current_id'])) {
            // nexus_error();
        }

        $messages = $this->data->readInstantMessages($user['user_id']);

        $users = $this->data->readOnlineUsers($user['user_id'], false);
      

        // this is used to tell the template which recipent to select by default
        $recipent = array (
            'user_id'   => $user_id
            );

        $templateData = array(
            'messages'      => $messages,
            'recipient'     => $recipent,
            'currentUser'   => $user,
            'users'         => $users,
            );

        $html = $this->ui->renderMessages($templateData);
        echo $html;
    }


    public function post() {

        // send message to user
         session_start();

        // check login 
        if (isset($_SESSION['current_id'])) {
            $user = $this->data->readUserInfo($_SESSION['current_id']);
        } else {
            //eject_user();
        }

        /* this is where we are dealing with user data, what should we check?

        recepient - do they exist?
        message - is the text okay?
        */

        $this->data->createMessage($userID = $_POST['recepient'], $fromID = $user['user_id'], $_POST['message']);
        
        // delete messages

        // return to messages screen

    }

    function get_xhr() {
        echo "get_xhr!";
        die();
    }
    function post_xhr() {
        echo "post_xhr!";
        die();
    }



}

/* 

/messages
- view, send and delete messages

    _GET

    /messages - view
    /messages/{id} - view with user_id selected as default send to 

    _POST 

    /messages - send messages
    /messages - delete selected messages


*/