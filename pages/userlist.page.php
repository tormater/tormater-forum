<?php
// userlist.page.php
// Displays a list of all the users on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

// Handle requests to approve accounts.
if ((isset($_POST["approve"])) and (is_numeric($_POST["approve"])) and (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
    $check = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($_POST["approve"]) . "'");

    if ($check->num_rows > 0) {
        $db->query("UPDATE users SET verified='1' WHERE userid='" . $db->real_escape_string($_POST["approve"]) . "'");
    }
}

if ($config["userlistEnabled"] == false) {
    message($lang["error.UserlistDisabled"]);
    require "pages/footer.php";
    exit;
}
if (!(get_role_permissions() & PERM_VIEW_USERLIST)) {
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
            if ($row["deleted"] == "1" && $config["showDeletedInUserlist"] != 1)
            {
                continue;
            }
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
                $username = $lang["user.Deleted"] . $row["userid"];
            }
            else {
                $username = $row["username"];
            }

	    $verify = "";

            if (($row["verified"] == "0") and (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
                $verify = "<form method='post' action='' class='postc'><button name='approve' value='" . $row["userid"] . "'>" . $lang["userlist.Approve"] . "</button></form>";
            }

			displayUser($deletedClass, $row["color"], $row["userid"], $row["role"], $row["username"], $verify, $row["lastaction"], $row["lastactive"]);
		}
	}
}

echo "</div>";

include "footer.php";

// If the viewing user is logged in, update their last action.
if (isset($_SESSION['signed_in']) && ($_SESSION['signed_in'] == true))
{
	update_last_action($lang["action.Userlist"]);
}

?>
