<?php

namespace nexusfive;

use PDO;

class NxData
{

    public $db;

    public function __construct($cfg)
    {
        // if dbServer is false then we are using the tests and setting the db connection from the test suite
        if ($cfg['dbServer'] !== false) {
            try {
                $this->connect($cfg['dbServer'], $cfg['dbDatabase'], $cfg['dbUser'], $cfg['dbPassword']);
            } catch (PDOException $exception) {
                echo "error".$exception->getMessage();
                die(); // TODO - is this what we want to do??
            }
        }
    }


    private function connect($server, $database, $user, $password)
    {
        // returns a connection to the database       
        $db = new PDO("mysql:host=".$server.";dbname=".$database, $user, $password);
        $this->db = $db;
    }

     /**
     * Set the database connection (used for testing)
     *
     * @param PDO $connection
     */
    public function setConnection(PDO $connection)
    {
        $this->db = $connection;
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
    * @param $topic_id the topic
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

    /**
    * counts the number of posts in a topic
    * @param $topic_id the topic
    * @return int|false the number of posts within the topic or false on failure
    */

    public function countPostsInTopic($topic_id)
    {
        $query = "SELECT count(message_id) as postCount FROM messagetable WHERE topic_id=:topic_id";

        $parameters['int'] = array(
                'topic_id' => $topic_id,
            );

        $results = $this->getData($query, $parameters);

        if ($results) {
            $return = (int)$results[0]['postCount'];
        } else {
            $return = false;
        }

        return $return;
    }


    /**
    * returns posts that match the search query
    * @param $search_query a text query
    * @return array the posts 
    */

    public function searchPosts($search_query)
    {
        $query = "SELECT * FROM messagetable WHERE message_text LIKE '%:search_query%'";

        $parameters['string'] = array(
            'search_query' => $search_query
            );

        $results = $this->getData($query, $parameters);

        return $results;
    }

    /**
    * counts the number of messages in a given topic since the user last read it
    * @param $topic_id the topic ID
    * @param $user_id the user ID
    * @return int the number of posts since the user last read the topic or 0 if the topic was never read before
    *
    */
    public function countNewPostsInTopic($topic_id, $user_id)
    {
        // get the last message time
        $query = "SELECT msg_date FROM topicview WHERE user_id=:user_id AND topic_id=:topic_id";

        $parameters['int'] = array(
                'topic_id' => $topic_id,
                'user_id'  => $user_id
            );

        $results = $this->getData($query, $parameters);

        if ($results) {
            $lastReadDate = $results[0]['msg_date'];
        } else {
            $lastReadDate = 0;
        }

        if ($lastReadDate) {
            // count how many messages since the last time read
            $query = "SELECT COUNT(messagetable.message_id) AS newMessages FROM messagetable WHERE topic_id=:topic_id AND message_time > :message_time";

            $parameters['int'] = array(
                'topic_id' => $topic_id,
            );
            $parameters['string'] = array(
                'message_time' => $lastReadDate,
            );

            $results = $this->getData($query, $parameters);

            if ($results) {
                $return = (int)$results[0]['newMessages'];
            } else {
                $return = 0;
            }
        } else {
            $return = 0;
        }
    }

    /**
    * updates the latest message a user has read in a particular topic
    * @param $topic_id the topic
    * @param $user_id the reader
    * @return the PDO success
    */
    public function updateTopicLatestReadTime($topic_id, $user_id)
    {
        // remove any existing record for this user and topic
        $query = "DELETE FROM topicview WHERE topic_id=:topic_id AND user_id=:user_id";
        $parameters['int'] = array(
                'topic_id' => $topic_id,
                'user_id'  => $user_id
            );
        $results = $this->getData($query, $parameters);

        // get the time of the latest post
        $query = "SELECT max(message_time) as latest_date FROM messagetable WHERE topic_id=:topic_id";
        $parameters['int'] = array(
            'topic_id' => $topic_id,
        );

        $results = $this->getData($query, $parameters);

        if ($results) {
            $latestPostDate = $results[0]['latest_date'];
        } else {
            $latestPostDate = false;
        }

        // record the date of the most recent post
        if ($latestPostDate !== false) {
            $query = "INSERT INTO topicview (user_id, topic_id, msg_date) VALUES (:user_id, :topic_id, :message_time)";

            $parameters['int'] = array(
                'topic_id' => $topic_id,
                'user_id' => $user_id,
            );

            $parameters['string'] = array(
                'message_time' => $latestPostDate,

            );

            $results = $this->getData($query, $parameters);
        }

        return $results;
    }
}

/*


*/
