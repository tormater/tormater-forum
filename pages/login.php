<?php
// login.php
// Logs the user in.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>Log in</h2>';

if($_SESSION['signed_in'] == true)
{
	echo 'You are already logged in, you can <a href="/logout/">log out</a> if you want.';
}

else
{
	if($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		echo '<form method="post" action="">
			<label>Username:</label><input type="text" name="user_name" /></br></br>
			<label>Password:</label><input type="password" name="user_pass"></br></br>
			<label></label><input type="submit" class="buttoninput" value="Log in" />
		 </form>';
	}
	
	else
	{
		$errors = array();
		
		if(!isset($_POST['user_name']))
		{
			$errors[] = 'The username field must not be empty.';
		}
		
		if(!isset($_POST['user_pass']))
		{
			$errors[] = 'The password field must not be empty.';
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
			$username = $db->real_escape_string($_POST['user_name']);
			
			$res = $db->query("SELECT salt FROM users WHERE username = '" . $username . "'");
			
			if(!$res)
			{
				echo "Something went wrong while logging in. Please try again later.";
			}
			
			if (!$res->num_rows)
			{
				echo "The specified user doesn't exist.";
			}
			
			while($row = $res->fetch_assoc()) {
				$salt = $row["salt"];
			}
				
			// Now check if the password is correct.
			$hash = $db->real_escape_string(md5($salt . $_POST["user_pass"]));
			
			$result = $db->query("SELECT userid, username, role FROM users WHERE username = '" . $username . "' AND password = '" . $hash . "'");
			
			
			if(!$result)
			{
				echo 'Something went wrong while logging in. Please try again later.';
			}
			
			else
			{
				if (!$result->num_rows)
				{
					echo "Wrong password.";
				}
				
				else
				{
					$_SESSION['signed_in'] = true;
					
					while($row = $result->fetch_assoc())
					{
						$_SESSION['userid'] = $row['userid'];
						$_SESSION['username'] = $row['username'];
						$_SESSION['role'] = $row['role'];
					}
					
					echo 'Welcome, ' . $_SESSION['username'] . '. <a href="/">Proceed to the forum overview</a>.';
				}
			}
		}
	}
}

include 'footer.php';

?>
