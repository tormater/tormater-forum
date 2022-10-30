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

if (isset($q3) && is_numeric($q3) && ($q2 == "color") && ($q3 !== 0) && ($q3 <= 16))
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
	echo "<h3>Post color</h3><div class='formcontainer'>";

    $i = 1;

    while ($i <= 16) {
    echo "<a class='settingscolor' postcolor='" . $i . "' href='/settings/color/" . $i . "/'></a>";
    $i++;
    }

    echo "</div>";

	// Display the change username form.
	echo '<h3>Change username</h3><div class="formcontainer">
    <form method="post" action="">
    <label>New username</label><input type="text" name="newusername"><br/>
    <label>Current password</label><input type="password" name="confirmpass"><br/>
    <label></label><input type="submit" class="buttonbig" value="Change username"></form></div>';
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
		
		elseif (!checkUsername($newusername))
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
