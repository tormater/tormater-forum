<?php
// paneluser.page.php
// Displays a list of all the users on the forum for admins to administrate.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Start off by making a query for our list.

// Find out what page we're on.
if (isset($q3) && is_numeric($q3)) {
	$currentPage = $q3;
}
else
{
	// If no valid page is specified then always assume we're on the first page.
	$currentPage = 1;
}

$offset = (($currentPage * $config["postsPerPage"]) - $config["postsPerPage"]);

$result = $db->query("SELECT * FROM auditlog ORDER BY `time` DESC LIMIT " . $config["postsPerPage"] . " OFFSET " . $offset . "");

$pagec = $db->query("SELECT * FROM auditlog");

while($row = $pagec->fetch_assoc()) {
	$numPosts = $row['actionid'];
	$pages = ceil($numPosts / $config["postsPerPage"]);
}

if (!$result)
{
	message($lang["panel.UnknownError"]);
}

else
{
	if ($pagec->num_rows == 0)
	{
		message($lang["panel.NoLogsFound"]);
	}
	
	else
	{
        echo "<br/>";
        pagination("panel"); // so basically, this takes the arg, then adds $q2 to it, being "auditlog", so it links to /panel/auditlog/PAGE
        echo "<div class='audit-log'>";
        
		
		while($row = $result->fetch_assoc())
		{
            echo "<div class='auditlog-row'>";
            
            if ($row["action"] == "edit_post") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                while($urow = $user->fetch_assoc())
		        {
                    $name = $urow["username"];
                }
                printf($lang["panel.LogEdit"], 
                "<a href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($name) . "</a>", 
                $row["victimid"]);

                echo "<details><summary>" . $lang["panel.BeforeEdit"] . "</summary><div class='userpost'>" . formatPost($row["before"]) . "</div></details>";
                echo "<details><summary>" . $lang["panel.AfterEdit"] . "</summary><div class='userpost'>" . formatPost($row["after"]) . "</div></details>";
            }
            elseif ($row["action"] == "edit_role") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                while($urow = $user->fetch_assoc()) $name = $urow["username"];
                $user2 = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                while($urow = $user2->fetch_assoc()) $vname = $urow["username"];
                printf($lang["panel.LogEditRole"], 
                "<a href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($name) . "</a>", 
               "<a href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($vname) . "</a>",$row["before"],$row["after"]);
            }
            elseif ($row["action"] == "delete_avatar") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                while($urow = $user->fetch_assoc()) $name = $urow["username"];
                $user2 = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                while($urow = $user2->fetch_assoc()) $vname = $urow["username"];
                printf($lang["panel.LogDeleteAvatar"], 
                "<a href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($name) . "</a>", 
               "<a href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($vname) . "</a>");
            }
            elseif ($row["action"] == "hide_post") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                while($urow = $user->fetch_assoc())
		        {
                    $name = $urow["username"];
                }
                $toggled = "panel.Restored";
                if ($row["after"] == "hidden") {
                    $toggled = "panel.Hid";
                }
                printf($lang["panel.LogHide"], 
                "<a href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($name) . "</a>", 
                $lang[$toggled], $row["victimid"]);
            }
            elseif ($row["action"] == "delete_post") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                while($urow = $user->fetch_assoc())
		        {
                    $name = $urow["username"];
                }
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                while($urow = $user->fetch_assoc())
		        {
                    $name2 = $urow["username"];
                }
                printf($lang["panel.LogDelete"], 
                "<a href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($name) . "</a>",
                "<a href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($name2) . "</a>");
                echo "<details><summary>" . $lang["panel.PostContent"] . "</summary><div class='userpost'>" . formatPost($row["before"]) . "</div></details>";
            }
            elseif ($row["action"] == "delete_thread") {

                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                while($urow = $user->fetch_assoc())
		        {
                    $name = $urow["username"];
                }
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                while($urow = $user->fetch_assoc())
		        {
                    $name2 = $urow["username"];
                }
                printf($lang["panel.LogDeleteThread"],
                "<a href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($name) . "</a>", 
                "<a href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($name2) . "</a>",
                htmlspecialchars($row["before"]) );
            }
            echo relativeTime($row["time"]);
            echo "</div>";
		}
        	echo "</div>";
            pagination("panel");
	}
}

?>
