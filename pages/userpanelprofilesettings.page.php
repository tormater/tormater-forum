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
else if (get_role_permissions() & PERM_EDIT_PROFILE) {
    if (isset($_POST["signature"]) and (strlen($_POST["signature"]) <= 512)) {
        $db->query("UPDATE users SET signature='" . $db->real_escape_string($_POST["signature"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
    }
    elseif (isset($_POST["bio"]) and (strlen($_POST["bio"]) <= 2048)) {
        $db->query("UPDATE users SET bio='" . $db->real_escape_string($_POST["bio"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
    }
}

$data = array(
    "header" => $lang["settings.PostColor"],
    "colors" => "",
    "bio_form" => "",
);

$result = $db->query("SELECT color FROM users WHERE userid='" . $_SESSION["userid"] . "'");
$user = $result->fetch_assoc();

for ($i = 1; $i <= 16; $i++) {
	if ($user['color'] == $i) $usercolor = " userscolor";
	else $usercolor = "";
    $data["colors"] .= '<button class="settingscolor' . $usercolor . '" postcolor="' . $i . '" value="' . $i . '" name="newcolor"></button>';
}

$userCheck = $db->query("SELECT signature, bio FROM users WHERE userid='" . $_SESSION["userid"] . "'");

while ($row = $userCheck->fetch_assoc()) {
    if (array_key_exists("signature",$_POST) and strlen($_POST["signature"])) {
        $signature = $_POST["signature"];
    }
    else if (array_key_exists("signature",$row) and strlen($row["signature"])) {
        $signature = $row["signature"];
    }
    else $signature = "";
    
    if (array_key_exists("bio",$_POST) and strlen($_POST["bio"])) {
        $bio = $_POST["bio"];
    }
    else if (array_key_exists("bio",$row) and strlen($row["bio"])) {
        $bio = $row["bio"];
    }
    else $bio = "";
}

if (get_role_permissions() & PERM_EDIT_PROFILE) {
    $data["bio_form"] .= "<h3>" . $lang["userpanel.Signature"] . "</h3>";
    $data["bio_form"] .=  BBCodeButtons(1,false) . "<form method='post' action=''><textarea maxlength='512' name='signature' id='textbox1'>" . htmlspecialchars($signature) . "</textarea><input type='submit' value='" . $lang["userpanel.UpdateSignature"] . "'></form>";
    $data["bio_form"] .=  "</br><h3>" . $lang["userpanel.Bio"] . "</h3>";
    $data["bio_form"] .=  BBCodeButtons(2,false) . "<form method='post' action=''><textarea maxlength='2048' name='bio' id='textbox2'>" . htmlspecialchars($bio) . "</textarea><input type='submit' value='" . $lang["userpanel.UpdateBio"] . "'></form>";
}

echo $template->render("templates/userpanel/profilesettings.html", $data);

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.ProfileSettings"]);
}

?>
