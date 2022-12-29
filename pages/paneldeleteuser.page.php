<?php
// paneldeleteuser.page.php
// Allow administrators to delete a user and give them various options.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["deleteKeepPosts"])) {
        // Make sure the inputted userid looks valid.
        if (($q3) and (isset($q3)) and (is_numeric($q3)) and ($config["mainAdmin"] != $q3)) {
            $existCheck = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($q3) . "'");

            // Make sure there is actually a user under the specified userid.
            if(!$existCheck or $existCheck->num_rows < 1) {
                echo($lang["panel.UseridError"]);
            }
            else {
                // If everything checks out, mark the user as deleted.
                $db->query("UPDATE users SET deleted='1' WHERE userid='" . $db->real_escape_string($q3) . "'");

                echo("</br>" . $lang["panel.DeleteUserSuccess"]);
            }
        }
    }
    elseif (isset($_POST["deleteHidePosts"])) {
        // Make sure the inputted userid looks valid.
        if (($q3) and (isset($q3)) and (is_numeric($q3)) and ($config["mainAdmin"] != $q3)) {
            $existCheck = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($q3) . "'");

            // Make sure there is actually a user under the specified userid.
            if(!$existCheck or $existCheck->num_rows < 1) {
                echo($lang["panel.UseridError"]);
            }
            else {
                // If everything checks out, mark the user as deleted and hide all of their posts.
                $db->query("UPDATE users SET deleted='1' WHERE userid='" . $db->real_escape_string($q3) . "'");
                $db->query("UPDATE posts SET deletedby='" . $db->real_escape_string($_SESSION["userid"]) . "' WHERE user='" . $db->real_escape_string($q3) . "'");

                echo("</br>" . $lang["panel.DeleteUserSuccessHide"]);
            }
        }
    }
    elseif (isset($_POST["deleteRemovePosts"])) {
        // Make sure the inputted userid looks valid.
        if (($q3) and (isset($q3)) and (is_numeric($q3)) and ($config["mainAdmin"] != $q3)) {
            $existCheck = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($q3) . "'");

            // Make sure there is actually a user under the specified userid.
            if(!$existCheck or $existCheck->num_rows < 1) {
                echo($lang["panel.UseridError"]);
            }
            else {
                // If everything checks out, mark the user as deleted.
                $db->query("UPDATE users SET deleted='1' WHERE userid='" . $db->real_escape_string($q3) . "'");

                // Now we need to delete any threads the user might have started and the posts within those threads.
                // Get all the ids of the user's threads first.
                $threads = $db->query("SELECT threadid FROM threads WHERE startuser='" . $db->real_escape_string($q3) . "'");

                // Now delete the posts from each of the threads and the threads themselves if there are any.
                if ($threads->num_rows > 0) {
                    while ($row = $threads->fetch_assoc()) {
                        // Delete the thread's posts.
                        $db->query("DELETE FROM posts WHERE thread='" . $db->real_escape_string($row["threadid"]) . "'");

                        // Finally, delete the thread.
                        $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($row["threadid"]) . "'");
                    }
                }

                // Here comes the hard part. We need to delete all of the user's posts in other user's threads without casuing issues.
                // First, get the ids of all the threads the remaining posts are in.
                $posts = $db->query("SELECT postid, thread FROM posts WHERE user='" . $db->real_escape_string($q3) . "'");

                // Now loop through the posts and delete them, but fix the thread they're in each time.
                if ($posts->num_rows > 0) {
                    while ($row = $posts->fetch_assoc()) {
                        // First off, delete the post.
                        $db->query("DELETE FROM posts WHERE postid='" . $db->real_escape_string($row["postid"]) . "'");

                        // Now check if the post was the only post in the thread.
                        $lastPostCheck = $db->query("SELECT 1 FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "'");

                        // If so, delete the thread.
                        if ($lastPostCheck->num_rows < 1) {
                            $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($row["thread"]) . "'");
                        }
                        // If not, we'll need to make sure to fix the thread's data based on the remaining posts.
                        else {
				            $lastPost = $db->query("SELECT user, timestamp FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "' ORDER BY timestamp DESC LIMIT 1");
                            $postCount = $db->query("SELECT 1 FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "'");

                            while($l = $lastPost->fetch_assoc()) {
						        $db->query("UPDATE threads SET posts='" . $db->real_escape_string($postCount->num_rows) . "', lastpostuser='" . $l["user"] . "', lastposttime='" . $l["timestamp"] . "' WHERE threadid='" . $db->real_escape_string($row["thread"]) . "'");
					        }
                        }
                    }
                }

                echo("</br>" . $lang["panel.DeleteUserSuccess"]);
            }
        }
    }
}

else {
    echo("</br>" . $lang["panel.DeleteUserMessage"] . "</br></br><form action='' method='POST'><button name='deleteKeepPosts'>" . $lang["panel.DeleteKeepPosts"] . "</button></form></br><form action='' method='POST'><button name='deleteHidePosts'>" . $lang["panel.DeleteHidePosts"] . "</button></form></br><form action='' method='POST'><button name='deleteRemovePosts'>" . $lang["panel.DeleteRemovePosts"] . "</button></form>");
}

?>
