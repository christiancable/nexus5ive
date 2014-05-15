<?php

namespace nexusfive;

use PDO;

class NxData
{

    public $db;

    public function __construct($cfg)
    {
        try {
            $this->connect($cfg['dbServer'], $cfg['dbDatabase'], $cfg['dbUser'], $cfg['dbPassword']);
        } catch (PDOException $exception) {
            echo "error".$exception->getMessage();
            die(); // TODO - is this what we want to do??
        }
    }

    private function connect($server, $database, $user, $password)
    {
        // returns a connection to the database
        $db = new PDO("mysql:host=".$server.";dbname=".$database, $user, $password);
        
        $this->db = $db;
    }


    public function updateLastActiveTime($current_id)
    {

        // updates the whoison table with current time

        $query = $this->db->prepare("DELETE FROM whoison WHERE user_id=:current_id");
        $query->bindValue(':current_id', $current_id, PDO::PARAM_INT);
        $query->execute();

        $query = $this->db->prepare("INSERT INTO whoison (user_id) VALUES (:current_id)");
        $query->bindValue(':current_id', $current_id, PDO::PARAM_INT);
        $query->execute();

        // return $return;
    }


    public function readUserInfo($user_id)
    {
        /**
        * takes user_id and returns an array of their userinfo
        * INPUT user_id
        * OUTPUT assoc array of user_info or false if user is not found
        */

        $query = $this->db->prepare("SELECT * FROM usertable WHERE user_id=:user_id");
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        // we should only have one result
        if (count($results) === 1) {
            $user_array = $results[0];

            $return = $user_array;
        } else {
            $return = false;
        }
        
        return $return;

    }

    public function readSectionInfo($section_id)
    {
        /**
        * takes section_id and returns an array of section info
        * INPUT section_id
        * OUTPUT assoc array of section_info or false if section is not found
        */

        $query = $this->db->prepare("SELECT * FROM sectiontable WHERE section_id=:section_id");
        $query->bindValue(':section_id', $section_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        // we should only have one result
        if (count($results) === 1) {
            $section_array = $results[0];

            $return = $section_array;
        } else {
            $return = false;
        }
        
        return $return;

    }

    public function readInstantMessages($user_id)
    {
        /* returns an array of a user's instant messages
        or false if there's no messages
        */

        // fetchAll returns an array with results OR an empty array or false if there's na error

        $query = $this->db->prepare("SELECT nexusmessage_id, text, from_id, user_name, time, readstatus FROM nexusmessagetable, usertable WHERE nexusmessagetable.user_id=:user_id AND usertable.user_id = from_id ORDER BY nexusmessage_id DESC");
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $return = $results;

        return $return;
    }


    public function countInstantMessages($user_id, $include_read = false)
    {
       // $sql = "SELECT count(nexusmessage_id) AS total_msg FROM nexusmessagetable WHERE readstatus IS NULL AND user_id=$user_id";
        if ($include_read === false) {
            $query = $this->db->prepare("SELECT count(nexusmessage_id) AS total_msg FROM nexusmessagetable WHERE readstatus IS NULL AND user_id=:user_id");
        } else {
            $query = $this->db->prepare("SELECT count(nexusmessage_id) AS total_msg FROM nexusmessagetable WHERE user_id=:user_id");
        }
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchColumn();

        $return = $results;

        return $return;
        
    }

    public function setInstantMessagesRead($user_id)
    {
        // set any instant messgaes as read
        // return true or false

        $sql = "UPDATE nexusmessagetable SET readstatus='y' WHERE user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        return $query;
    }


    public function countComments($user_id, $include_read = false)
    {
        if ($include_read === false) {
            $query = $this->db->prepare("SELECT count(comment_id) FROM commenttable WHERE readstatus IS NULL AND user_id=:user_id");
        } else {
            $query = $this->db->prepare("SELECT count(comment_id) FROM commenttable WHERE AND user_id=:user_id");
        }
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchColumn();

        $return = $results;

        return $return;

    }

    public function updateUserLocation($user_id, $location)
    {

        // takes a user_id and location string
        // returns true or false

        $query = $this->db->prepare("UPDATE usertable SET user_location=:location WHERE user_id=:user_id");
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindValue(':location', $location, PDO::PARAM_STR);
        $query->execute();

        $rowsAffected = $query->rowCount();

        if ($rowsAffected !== 1) {
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }


    public function readOnlineUsers($user_id, $include_self = false)
    {
        
        /* checking for Offline here rather than Online because people can be instantly Offline and
        so should just vanish from the list right away */
        if ($include_self === true) {
            $sql = "SELECT whoison.user_id as user_id, 
                    usertable.user_popname as user_popname, 
                    usertable.user_location as user_location, 
                    user_name, 
                    whoison.timeon as last_active 
                    from whoison, usertable
                    WHERE (whoison.timeon > date_sub(now(), INTERVAL 5 minute)) and
                    whoison.user_id = usertable.user_id and
                    usertable.user_status <> 'Offline' ORDER BY timeon DESC";
        } else {
            $sql = "SELECT whoison.user_id as user_id, 
                    usertable.user_popname as user_popname, 
                    usertable.user_location as user_location, 
                    user_name, 
                    whoison.timeon as last_active 
                    from whoison, usertable
                    WHERE (whoison.timeon > date_sub(now(), INTERVAL 5 minute)) and
                    whoison.user_id = usertable.user_id and
                    whoison.user_id <> :user_id 
                    and usertable.user_status <> 'Offline' ORDER BY timeon DESC";
        }

        $query = $this->db->prepare($sql);

        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $return = $results;

        return $return;
    }

    public function createMessage($userID, $fromID, $message)
    {
        $sql = "INSERT INTO nexusmessagetable (user_id, from_id, text) values (:user_id, :from_id, :text)";
        $query = $this->db->prepare($sql);

        $query->bindValue(':user_id', $userID, PDO::PARAM_INT);
        $query->bindValue(':from_id', $fromID, PDO::PARAM_INT);
        $query->bindValue(':text', $message, PDO::PARAM_STR);

        $query->execute();

        $rowsAffected = $query->rowCount();

        if ($rowsAffected !== 1) {
            $return = false;
        } else {
            $return = true;
        }

        return $return;

    }

    public function deleteMessages($userID, $messageIDs)
    {
        /* accepts user and an array of message IDs and deletes them */

        /* returns
         success    - true
         fail       - false
        */

        $messages = array();
        $placeholders = array();

        foreach ($messageIDs as $messageID) {
            $placeholder = ":$messageID";
            $messages[] = array(
                'messageID'     =>  $messageID,
                'placeholder'   =>  $placeholder
                );
            $placeholders[] = $placeholder;

        }

        $placeholderSQL = implode(", ", $placeholders);

        $sql = "DELETE FROM nexusmessagetable WHERE nexusmessage_id IN (". $placeholderSQL . ") AND user_id=:user_id";

        $query = $this->db->prepare($sql);

        $query->bindValue(':user_id', $userID, PDO::PARAM_INT);
        foreach ($messages as $message) {
            $query->bindValue($message['placeholder'], $message['messageID'], PDO::PARAM_INT);
        }

        $query->execute();
        $rowsAffected = $query->rowCount();

        echo $rowsAffected;

        if ($rowsAffected !== count($messageIDs)) {
            $return = false;
        } else {
            $return = true;
        }

        return $return;

    }

    public function setUserOnline($userID)
    {
        /* remove any existing online record for this user and add a new one */

        $sql = "DELETE FROM whoison WHERE user_id=:user_id";
        $query = $this->db->prepare($sql);
        $query->bindValue(':user_id', $userID, PDO::PARAM_INT);
        $query->execute();


        $sql = "INSERT INTO whoison (user_id) VALUES (:user_id)";
        $query = $this->db->prepare($sql);
        $query->bindValue(':user_id', $userID, PDO::PARAM_INT);
        $query->execute();

        $rowsAffected = $query->rowCount();

        if ($rowsAffected !== 1) {
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }
}


/*

    if (isset($_SESSION['current_id'])) {

        $db = opendata();
        $current_id = $_SESSION['current_id'];

        $sql = "DELETE FROM whoison WHERE user_id=$current_id";
        mysql_query($sql, $db);
        $sql = "INSERT INTO whoison (user_id) VALUES ($current_id)";
        mysql_query($sql, $db);

        return true;
*/
