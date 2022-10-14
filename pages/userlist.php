<?php
// userlist.php
// Displays a list of all the users on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

// Start off by making a query for our list.
$result = $db->query("SELECT * FROM users ORDER BY userid ASC");

if (!$result)
{
	echo "Failed to fetch users.";
}

else
{
	if ($result->num_rows == 0)
	{
		echo "Sadly, there are currently no users on the forum.";
	}
	
	else
	{
		echo '<h2>Userlist</h2>';
		
		while($row = $result->fetch_assoc())
		{
			echo '<div class="userlist" postcolor="' . $row["color"] . '"><b><a href="/user/' . $row["userid"] . '/">' . htmlspecialchars($row["username"]) . '</a></b>&nbsp; ' . $row["role"] . '&nbsp; <small>' . $row["lastaction"] . ' (<a title="' . date('m-d-Y h:i:s A', $row["lastactive"]) . '">' . relativeTime($row["lastactive"]) . '</a>)</small></div></br>';
		}
	}
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("Viewing: Userlist");
}

?>
