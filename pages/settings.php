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

if (isset($q3) && is_numeric($q3) && ($q2 == "color"))
{
	$result = $db->query("UPDATE users SET color='" . $db->real_escape_string($q3) . "' WHERE userid='" . $_SESSION["userid"] . "'");
	
	if (!$result) {
		message("Sorry, couldn't set requested color.");
	}
	else {
		message("Successfully set post color.");
	}
}

echo '<h2>Settings</h2>';

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
	// Display the post color setting.
	echo "<fieldset><legend>Post color</legend><a href='/settings/color/1/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='lightblue' /></svg></a><a href='/settings/color/2/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='lightgreen' /></svg></a><a href='/settings/color/3/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='yellow' /></svg></a><a href='/settings/color/4/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='indianred' /></svg></a><a href='/settings/color/5/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='hotpink' /></svg></a><a href='/settings/color/6/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='mediumpurple' /></svg></a><a href='/settings/color/7/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='coral' /></svg></a><a href='/settings/color/8/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='cornflowerblue' /></svg></a><a href='/settings/color/9/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='lavender' /></svg></a><a href='/settings/color/10/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='silver' /></svg></a><a href='/settings/color/11/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='wheat' /></svg></a><a href='/settings/color/12/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='sandybrown' /></svg></a><a href='/settings/color/13/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='mediumseagreen' /></svg></a><a href='/settings/color/14/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='gold' /></svg></a><a href='/settings/color/15/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='plum' /></svg></a><a href='/settings/color/16/'><svg width='100' height='100'><circle cx='50' cy='50' r='40' fill='aliceblue' /></svg></a></fieldset>";
	// Display the change username form.
	echo '<fieldset><legend>Change username</legend><form method="post" action="">New username: <input type="text" name="newusername"></p>Current password: <input type="password" name="confirmpass"></p><input type="submit" value="Change username"></form></fieldset>';
}

else
{
	if (isset($_POST["newusername"]))
	{
		$newusername = $_POST["newusername"];
		if (strlen($newusername) < 1)
		{
			echo "New username cannot be empty.";
		}
		
		elseif ($newusername == $_SESSION["username"])
		{
			echo "New username cannot be the same as old username.";
		}
		
		elseif (strlen($newusername) > 24)
		{
			echo "New username cannot be longer than 24 characters.";
		}
		
		elseif (!ctype_alnum($newusername))
		{
			echo "New username cannot contain non-alphanumeric characters.";
		}
		
		else
		{
			$result = $db->query("UPDATE users SET username='" . $db->real_escape_string($_POST["newusername"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
			if (!$result) {
				message("Unable to change username.");
			}
			else {
				$_SESSION["username"] = $_POST["newusername"];
				refresh(0);
			}
		}
	}
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("Viewing: Settings");
}

?>
