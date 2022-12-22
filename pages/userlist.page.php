<?php
// userlist.page.php
// Displays a list of all the users on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if (($q1 == "userlist") && ($config["userlistEnabled"] == false)) {
    message($lang["error.UserlistDisabled"]);
    require "pages/footer.php";
    exit;
}
if (($q1 == "userlist") && ($config["userlistEnabled"] == true) && (($config["userlistMembersOnly"] == true) && ($_SESSION["signed_in"] == false))) {
    message(sprintf($lang["error.UserlistMembersOnly"], genURL("signup"), genURL("login")));
    require "pages/footer.php";
    exit;
}

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
		echo '<h2>'.$lang["header.Userlist"].'</h2>';
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
			
			if ($row["deleted"] == "1") $deletedClass = " deleteduser";
            		else $deletedClass = "";
			
			if ($row["deleted"] == "1") {
                		$username = "deleted" . $row["userid"];
            		}
            		else {
                		$username = $row["username"];
            		}

			echo '<div class="userlist' . $deletedClass . '"><div class="userlist-top" postcolor="' . $row["color"] . '"><b><a href="' . genURL('user/' . $row["userid"]) . '/" id="' . $row["role"] . '">' . htmlspecialchars($username) . '</a></b>&nbsp; ' . $role . '&nbsp; <small></div><div class="userlist-bottom">' . parseAction($row["lastaction"], $lang) . ' (<a class="date" title="' . date('m-d-Y h:i:s A', $row["lastactive"]) . '">' . relativeTime($row["lastactive"]) . '</a>)</small></div></div>';
		}
	}
}

echo "</div>";

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.Userlist"]);
}

?>
