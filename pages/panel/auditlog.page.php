<?php
// paneluser.page.php
// Displays a list of all the users on the forum for admins to administrate.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$currentPage = 1;
if (isset($q3) && is_numeric($q3)) {
    $currentPage = $q3;
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
                $urow = $user->fetch_assoc();
                printf($lang["panel.LogEdit"], 
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>", 
                $row["victimid"]);

                echo "<details><summary>" . $lang["panel.BeforeEdit"] . "</summary><div class='userpost'>" . formatPost($row["before"]) . "</div></details>";
                echo "<details><summary>" . $lang["panel.AfterEdit"] . "</summary><div class='userpost'>" . formatPost($row["after"]) . "</div></details>";
            }
            elseif ($row["action"] == "edit_role") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                $urow = $user->fetch_assoc();
                $user2 = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                $urow2 = $user2->fetch_assoc();
                printf($lang["panel.LogEditRole"], 
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>", 
               "<a class='".$urow2["role"]."' href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($urow2["username"]) . "</a>",$row["before"],$row["after"]);
            }
            elseif ($row["action"] == "delete_avatar") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                $urow = $user->fetch_assoc();
                $user2 = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                $urow2 = $user2->fetch_assoc();
                printf($lang["panel.LogDeleteAvatar"], 
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>", 
               "<a class='".$urow2["role"]."' href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($urow2["username"]) . "</a>");
            }
            elseif ($row["action"] == "hide_post") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                $urow = $user->fetch_assoc();
                $toggled = "panel.Restored";
                if ($row["after"] == "hidden") $toggled = "panel.Hid";
                printf($lang["panel.LogHide"], 
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>", 
                $lang[$toggled], $row["victimid"]);
            }
            elseif ($row["action"] == "delete_post") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                $urow = $user->fetch_assoc();
                $user2 = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                $urow2 = $user2->fetch_assoc();
                printf($lang["panel.LogDelete"], 
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>",
                "<a class='".$urow2["role"]."' href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($urow2["username"]) . "</a>");
                echo "<details><summary>" . $lang["panel.PostContent"] . "</summary><div class='userpost'>" . formatPost($row["before"]) . "</div></details>";
            }
            elseif ($row["action"] == "delete_thread") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                $urow = $user->fetch_assoc();
                $user2 = $db->query("SELECT * FROM users WHERE userid='" . $row["victimid"] . "'");
                $urow2 = $user2->fetch_assoc();
                printf($lang["panel.LogDeleteThread"],
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>", 
                "<a class='".$urow2["role"]."' href='" . genURL("user/" . $row["victimid"]) . "'>" . htmlspecialchars($urow2["username"]) . "</a>",
                htmlspecialchars($row["before"]) );
            }
            else if ($row["action"] == "move_thread") {
                $user = $db->query("SELECT * FROM users WHERE userid='" . $row["userid"] . "'");
                $urow = $user->fetch_assoc();
                printf($lang["panel.LogMoveThread"], 
                "<a class='".$urow["role"]."' href='" . genURL("user/" . $row["userid"]) . "'>" . htmlspecialchars($urow["username"]) . "</a>", 
                htmlspecialchars($row["victimid"]),
                htmlspecialchars($row["before"]),
                htmlspecialchars($row["after"]));
            }
            echo relativeTime($row["time"]);
            echo "</div>";
        }
        echo "</div>";
        pagination("panel");
    }
}

?>
