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

    public function get($topic_id = false, $startPost = 0) {

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

        /*

        options:

        * we are viewing the topic for the first time
            - show the latest $currentUser->user_display posts

                start_message is total messages - user_display
                posts to view is user_display

        * we are viewing the topic again
            - show all the posts since the user last viewed the topic
        */
        
        // user_display is stored as a string so we need to cast as an int here

        $postsToDisplay = (int)$currentUser->user_display;
        $startPost = (int)$startPost;
        // if startPost is 0 then see when we last viewed this topic and use that instead

        $topic->countNewPosts($currentUser->user_id);

        $posts = $topic->readTopic($startPost, $postsToDisplay, $currentUser->user_id);
    
        $templateData = array(
            'topic'         => $topic,
            'posts'         => $posts,
            'currentUser'   => $currentUser,
            );

        $html = $this->ui->renderTopic($templateData);

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
