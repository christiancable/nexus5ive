<?
namespace nexusfive;

/* 

TODO

navigation

*/

class nxUsersHandler 
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

    public function get() {

        // start session and check login status
        session_start();

        if (isset($_SESSION['current_id'])) {
            try {
                $currentUser = new user($_SESSION['current_id']);
            } catch (Exception $e) {
                echo 'Error ', $e->getMessage();
            }
        } else {
                $this->ui->ejectUser();    
        }
        // user logged in 

        $currentUser->updateCurrentActivity('Users on Nexus');
    

        $users = $this->nexus->getOnlineUsers($currentUser->user_id, true);


         // populate some data for the interface
        $templateData = array(
            'currentUser'   => $currentUser,
            'users'         => $users,
            );

        $html = $this->ui->renderUsersOnline($templateData);
        echo $html;
    }


    public function post() {

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
