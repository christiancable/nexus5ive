<?
namespace nexusfive;

/* 

provides a count of unread messages and that's it

*/

class nxMessageCountHandler 
{
    private $cfg;
    private $nexus;

    function __construct($cfg=false, $ui=false, $nexus=false) {

        if ($cfg === false) {
            $globalConfig = new NxConfig();
            $this->cfg = $globalConfig->getConfig();
        } else {
            $this->cfg = $cfg;
        }

         if ($nexus === false) {
            $this->nexus = new nexus;
        } else {
            $this->ui = $nexus;
        }
    }

    public function get($user_id = false) {
       
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

   

        $html = $currentUser->countInstantMessages();
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
