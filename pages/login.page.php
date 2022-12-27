<?php
// login.page.php
// Logs the user in.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>' . $lang["login.Header"] . '</h2>';

if($_SESSION['signed_in'] == true)
{
	message(sprintf($lang["error.AlreadyLoggedIn"], genURL("logout")));
}

else
{
	if($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		echo '<form method="post" action=""><div class="formcontainer">
			<div class="forminput"><label>' . $lang["login.Username"] . '</label><input type="text" name="user_name" /></div>
			<div class="forminput"><label>' . $lang["login.Password"] . '</label><input type="password" name="user_pass"></div>
			<div class="forminput"><label></label><input type="submit" class="buttonbig" value="' . $lang["login.Submit"] . '" /></div>
		 </div></form>';
	}
	
	else
	{
		$errors = array();
		
		if(!isset($_POST['user_name']))
		{
			$errors[] = $lang["error.UsernameNull"];
		}
		
		if(!isset($_POST['user_pass']))
		{
			$errors[] = $lang["error.PasswordNull"];
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
		}
		else
		{	
			$username = $db->real_escape_string($_POST['user_name']);
            
            $resemail = $db->query("SELECT username FROM users WHERE email = '" . $username . "'");

            if(strpos($username, '@') !== "0")
            {
                while($rowe = $resemail->fetch_assoc()) {
				$username = $rowe["username"];
			    }
            }

            $res = $db->query("SELECT salt FROM users WHERE username = '" . $username . "'");

			if(!$res)
			{
				$errors[] = $lang["error.Database"];
			}
			
			if (!$res->num_rows)
			{
				$errors[] = $lang["error.UsernameWrong"];
			}
			
			while($row = $res->fetch_assoc()) {
				$salt = $row["salt"];
			}
				
			// Now check if the password is correct.
			$hash = $db->real_escape_string(hashstring($salt . $_POST["user_pass"]));
			

			$result = $db->query("SELECT userid, username, role FROM users WHERE username = '" . $username . "' AND password = '" . $hash . "'");
			
			
			if(!$result)
			{
				$errors[] = $lang["error.Database"];
			}
			
			else
			{
				if (!$result->num_rows)
				{
					$errors[] = $lang["error.PasswordWrong"];
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
					
					// Write the IP to the database.
					$db->query("UPDATE users SET ip='" . $db->real_escape_string(hashstring($_SERVER["REMOTE_ADDR"])) . "' WHERE userid='" . $_SESSION["userid"] . "'");
					
					message(sprintf($lang["login.Welcome"], $_SESSION['username'], genURL("")));
					header("Refresh:1; url=" . genURL(""));
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
			}
		}
	}
}

include 'footer.php';

?>
