<?
namespace nexusfive;

class nxTopicHandler 
{
    private $cfg;
    private $ui;
    private $data;
    private $nexus;

    function __construct($cfg=false, $ui=false, $nexus=false, $data=false) {

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

    public function get($topic_id = false) {

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

        try {
            $topic = new NxTopic($topic_id);   
        } catch (Exception $e) {
            echo 'No such topic ', $e->getMessage();
        }
        
        $currentUser->updateCurrentActivity('Reading: '. $topic->topic_title);

        // get messages in topic

        $posts = $topic->readTopic(0, 10000);

        $templateData = array(
            'topic'         => $topic,
            'posts'         => $posts,
            'currentUser'   => $currentUser,
            );

        $html = $this->ui->renderTopic($templateData);

        echo $html;

        // user logged in 


        /* 

        1: get topic infomation
        2: get topic posts

    

        $users = $this->nexus->getOnlineUsers($currentUser->user_id, true);


         // populate some data for the interface
        $templateData = array(
            'currentUser'   => $currentUser,
            'users'         => $users,
            );

        $html = $this->ui->renderUsersOnline($templateData);
        echo $html;
        */
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
