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
	echo("</br>" . $lang["panel.DeleteUserMessage"] . "</br></br><form action='' method='POST'><button name='deleteKeepPosts'>" . $lang["panel.DeleteKeepPosts"] . "</button></form></br><form action='' method='POST'><button name='deleteHidePosts'>" . $lang["panel.DeleteHidePosts"] . "</button></form></br><form action='' method='POST'><button name='deleteRemovePosts'>" . $lang["panel.DeleteRemovePosts"] . "</button></form>");
}

?>
