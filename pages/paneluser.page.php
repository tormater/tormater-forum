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
        echo "<div class='userlist-grid'>";
        
		
		while($row = $result->fetch_assoc())
		{
            $role = $lang["role." . $row["role"]];
			
			if ($row["deleted"] == "1") $deletedClass = " deleteduser";
            		else $deletedClass = "";
			
			if ($row["deleted"] == "1") {
                		$username = $lang["user.Deleted"] . $row["userid"];
            		}
            		else {
                		$username = $row["username"];
            		}

			echo '<div class="userlist' . $deletedClass . '"><div class="userlist-top" postcolor="' . $row["color"] . '"><b><a href="' . genURL('user/' . $row["userid"]) . '/" id="' . $row["role"] . '">' . htmlspecialchars($username) . '</a></b>&nbsp; ' . $role . '&nbsp; <small>';
            		if ($config["mainAdmin"] != $row["userid"]) {
                		echo '<a href="' . genURL("panel/useradmin/" . $row["userid"]) . '">' . $lang["panel.Administrate"] . '</a>&nbsp; ';
            		}
            		echo '</div><div class="userlist-bottom">' . parseAction($row["lastaction"], $lang) . ' (<a class="date" title="' . date('m-d-Y h:i:s A', $row["lastactive"]) . '">' . relativeTime($row["lastactive"]) . '</a>)</small></div></div>';
		}
        	echo "</div>";
	}
}

?>
