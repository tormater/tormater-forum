<?php
// user.php
// Displays a given user's profile.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

// Start off by making a query using the given userid.
$result = $db->query("SELECT * FROM users WHERE userid='" . $db->real_escape_string($q2) . "'");

if (!$result)
{
	message($lang["user.FaildFindUser"]);
}

else
{
	if ($result->num_rows == 0)
	{
		message($lang["user.NoSuchUser"]);
	}
	
	else
	{
		if (($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST["role"])))
		{
			$setrole = $db->query("UPDATE users SET role='" . $db->real_escape_string($_POST["role"]) . "' WHERE userid='" . $db->real_escape_string($q2) . "'");
			
			if (!$setrole)
			{
				echo $lang["user.FaildChangeRole"];
			}
			
			refresh(0);
		}
		
		while ($row = $result->fetch_assoc())
		{
			if ($row["verified"] == "1") $verified = $lang["user.VerifiedYes"];
			else $verified = $lang["user.VerifiedNo"];
			
            echo '<h2>'.$lang["user.ViewingProfile"].' "' . htmlspecialchars($row["username"]) . '"</h2>';
            echo '<div class="post"><div class="usertop" postcolor="' . htmlspecialchars($row["color"]) . '"><b id="' .$row["role"] . '">' . htmlspecialchars($row["username"]) . '</b>';
			if ($_SESSION['role'] == "Administrator")
			{
				echo '<div class="forminput"><form method="post" class="changerole" action=""><select name="role">';
				
				if ($row['role'] == "Administrator")
				{
					echo '<option value="Administrator" selected>'.$lang["user.OptionAdmin"].'</option>';
					echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
					echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
					echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
				}
				
				elseif ($row['role'] == "Moderator")
				{
					echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
					echo '<option value="Moderator" selected>'.$lang["user.OptionMod"].'</option>';
					echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
					echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
				}
				
				elseif ($row['role'] == "Member")
				{
					echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
					echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
					echo '<option value="Member" selected>'.$lang["user.OptionMember"].'</option>';
					echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
				}
				
				elseif ($row['role'] == "Suspended")
				{
					echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
					echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
					echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
					echo '<option value="Suspended" selected>'.$lang["user.OptionSuspended"].'</option>';
				}
				
				echo '</select><div class="forminput"><input type="submit" class="buttonrole" value="'.$lang["user.ChangeRole"].'"></div></form></div></div>';
			
				
			}
			
			else
			{
				
				echo '<div class="userrole">' . $row["role"] . '</div></div>';
				
			}
			$posts = $db->query("SELECT 1 FROM posts WHERE user='" . $db->real_escape_string($q2) . "'");
				
			$uposts = 0;
				
			while ($p = $posts->fetch_assoc())
			{
				// Had to be ghetto here and increment a custom variable because num_rows was throwing tons of
				// notices for no reason and wouldn't return with a number like it's supposed to.
				$uposts += 1;
			}
				
			$threads = $db->query("SELECT 1 FROM threads WHERE startuser='" . $db->real_escape_string($q2) . "'");
				
			$uthreads = 0;
				
			while ($t = $threads->fetch_assoc())
			{
				$uthreads +=1;
			}
			echo '<div class="userbottom">
				<span class="userstat"><label class="shortlabel">'.$lang["user.TitleRegistered"].'</label>
				<a title="' . date('m-d-Y h:i:s A', $row['jointime']) . '">' . relativeTime($row["jointime"]) . '</a></span>
				<span class="userstat"><label class="shortlabel">'.$lang["user.TitleLastActive"].'</label>
				<a title="' . date('m-d-Y h:i:s A', $row['lastactive']) . '">' . relativeTime($row["lastactive"]) . '</a></span>
				<span class="userstat"><label class="shortlabel">'.$lang["user.TitlePosts"].'</label>' . $uposts . '</span>
				<span class="userstat"><label class="shortlabel">'.$lang["user.TitleThreads"].'</label>' . $uthreads . '</span>
				<span class="userstat"><label class="shortlabel">'.$lang["user.TitleVerified"].'</label>' . $verified . '</span></div></div>';

			// If the viewing user is logged in, update their last action.
			if ($_SESSION['signed_in'] == true)
			{
				$action = $lang["action.Generic"]. '<a href="/user/' . $row["userid"] . '/">' . $row["username"] . $lang["action.UserProfile"] . '</a>';
				update_last_action($action);
			}
		}
	}
}

include "footer.php";

?>
