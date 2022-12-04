<?php
// settings.page.php
// Allows the user to change their settings.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if (empty($_SESSION["signed_in"]))
{
	message($lang["settings.LoginFirst"]);
	include "footer.php";
	exit;
}

echo '<h2>'.$lang["header.Settings"].'</h2>';

if (isset($_POST["newcolor"]))
{
	$result = $db->query("UPDATE users SET color='" . $_POST["newcolor"] . "' WHERE userid='" . $_SESSION["userid"] . "'");
	if (!$result) {
		message($lang["settings.SetColorError"]);
	}
	else {
		message($lang["settings.SetColorSuccess"]);
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
			$errors[] = $lang["settings.NewUsernameEmpty"];
		}
		elseif (!isset($_POST["confirmpass"]))
		{
			$errors[] = $lang["settings.PasswordEmpty"];
		}
		elseif ($newusername == $_SESSION["username"])
		{
			$errors[] = $lang["settings.NewUsernameSame"];
		}
		elseif (strlen($newusername) > 24)
		{
			$errors[] = $lang["settings.NewUsernameBig"];
		}
		elseif (!checkUsername($newusername))
		{
			$errors[] = $lang["settings.NewUsernameNonAlph"];
		}
		elseif (!$result->num_rows)
		{
			$errors[] = $lang["error.PasswordWrong"];
		}
		else
		{
			$result = $db->query("UPDATE users SET username='" . $db->real_escape_string($_POST["newusername"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
			if (!$result) {
				message($lang["settings.NewUsernameError"]);
			}
			else {
				$_SESSION["username"] = $_POST["newusername"];
				message($lang["settings.NewUsernameChanged"]);
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
			$errors[] = $lang["settings.NewPasswordEmpty"];
		}
		elseif (empty($confirmnewpass))
		{
			$errors[] = $lang["settings.ConfirmNewPasswordEmpty"];
		}
		elseif (empty($_POST["oldpass"]))
		{
			$errors[] = $lang["settings.OldPasswordEmpty"];
		}
		elseif ($newpass != $confirmnewpass)
		{
			$errors[] = $lang["settings.ConfirmNewPasswordError"];
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
				message($lang["settings.ChangePasswordError"]);
			}
			else {
				message($lang["settings.ChangePasswordChanged"]);
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
		echo '<a class="buttonbig" href="javascript:history.back()">'.$lang["error.GoBack"].'</a>';
		include "footer.php";
        exit;
	}
}

// Load users color.
$result = $db->query("SELECT color FROM users WHERE userid='" . $_SESSION["userid"] . "'");
$user = $result->fetch_assoc();

// Display the post color setting.
echo '<h3>'.$lang["settings.PostColor"].'</h3><div class="formcontainer">
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
echo '<h3>'.$lang["settings.ChangePassword"].'</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>'.$lang["settings.CurrentPassword"].'</label><input type="password" name="oldpass"></div>
<div class="forminput"><label>'.$lang["settings.NewPassword"].'</label><input type="password" name="newpass"></div>
<div class="forminput"><label>'.$lang["settings.ConfirmNewPassword"].'</label><input type="password" name="confirmnewpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="'.$lang["settings.ChangePasswordBtn"].'"></div></form></div>';

// Display the change username form.
echo '<h3>'.$lang["settings.ChangeUsername"].'</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>'.$lang["settings.NewUsername"].'</label><input type="text" name="newusername"></div>
<div class="forminput"><label>'.$lang["settings.CurrentPassword"].'</label><input type="password" name="confirmpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="'.$lang["settings.ChangeUsernameBtn"].'"></div></form></div>';

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.Settings"]);
}

?>
