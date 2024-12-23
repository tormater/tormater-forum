<?php
// userpanelprofilesettings.page.php
// Allows the user to change their profile settings.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if (isset($_POST["newcolor"]) and is_numeric($_POST["newcolor"]) and ($_POST["newcolor"] > 0) and ($_POST["newcolor"] <= 16)) {
	$result = $db->query("UPDATE users SET color='" . $db->real_escape_string($_POST["newcolor"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
	if (!$result) {
		message($lang["settings.SetColorError"]);
	}
	else {
		message($lang["settings.SetColorSuccess"]);
		header("Cache-Control:private");
	}
}
elseif (isset($_POST["signature"]) and (strlen($_POST["signature"]) <= 512)) {
    $db->query("UPDATE users SET signature='" . $db->real_escape_string($_POST["signature"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
}
elseif (isset($_POST["bio"]) and (strlen($_POST["bio"]) <= 2048)) {
    $db->query("UPDATE users SET bio='" . $db->real_escape_string($_POST["bio"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
}

// Load users color.
$result = $db->query("SELECT color FROM users WHERE userid='" . $_SESSION["userid"] . "'");
$user = $result->fetch_assoc();

// Display the post color setting.
echo '</br><h3>'.$lang["settings.PostColor"].'</h3><div class="formcontainer">
<form method="post" action="">';

for ($i =1; $i <= 16; $i++) {
echo '<button class="settingscolor ';
// Display users color.
	if($user['color'] == $i) {
		echo ' userscolor';
	}
echo '" postcolor="' . $i . '" value="' . $i . '" name="newcolor"></button>';
}

echo '</form></div>';

$userCheck = $db->query("SELECT signature, bio FROM users WHERE userid='" . $_SESSION["userid"] . "'");

while ($row = $userCheck->fetch_assoc()) {
    if ((!$row["signature"]) or (!isset($row["signature"])) or ($row["signature"] == "")) {
        $signature = $_POST["signature"];
    }
    else {
        $signature = $row["signature"];
    }

    if ((!$row["bio"]) or (!isset($row["bio"])) or ($row["bio"] == "")) {
        $bio = $_POST["bio"];
    }
    else {
        $bio = $row["bio"];
    }
}

// Show the signature and bio inputs.
echo "<h3>" . $lang["userpanel.Signature"] . "</h3>";
echo BBCodeButtons(1) . "<form method='post' action=''><textarea maxlength='512' name='signature' id='textbox1'>" . htmlspecialchars($signature) . "</textarea><input type='submit' value='" . $lang["userpanel.UpdateSignature"] . "'></form>";
echo "</br><h3>" . $lang["userpanel.Bio"] . "</h3>";
echo BBCodeButtons(2) . "<form method='post' action=''><textarea maxlength='2048' name='bio' id='textbox2'>" . htmlspecialchars($bio) . "</textarea><input type='submit' value='" . $lang["userpanel.UpdateBio"] . "'></form>";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.ProfileSettings"]);
}

?>
