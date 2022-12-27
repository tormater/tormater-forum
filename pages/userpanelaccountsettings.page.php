<?php
// userpanelaccountsettings.page.php
// Allows the user to change some account-related settings.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

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
	$hash = hashstring($salt . $_POST["confirmpass"]);
	$result = $db->query("SELECT userid, username, role FROM users WHERE username = '" . $_SESSION['username'] . "' AND password = '" . $db->real_escape_string($hash) . "'");
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
		$hash = $db->real_escape_string(hashstring($salt . $_POST["oldpass"]));
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
			$salt = random_str(64);
			$password = hashstring($salt . $_POST["newpass"]);
			$result = $db->query("UPDATE users SET password ='" . $db->real_escape_string($password) . "', salt = '" . $db->real_escape_string($salt) . "'  WHERE userid='" . $_SESSION["userid"] . "'");
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

// Display the change username form.
echo '</br><h3>'.$lang["settings.ChangeUsername"].'</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>'.$lang["settings.NewUsername"].'</label><input type="text" name="newusername"></div>
<div class="forminput"><label>'.$lang["settings.CurrentPassword"].'</label><input type="password" name="confirmpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="'.$lang["settings.ChangeUsernameBtn"].'"></div></form></div>';

// Display the change password form.
echo '<h3>'.$lang["settings.ChangePassword"].'</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>'.$lang["settings.CurrentPassword"].'</label><input type="password" name="oldpass"></div>
<div class="forminput"><label>'.$lang["settings.NewPassword"].'</label><input type="password" name="newpass"></div>
<div class="forminput"><label>'.$lang["settings.ConfirmNewPassword"].'</label><input type="password" name="confirmnewpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="'.$lang["settings.ChangePasswordBtn"].'"></div></form></div>';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.AccountSettings"]);
}

?>
