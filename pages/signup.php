<?php
//signup.php
// Allows users to create accounts.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>' . $lang["register.Header"] . '</h2>';

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    echo '<div class="formcontainer"><form method="post" action="">
 	 	<div class="forminput"><label>' . $lang["register.Username"] . '</label><input type="text" name="user_name" /></div>
		<div class="forminput"><label></label><small class="fieldhint">' . $lang["register.UsernameDesc"] . '</small></div>
		<div class="forminput"><label>' . $lang["register.Email"] . '</label><input type="email" name="user_email"></div>
		<div class="forminput"><label>' . $lang["register.Password"] . '</label><input type="password" name="user_pass"></div>
		<div class="forminput"><label></label><small class="fieldhint">' . $lang["register.PasswordDesc"] . 'X' .$lang["register.PasswordDesc2"] . '</small></div>
		<div class="forminput"><label>' . $lang["register.PasswordConf"] . '</label><input type="password" name="user_pass_check"></div>
		<div class="forminput"><label></label><input type="submit" class="buttonbig" value="' . $lang["register.Submit"] . '" /></div>
 	 </form></div>';
}
else
{
	$errors = array();
	
	if(isset($_POST['user_name']))
	{
		if(!checkUsername($_POST['user_name']))
		{
			$errors[] = $lang["error.UsernameAlphNum"];
		}
		if(strlen($_POST['user_name']) < 3)
		{
			$errors[] = $lang["error.UsernameSmall"];
		}
		if(strlen($_POST['user_name']) > 24)
		{
			$errors[] = $lang["error.UsernameBig"];
		}
	}
	else
	{
		$errors[] = $lang["error.UsernameNull"];
	}
	
	
	if(isset($_POST['user_pass']))
	{
		if($_POST['user_pass'] != $_POST['user_pass_check'])
		{
			$errors[] = $lang["error.PassConfFail"];
		}
	}
	else
	{
		$errors[] = $lang["error.PassNull"];
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
	}
	else
	{
		// Construct the query.
		// First we need to generate our salt in order to salt the password.
		$salt = random_str(32);
		$username = $_POST['user_name'];
		$email = $_POST['user_email'];
		$password = md5($salt . $_POST['user_pass']);
		$role = "Member";
		$jointime = time();
        $lastactive = time();
		$color = '1';
		$ip = md5($_SERVER["REMOTE_ADDR"]);
		$verified = '1';
		
		$result = $db->query("INSERT INTO users (username, email, password, role, jointime, lastactive, color, ip, salt, verified) VALUES('" . $db->real_escape_string($username) . "', '" . $db->real_escape_string($email) . "', '" . $db->real_escape_string($password) . "', '$role', '$jointime', '$lastactive', '$color', '$ip', '$salt', '$verified')");
						
		if(!$result)
		{
			echo $lang["error.Database"];
		}
		else
		{
			echo $lang["register.Success"];
		}
	}
}

include 'footer.php';

?>
