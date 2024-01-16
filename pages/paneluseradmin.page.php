<?php
// paneldeleteuser.page.php
// Allow administrators to delete a user and give them various options.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["deleteKeepPosts"])) {
		deleteUser("deleteKeepPosts", $q3);
	}
	elseif (isset($_POST["deleteHidePosts"])) {
		deleteUser("deleteHidePosts", $q3);
	}
	elseif (isset($_POST["deleteRemovePosts"])) {
		deleteUser("deleteRemovePosts", $q3);
	}
}

else {

// Start off by making a query for our list.
$ip = $db->query("SELECT ip, username, deleted FROM users WHERE userid='" . $db->real_escape_string($q3) . "'");
while ($r = $ip->fetch_assoc()) {
    $deleted = $r["deleted"];
    $name = $r["username"];
    $result = $db->query("SELECT * FROM users WHERE ip='" . $r["ip"] . "' AND NOT userid='" . $db->real_escape_string($q3) . "'");
}

echo '<br/>';
echo '<h2>'. sprintf($lang["panel.sameIP"], $name) .'</h2>';

if($result->num_rows == 0)
{
	message($lang["panel.noSameIP"]);
}
else {
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
    if (($config["mainAdmin"] != $row["userid"])) {
        echo '<a href="' . genURL("panel/useradmin/" . $row["userid"]) . '">' . $lang["panel.Administrate"] . '</a>&nbsp; ';
    }
    echo '</div><div class="userlist-bottom">' . parseAction($row["lastaction"], $lang) . ' (<a class="date" title="' . date('m-d-Y h:i:s A', $row["lastactive"]) . '">' . relativeTime($row["lastactive"]) . '</a>)</small></div></div>';
}

echo "</div>";
}

echo '<br/>';
echo '<h2>'. $lang["panel.DangerZone"] .'</h2>';
if (($config["mainAdmin"] != $row["userid"]) and ($deleted != "1")) {
echo($lang["panel.DeleteUserMessage"] . "</br></br><form action='' method='POST'><button class='buttonbig' name='deleteKeepPosts'>" . $lang["panel.DeleteKeepPosts"] . "</button></form></br><form action='' method='POST'><button class='buttonbig' name='deleteHidePosts'>" . $lang["panel.DeleteHidePosts"] . "</button></form></br><form action='' method='POST'><button class='buttonbig' name='deleteRemovePosts'>" . $lang["panel.DeleteRemovePosts"] . "</button></form>");
}
elseif ($deleted == "1") {
    echo '<a class="buttonbig" href="' . genURL("panel/restoreuser/" . htmlspecialchars($q3)) . '">' . $lang["panel.RestoreUser"] . '</a>&nbsp; ';
}

}

?>

