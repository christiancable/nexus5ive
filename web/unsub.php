<?php
# leaps to the next section containg unread messages or to main menu

include('../includes/theme.php');
include('../includes/database.php');


$db = opendata();
htmlheader("Unsubscribing","section.php?section=".$section_id,1);
pagetitle("Unsubscribing ...");


//$sql = "DELETE FROM topicview WHERE topic_id=$topic_id AND user_id=$current_id";

unsubscribe_from_topic($topic_id, $_SESSION[current_id]);


mysql_query($sql);

htmlfooter();

?>






