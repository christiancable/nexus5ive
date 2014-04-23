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

        $query = $this->db->prepare("SELECT nexusmessage_id, text, from_id, user_name FROM nexusmessagetable, usertable WHERE nexusmessagetable.user_id=:user_id AND usertable.user_id = from_id ORDER BY nexusmessage_id DESC");
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        $return = $results;

        return $return;
    }
}


/*

function get_instant_messages($user_id)
{
    // returns an array of instant message arrays
    $instant_message_array = array();

    $sql = "SELECT nexusmessage_id, text, from_id, user_name FROM nexusmessagetable, usertable
    WHERE nexusmessagetable.user_id=$user_id AND usertable.user_id = from_id
    ORDER BY nexusmessage_id DESC";

    if (!$sql_result = mysql_query($sql)) {
        return false;
    } else {
        if ($current_array = mysql_fetch_array($sql_result)) {
            do {
                array_push($instant_message_array, $current_array);
            } while ($current_array = mysql_fetch_array($sql_result));

            return $instant_message_array;
        } else {
            return false;
        }
    }
}



<?php
// collection of user information to import into the database
$users = ...
 
// specify the query "template"
$query = $db->prepare("INSERT INTO users (first_name, last_name, email) VALUES (:fname, :lname, :email)");
 
// bind the placeholder names to specific script variables
$query->bindParam(":fname", $firstName);
$query->bindParam(":lname", $lastName);
$query->bindParam(":email", $email);
 
// assign values to the specific variables and execute the query
foreach ($users as $u) {
    $firstName = $u["first_name"];
    $lastName = $u["last_name"];
    $email = $u["email"];
    $que
*/
