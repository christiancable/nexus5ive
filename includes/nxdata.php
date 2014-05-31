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


    /**
     *  queries the database using the supplied sql and an array of parameters
     * 
     * @param string $query the query in SQL with placeholder
     * @param array $strings a named array of string parameters to match the placeholders in the SQL (optional)
     * @param array $numbers a named array of int parameters to match the placeholders in the SQL (optional)
     * @return assoc array|false the results of the query or false on error
     */
    public function getData($query, $parameters = null)
    {

        $statement = $this->db->prepare($query);

        if (isset($parameters['string'])) {
            foreach ($parameters['string'] as $string => $value) {
                $statement->bindValue(':'.$string, $value, PDO::PARAM_STR);
            }
        }

        if (isset($parameters['int'])) {
            foreach ($parameters['int'] as $number => $value) {
                $statement->bindValue(':'.$number, $value, PDO::PARAM_INT);
            }
        }

        if ($statement->execute()) {
            $return = $statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $return = false;
        }

        return $return;
    }



    /**
    *  updates who's online with latest time of activity
    * 
    * @param $user_id the current user's id
    * @return bool to show status of update 
    */
    public function updateLastActiveTime($user_id)
    {
        $successStatus = true;

        $query = "DELETE FROM whoison WHERE user_id=:user_id";
        $parameters['int'] = array(
            'user_id' => $user_id
            );
        $status = $this->getData($query, $parameters);
        if ($status === false) {
            $successStatus = false;
        }

        $query = "INSERT INTO whoison (user_id) VALUES (:user_id)";
        $status = $this->getData($query, $parameters);
        if ($status === false) {
            $successStatus = false;
        }

        return $successStatus;
    }


    /**
     *  looks up a user's information 
     * 
     * @param $user_id the user's id
     * @return array|false user's information or false if user not found
     */
    public function readUserInfo($user_id)
    {
        $query = "SELECT * FROM usertable WHERE user_id=:user_id";
        
        $parameters['int'] = array(
            'user_id' => $user_id
            );

        $results = $this->getData($query, $parameters);

        // we should only have one result
        if (count($results) === 1) {
            $user_array = $results[0];
            $return = $user_array;
        } else {
            $return = false;
        }
        
        return $return;
    }


    /**
    *  looks up information about a section
    * 
    * @param $section_id the section
    * @return array|false sections's information or false if section not found
    */
    public function readSectionInfo($section_id)
    {
        $query = "SELECT * FROM sectiontable WHERE section_id=:section_id";
        $parameters['int'] = array(
                'section_id' => $section_id,
            );

        $results = $this->getData($query, $parameters);

        // we should only have one result
        if (count($results) === 1) {
            $section_array = $results[0];
            $return = $section_array;
        } else {
            $return = false;
        }

        return $return;
    }


   /**
    *  gets a given user's instant messages
    * 
    * @param $user_id the user
    * @return array|false the users instant messages or false on failure
    */
    public function readInstantMessages($user_id)
    {
        $query = "SELECT nexusmessage_id, text, from_id, user_name, time, readstatus FROM nexusmessagetable, usertable WHERE nexusmessagetable.user_id=:user_id AND usertable.user_id = from_id ORDER BY nexusmessage_id DESC";
        $parameters['int'] = array(
                'user_id' => $user_id,
            );

        $results = $this->getData($query, $parameters);

        return $results;
    }


    /**
    *  counts the number of instant message a user has
    * 
    * @param int $user_id the user
    * @param bool $include_read should the count include previously read mesages
    * @return int|false the number of message or false on error
    */
    public function countInstantMessages($user_id, $include_read = false)
    {
        if ($include_read === false) {
            $query = "SELECT count(nexusmessage_id) AS total_msg FROM nexusmessagetable WHERE readstatus IS NULL AND user_id=:user_id";
        } else {
            $query = "SELECT count(nexusmessage_id) AS total_msg FROM nexusmessagetable WHERE user_id=:user_id";
        }

        $parameters['int'] = array(
                'user_id' => $user_id,
            );

        $results = $this->getData($query, $parameters);

        if ($results) {
            $return = $results[0]['total_msg'];
        } else {
            $return = false;
        }

        return $return;
    }


    /**
    *  sets a user's instant messages as read
    * 
    * @param int $user_id the user
    * @return int|false the number of message or false on error
    */
    public function setInstantMessagesRead($user_id)
    {
        $parameters['int'] = array(
                'user_id' => $user_id,
            );
        $query = "UPDATE nexusmessagetable SET readstatus='y' WHERE user_id = :user_id";
        $results = $this->getData($query, $parameters);

        return $results;
    }


   /**
    *  counts the number of comments on a users info screen
    * 
    * @param int $user_id the user
    * @param bool $include_read should the count include previously read mesages
    * @return int|false the number of message or false on error
    */
    public function countComments($user_id, $include_read = false)
    {
        
        if ($include_read === false) {
            $query = "SELECT count(comment_id) as total_msg FROM commenttable WHERE readstatus IS NULL AND user_id=:user_id";
        } else {
            $query = "SELECT count(comment_id) as total_msg FROM commenttable WHERE AND user_id=:user_id";
        }

        $parameters['int'] = array(
                'user_id' => $user_id,
            );

        $results = $this->getData($query, $parameters);

        if ($results) {
            $return = $results[0]['total_msg'];
        } else {
            $return = false;
        }

        return $return;
    }


    /**
    *  updates a user's bbs location 
    * 
    * @param int $user_id the user
    * @param string $location the location on the bbs
    * @return int|false the number of message or false on error
    */
    public function updateUserLocation($user_id, $location)
    {
        $query = "UPDATE usertable SET user_location=:location WHERE user_id=:user_id";

        $parameters['int'] = array (
            'user_id' => $user_id
            );

        $parameters['string'] = array(
            'location' => $location
            );

        $results = $this->getData($query, $parameters);

        return $results;
    }


    /**
    *  retrieves a list of users online
    * 
    * @param int $user_id the user
    * @param bool $include_self include user_id in the list or not
    * @return array|false information about the currently online users or false on failure
    */
    public function readOnlineUsers($user_id, $include_self = false)
    {

        /* checking for Offline here rather than Online because people can be instantly Offline and
        so should just vanish from the list right away */

        if ($include_self === true) {
            $query = "SELECT whoison.user_id as user_id, 
            usertable.user_popname as user_popname, 
            usertable.user_location as user_location, 
            user_name, 
            whoison.timeon as last_active 
            from whoison, usertable
            WHERE (whoison.timeon > date_sub(now(), INTERVAL 5 minute)) and
            whoison.user_id = usertable.user_id and
            usertable.user_status <> 'Offline' ORDER BY timeon DESC";
        } else {
            $query = "SELECT whoison.user_id as user_id, 
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

        $parameters['int'] = array (
            'user_id' => $user_id
            );

        $results = $this->getData($query, $parameters);

        return $results;
    }


   /**
    *  send an instant message
    * 
    * @param int $userID the user receiving the message
    * @param int $fromID the user sending the message
    * @param string $message the text of the message
    * @return bool true on success or false on error
    */
    public function createMessage($userID, $fromID, $message)
    {
        $query = "INSERT INTO nexusmessagetable (user_id, from_id, text) values (:user_id, :from_id, :text)";


        $parameters['int'] = array(
            'user_id' => $userID,
            'from_id' => $fromID
            );

        $parameters['string'] = array(
            'text' => $message
            );

        $results = $this->getData($query, $parameters);

        return $results;
    }

    /**
    *  send an instant message
    * 
    * @param int $userID the user who owns the messages
    * @param array $messageIDs the IDs of the messages to delete
    * @return bool true on success or false on error
    */
    public function deleteMessages($userID, $messageIDs)
    {
        $parameters['int']['user_id'] = $userID;

        foreach ($messageIDs as $messageID) {
            $parameters['int']["$messageID"] = $messageID;
            $placeholders[] = ":$messageID";
        }

        $placeholderSQL = implode(", ", $placeholders);
        $query = "DELETE FROM nexusmessagetable WHERE nexusmessage_id IN (". $placeholderSQL . ") AND user_id=:user_id";

        $results = $this->getData($query, $parameters);

        return $results;
    }



    /**
    *  looks up information about a topic
    * 
    * @param $topic_id the section
    * @return array|false the topics's information or false if section not found
    */
    public function readTopicInfo($topic_id)
    {
        $query = "SELECT * FROM topictable WHERE topic_id=:topic_id";
        $parameters['int'] = array(
                'topic_id' => $topic_id,
            );

        $results = $this->getData($query, $parameters);

        if (count($results)) {
            $return = $results[0];
        } else {
            $return = false;
        }
        
        return $return;
    }

    public function getPostsInTopic($topic_id, $startPost, $numberOfPosts)
    {
         $query = 'SELECT messagetable.*, usertable.user_name FROM messagetable, usertable WHERE topic_id=:topic_id AND usertable.user_id = messagetable.user_id ORDER BY  message_id  LIMIT :startPost, :numberOfPosts';

         $parameters['int'] = array(
            'topic_id' => $topic_id,
            'startPost' => $startPost,
            'numberOfPosts' => $numberOfPosts
            );


         $results = $this->getData($query, $parameters);
         return $results;

    }
}

/*

unction fetchPostArray($topicID, $numberOfPosts, $startPost, $userID)
{
    // SUCCCESS: returns an array of posts from a topic
    // FAILURE: returns false
    

    $returnValue = false;

    $totalPosts = get_count_topic_messages($topicID);
    $newPosts = new_messages_in_topic($topicID, $userID);


    $sql = 'SELECT message_id FROM messagetable WHERE topic_id=' . $topicID . '  ORDER BY  message_id  ';

    if ($startPost === false) {
        unset($startPost);
    }

    if (!isset($startPost)) {
        $startPost = $totalPosts - $numberOfPosts;

        if ($newPosts > $numberOfPosts) {
            $startPost = $totalPosts - $newPosts;
            $numberOfPosts = $newPosts + 1;
        }

    } else {
        if ($startPost > ($totalPosts - $numberOfPosts)) {
            $startPost = $totalPosts - $numberOfPosts;
        }
    }

    if ($startPost < 0) {
        $startPost = 0;
    }

    $limit_sql = "LIMIT $startPost, $numberOfPosts";

    $sql = $sql . $limit_sql;


    if (!$sqlResult = mysql_query($sql)) {

        $returnValue = false;

    } else {
            // gather the topic's posts into an array of posts
            
        $postArray = array();
        if (mysql_num_rows($sqlResult)) {
            if ($currentPost = mysql_fetch_array($sqlResult)) {
                do {
                    array_push($postArray, $currentPost);
                } while ($currentPost = mysql_fetch_array($sqlResult));
            }
        }

        $returnValue = $postArray;
    }

    return $returnValue;
}
*/
