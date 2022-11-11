<?php
// paneluser.php
// Displays a list of all the users on the forum for admins to administrate.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Start off by making a query for our list.
$result = $db->query("SELECT * FROM users ORDER BY userid ASC");

if (!$result)
{
	echo $lang["error.FailedFetchUsers"];
}

else
{
	if ($result->num_rows == 0)
	{
		echo $lang["error.NoUsers"];
	}
	
	else
	{
		echo '<h2>'.$lang["panel.Users"].'</h2>';

        
		
		while($row = $result->fetch_assoc())
		{
			echo '<div class="userlist" postcolor="' . $row["color"] . '"><b><a href="/user/' . $row["userid"] . '/" id="' . $row["role"] . '">' . htmlspecialchars($row["username"]) . '</a></b>&nbsp; ' . $row["role"] . '&nbsp; <small>' . parseAction($row["lastaction"], $lang) . ' (<a class="date" title="' . date('m-d-Y h:i:s A', $row["lastactive"]) . '">' . relativeTime($row["lastactive"]) . '</a>)</small></div>';
		}
	}
}

?>
