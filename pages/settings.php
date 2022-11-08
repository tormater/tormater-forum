<?php
// settings.php
// Allows the user to change their settings.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if (empty($_SESSION["signed_in"]))
{
	message("You must be logged in to change user settings.");
	include "footer.php";
	exit;
}

echo '<h2>Settings</h2>';

if (isset($_POST["newcolor"]))
{
	$result = $db->query("UPDATE users SET color='" . $_POST["newcolor"] . "' WHERE userid='" . $_SESSION["userid"] . "'");
	if (!$result) {
		message("Sorry, couldn't set requested color.");
	}
	else {
		message("Successfully set post color.");
		header("Cache-Control:private");
	}
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$res = $db->query("SELECT salt FROM users WHERE username = '" . $_SESSION['username'] . "'");
	if(!$res)
	{
		$errors[] = $lang["error.Database"];
	}
	while($row = $res->fetch_assoc()) {
		$salt = $row["salt"];
	}
	$hash = $db->real_escape_string(md5($salt . $_POST["confirmpass"]));
	$result = $db->query("SELECT userid, username, role FROM users WHERE username = '" . $_SESSION['username'] . "' AND password = '" . $hash . "'");
	$errors = array();
	if (isset($_POST["newusername"]))
	{
		$newusername = $_POST["newusername"];
		if (!isset($newusername))
		{
			$errors[] = "New username cannot be empty.";
		}
		elseif (!isset($_POST["confirmpass"]))
		{
			$errors[] = "Password cannot be empty.";
		}
		elseif ($newusername == $_SESSION["username"])
		{
			$errors[] = "New username cannot be the same as old username.";
		}
		elseif (strlen($newusername) > 24)
		{
			$errors[] = "New username cannot be longer than 24 characters.";
		}
		elseif (!checkUsername($newusername))
		{
			$errors[] = "New username cannot contain non-alphanumeric characters.";
		}
		elseif (!$result->num_rows)
		{
			$errors[] = $lang["error.PasswordWrong"];
		}
		else
		{
			$result = $db->query("UPDATE users SET username='" . $db->real_escape_string($_POST["newusername"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
			if (!$result) {
				message("Unable to change username.");
			}
			else {
				$_SESSION["username"] = $_POST["newusername"];
				message("Username has been changed.");
				include "footer.php";
				refresh(1.5);
				exit;
			}
		}
	}
	
	if (isset($_POST["newpass"]))
	{
		$hash = $db->real_escape_string(md5($salt . $_POST["oldpass"]));
		$result = $db->query("SELECT userid, username, role FROM users WHERE username = '" . $_SESSION['username'] . "' AND password = '" . $hash . "'");
		$newpass = $_POST["newpass"];
		$confirmnewpass = $_POST["confirmnewpass"];
		if(empty($newpass))
		{
			$errors[] = "New password cannot be empty.";
		}
		elseif (empty($confirmnewpass))
		{
			$errors[] = "Confirm new password cannot be empty.";
		}
		elseif (empty($_POST["oldpass"]))
		{
			$errors[] = "Current password cannot be empty.";
		}
		elseif ($newpass != $confirmnewpass)
		{
			$errors[] = "Confirm new password is not the same.";
		}
		elseif (!$result->num_rows)
		{
			$errors[] = $lang["error.PasswordWrong"];
		}
		else
		{
			$salt = random_str(32);
			$password = md5($salt . $_POST["newpass"]);
			$result = $db->query("UPDATE users SET password ='" . $db->real_escape_string($password) . "', salt = '".$salt."'  WHERE userid='" . $_SESSION["userid"] . "'");
			if (!$result) {
				message("Unable to change password.");
			}
			else {
				message("Password has been changed.");
				include "footer.php";
				refresh(1.5);
				exit;
			}
		}
	}
	
	if(!empty($errors))
	{
		echo $lang["error.BadFields"];
		echo '<ul>';
		foreach($errors as $key => $value)
		{
		echo '<li>' . $value . '</li>';
		}
		echo '</ul>';
		echo '<a class="buttonbig" href="javascript:history.back()">Go Back</a>';
		include "footer.php";
        exit;
	}
}

// Load users color.
$result = $db->query("SELECT color FROM users WHERE userid='" . $_SESSION["userid"] . "'");
$user = $result->fetch_assoc();

// Display the post color setting.
echo '<h3>Post color</h3><div class="formcontainer">
<form method="post" action="">';

$i = 1;

while ($i <= 16) {
echo '<button class="settingscolor ';
// Display users color.
	if($user['color'] == $i) {
		echo ' userscolor';
	}
echo '" postcolor="' . $i . '" value="' . $i . '" name="newcolor"></button>';
$i++;
}

echo '</form></div>';

// Display the change password form.
echo '<h3>Change Password</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>Current password</label><input type="password" name="oldpass"></div>
<div class="forminput"><label>New Password</label><input type="password" name="newpass"></div>
<div class="forminput"><label>Confirm New Password</label><input type="password" name="confirmnewpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="Change Password"></div></form></div>';

// Display the change username form.
echo '<h3>Change Username</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>New Username</label><input type="text" name="newusername"></div>
<div class="forminput"><label>Current Password</label><input type="password" name="confirmpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="Change Username"></div></form></div>';

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("Viewing: Settings");
}

?>
