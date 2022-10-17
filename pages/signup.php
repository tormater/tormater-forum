<?php
//signup.php
// Allows users to create accounts.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>Sign up</h2>';

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    echo '<form method="post" action="">
 	 	<label>Username:</label><input type="text" name="user_name" /></br>
        <label></label><small>(3-24 characters, alphanumeric only)</small></br></br>
 	 	<label>Email:</label><input type="email" name="user_email"></br></br>
 		<label>Password:</label><input type="password" name="user_pass"></br>
 		<label></label><small>(minimum X characters)</small></br></br>
		<label>Confirm password:</label><input type="password" name="user_pass_check"></br></br>
 		<label></label><input type="submit" class="buttoninput" value="Register" />
 	 </form>';
}
else
{
	$errors = array();
	
	if(isset($_POST['user_name']))
	{
		if(!ctype_alnum($_POST['user_name']))
		{
			$errors[] = 'The username can only contain alphanumeric characters.';
		}
		if(strlen($_POST['user_name']) < 3)
		{
			$errors[] = 'The username must be at least 3 characters.';
		}
		if(strlen($_POST['user_name']) > 24)
		{
			$errors[] = 'The username cannot be longer than 24 characters.';
		}
	}
	else
	{
		$errors[] = 'The username field must not be empty.';
	}
	
	
	if(isset($_POST['user_pass']))
	{
		if($_POST['user_pass'] != $_POST['user_pass_check'])
		{
			$errors[] = 'Your passwords do not match.';
		}
	}
	else
	{
		$errors[] = 'The password field cannot be empty.';
	}
	
	if(!empty($errors))
	{
		echo 'Uh-oh.. a couple of fields are not filled in correctly..';
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
			echo 'Something went wrong while registering. Please try again later.';
		}
		else
		{
			echo 'Successfully registered. You can now <a href="login.php">log in</a> and start posting! :-)';
		}
	}
}

include 'footer.php';

?>
