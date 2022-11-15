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
		<div class="forminput"><label></label><small class="fieldhint">' . sprintf($lang["register.PasswordDesc"], X) . '</small></div>
		<div class="forminput"><label>' . $lang["register.PasswordConf"] . '</label><input type="password" name="user_pass_check"></div>
		<div class="forminput"><label></label><input type="submit" class="buttonbig" value="' . $lang["register.Submit"] . '" /></div>
 	 </form></div>';
}
else
{
	$errors = array();
	
	if(isset($_POST['user_name']))
	{
		$res = $db->query("SELECT salt FROM users WHERE username = '" . $_POST['user_name'] . "'");
		if($res->num_rows)
		{
			$errors[] = $lang["register.UsernameExists"];
		}
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

	if(isset($_POST['user_email']))
	{
		$res = $db->query("SELECT * FROM users WHERE email ='". $_POST['user_email'] ."'");
		if($res->num_rows)
		{
			$errors[] = $lang["register.EmailExists"];
		}
		if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL))
		{
			$errors[] = $lang["register.InvalidEmailFormat"];
		}
	}
	else
	{
		$errors[] = $lang["register.EmailEmpty"];
	}

	if(empty($_POST['user_pass']))
	{
		$errors[] = $lang["register.PasswordEmpty"]; // $lang["error.PassNull"]
	}
	elseif (empty($_POST['user_pass_check']))
	{
		$errors[] = $lang["register.ConfirmPasswordEmpty"]; // $lang["error.ConfPassNull"]
	}
	else
	{
		if($_POST['user_pass'] != $_POST['user_pass_check'])
		{
			$errors[] = $lang["register.ConfirmPasswordWrong"]; // $lang["error.PassConfFail"]
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
		echo '<a class="buttonbig" href="javascript:history.back()">' . $lang["error.GoBack"] . '</a>';
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
			printf($lang["register.Success"], genURL("login"));
		}
	}
}

include 'footer.php';

?>
