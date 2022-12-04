<?php
// paneluser.page.php
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
        echo "<div class='userlist-grid'>";
        
		
		while($row = $result->fetch_assoc())
		{
			if ($row["role"] == "Administrator")
			{
				$role = $lang["role.Admin"];
			}
			elseif ($row["role"] == "Moderator")
			{
				$role = $lang["role.Mod"];
			}
			elseif ($row["role"] == "Member")
			{
				$role = $lang["role.Member"];
			}
			elseif ($row["role"] == "Suspended")
			{
				$role = $lang["role.Suspend"];
			}
			else
			{
				$role = $lang["role.Member"];
			}

			echo '<div class="userlist"><div class="userlist-top" postcolor="' . $row["color"] . '"><b><a href="' . genURL('user/' . $row["userid"]) . '/" id="' . $row["role"] . '">' . htmlspecialchars($row["username"]) . '</a></b>&nbsp; ' . $role . '&nbsp; <small></div><div class="userlist-bottom">' . parseAction($row["lastaction"], $lang) . ' (<a class="date" title="' . date('m-d-Y h:i:s A', $row["lastactive"]) . '">' . relativeTime($row["lastactive"]) . '</a>)</small></div></div>';
		}
        echo "</div>";
	}
}

?>
