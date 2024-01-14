<?php
// homepage.page.php
// Initializes the home page.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>' . $lang["homepage.Title"] . '</h2>';

$result = $db->query("SELECT * FROM `categories` ORDER BY `order` ASC");

if (isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] != true) {
    if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) {
        $threads = $db->query("SELECT * FROM threads ORDER BY lastposttime DESC LIMIT 5");
    }
    else {
        $threads = $db->query("SELECT * FROM threads WHERE draft='0' OR (draft='1' AND startuser='" . $_SESSION["userid"] . "') ORDER BY lastposttime DESC LIMIT 5");
    }
}
else {
    $threads = $db->query("SELECT * FROM threads WHERE draft='0' ORDER BY lastposttime DESC LIMIT 5");
}

echo '<table><tr><th>' . $lang["homepage.Cats"] . '</th><th><center>' . $lang["homepage.CatThreads"] . '</center></th><th>' . $lang["category.LastPost"] . '</th></tr>';
while($row = $result->fetch_assoc()) {
	$numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "' ORDER BY lastposttime DESC");
	$number = $numthreads->num_rows;
	$trow = $numthreads->fetch_assoc();
	$title = htmlspecialchars($trow['title']);
	if ($trow["posts"] > 1) {
	    $title = sprintf($lang["category.ReplyTo"], $title);
        }
	
	echo '<tr><td class="leftpart"><h3><a href="' . genURL("category/" . $row["categoryid"]) . '">' . htmlspecialchars($row["categoryname"]) . '</a></h3>';
	echo '<div>' . formatPost($row["categorydescription"]) . '</div></td>';
	echo '<td class="tdthreads"><div><center>' . $number . '</center></div></td>';
	echo '<td class="tdlastpost"><a href="' . genURL('thread/' . $trow['threadid']) . '">' . $title . "</a>";
	$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $trow["lastpostuser"] . "'");
					
	while ($u = $uinfo->fetch_assoc())
	{
		if ($u["deleted"] == 1) {
        	$username = $lang["user.Deleted"] . $u["userid"];
        }
		else {
            $username = $u["username"];
        }
        echo "<div class='tdinfo'>";
		printf("<span>" .$lang["thread.Info"] . "</span>", $u["role"], genURL("user/" . htmlspecialchars($trow["lastpostuser"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $trow['lastposttime']), relativeTime($trow["lastposttime"]));
        echo "</div>";
	}
	echo "</td></tr>";
}
echo '</table>';
echo '<table><tr><th>' . $lang["homepage.Threads"] . '</th><th><center>' . $lang["category.Posts"] . '</center></th><th>' . $lang["category.LastPost"] . '</th></tr>';
					
while($row = $threads->fetch_assoc())
{				
	echo '<tr><td class="leftpart">';

	if ($row["locked"] == 1)
	{
		echo '<span class="locked">' . $lang["label.Locked"] . '</span>';
	}
	
	if ($row["sticky"] == 1)
	{
		echo '<span class="sticky">' . $lang["label.Sticky"] . '</span>';
	}

    if ($row["draft"] == 1)
    {
        echo '<span class="draft">' . $lang["label.Draft"] . '</span>';
    }

    if ($row["pinned"] == 1)
    {
        echo '<span class="pinned">' . $lang["label.Pinned"] . '</span>';
    }

    echo '<b><a href="' . genURL('thread/' . $row['threadid']) . '">' . htmlspecialchars($row['title']) . "</a></b>";	
					
	$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
					
	while ($u = $uinfo->fetch_assoc())
	{
		if ($u["deleted"] == 1) {
        	$username = $lang["user.Deleted"] . $u["userid"];
        }
		else {
            $username = $u["username"];
        }
        echo "<div class='tdinfo'>";
		printf("<span>" .$lang["thread.Info"] . "</span>", $u["role"], genURL("user/" . htmlspecialchars($row["startuser"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $row['starttime']), relativeTime($row["starttime"]));
        echo "</div>";
	}

	echo '</td><td class="tdposts"><center>' . $row['posts'] . '</center></td><td class="tdlastpost">';
    				
	$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $db->real_escape_string($row["lastpostuser"]) . "'");
					
	while ($u = $uinfo->fetch_assoc())
	{
		if ($u["deleted"] == 1) {
            		$username = $lang["user.Deleted"] . $u["userid"];
        	}
        	else {
            		$username = $u["username"];
        	}
		echo '<a href="' . genURL('user/' . $row['lastpostuser']) . '" id="' . $u["role"] . '">' . htmlspecialchars($username) . '</a>';
	}
					
	echo '<div class="tddate" title="' . date('m-d-Y h:i:s A', $row['lastposttime']) . '">' . relativeTime($row["lastposttime"]) . '</div></td></tr>';
}

echo "</table>";


// Include "latest threads"

include 'footer.php';

// If the viewing user is logged in, update their last action.
if (isset($_SESSION['signed_in']) && ($_SESSION['signed_in'] == true))
{
	update_last_action("action.Homepage");
}

?>
