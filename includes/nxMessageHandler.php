<?
namespace nexusfive;

/* 

TODO

navigation 


*/

class nxMessageHandler 
{
    private $cfg;
    private $ui;
    private $nexus;

    function __construct($cfg=false, $ui=false, $nexus=false) {

        if ($cfg === false) {
            $globalConfig = new NxConfig();
            $this->cfg = $globalConfig->getConfig();
        } else {
            $this->cfg = $cfg;
        }

        if ($ui === false) {
            $this->ui = new NxInterface($this->cfg);
        } else {
            $this->ui = $ui;
        }

         if ($nexus === false) {
            $this->nexus = new nexus;
        } else {
            $this->ui = $nexus;
        }
    }

    public function get($user_id = false) {
       

        // if we have $user_id  we set that as the default message send choice
      
        /* is this session start stuff boiler plate we can hide in away? */

        // start session and check login status
        session_start();

        if (isset($_SESSION['current_id'])) {
            try {
                $currentUser = new user($_SESSION['current_id']);
            } catch (Exception $e) {
                echo 'Error ', $e->getMessage(), "\n";
            }
        } else {
                $this->ui->ejectUser();    
        }
        // user logged in 

        $currentUser->updateCurrentActivity('Messages');
        $messages = $currentUser->readInstantMessages();
        $users = $this->nexus->getOnlineUsers($currentUser->user_id, false);

        // this is used to tell the template which recipent to select by default
        $recipent = array (
            'user_id'   => $user_id
            );

        // populate some data for the interface
        $templateData = array(
            'messages'      => $messages,
            'recipient'     => $recipent,
            'currentUser'   => $currentUser,
            'users'         => $users,
            );

        $html = $this->ui->renderMessages($templateData);
        echo $html;
    }


    public function post() {

        // start session and check login status
        session_start();

        if (isset($_SESSION['current_id'])) {
            try {
                $currentUser = new user($_SESSION['current_id']);
            } catch (Exception $e) {
                echo 'Error ', $e->getMessage(), "\n";
            }
        } else {
                $this->ui->ejectUser();    
        }
        // user logged in 

        if(isset($_POST['delete']) && isset($_POST['MessChk'])) {
            // delete messages
            $currentUser->deleteMessages($_POST['MessChk']);
        } elseif (isset($_POST['send'])) {
            // send message
            if ($_POST['recepient'] === 'ALL') {
                // send to all users for sysops only
                if ($currentUser->user_sysop === 'y') {
                    $users = $this->nexus->getOnlineUsers($currentUser->user_id, false);
                    foreach ($users as $recepient) {
                        $currentUser->sendMessage($recepient['user_id'], $_POST['message']);
                    }
                }
            } else {
                // send to one user
                $currentUser->sendMessage($_POST['recepient'], $_POST['message']);
            }
        } else {
            // we either have bad post data or they have elected to not choose any messages to delete - do nothing
        }

        $this->ui->redirectToMessages();
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
