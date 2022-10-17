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
	message("Error! Failed to find given user!");
}

else
{
	if ($result->num_rows == 0)
	{
		message("No such user.");
	}
	
	else
	{
		if (($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST["role"])))
		{
			$setrole = $db->query("UPDATE users SET role='" . $db->real_escape_string($_POST["role"]) . "' WHERE userid='" . $db->real_escape_string($q2) . "'");
			
			if (!$setrole)
			{
				echo "Failed to change role.";
			}
			
			refresh(0);
		}
		
		while ($row = $result->fetch_assoc())
		{
			if ($row["verified"] == "1") $verified = "Yes";
			else $verified = "No";
			
            echo '<h2>Viewing profile "' . htmlspecialchars($row["username"]) . '"</h2>';
            echo "<div class='post'><div class='usertop' postcolor='" . htmlspecialchars($row["color"]) . "'><b id='" .$row["role"] . "'>" . htmlspecialchars($row["username"]) . "</b>";
			if ($_SESSION['role'] == "Administrator")
			{
				echo " <form method='post' class='changerole' action=''><select name='role'>";
				
				if ($row['role'] == "Administrator")
				{
					echo '<option value="Administrator" selected>Administrator</option>';
					echo '<option value="Moderator">Moderator</option>';
					echo '<option value="Member">Member</option>';
					echo '<option value="Suspended">Suspended</option>';
				}
				
				elseif ($row['role'] == "Moderator")
				{
					echo '<option value="Administrator">Administrator</option>';
					echo '<option value="Moderator" selected>Moderator</option>';
					echo '<option value="Member">Member</option>';
					echo '<option value="Suspended">Suspended</option>';
				}
				
				elseif ($row['role'] == "Member")
				{
					echo '<option value="Administrator">Administrator</option>';
					echo '<option value="Moderator">Moderator</option>';
					echo '<option value="Member" selected>Member</option>';
					echo '<option value="Suspended">Suspended</option>';
				}
				
				elseif ($row['role'] == "Suspended")
				{
					echo '<option value="Administrator">Administrator</option>';
					echo '<option value="Moderator">Moderator</option>';
					echo '<option value="Member">Member</option>';
					echo '<option value="Suspended" selected>Suspended</option>';
				}
				
				echo "</select><input type='submit' value='Change role'></form></div>";
			
				
			}
			
			else
			{
				
				echo "<br/><span class='userrole'>" . $row["role"] . "</span></div>";
				
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
			echo "<div class='userbottom'><span class='userstat'><label class='shortlabel'>Registered:</label><a title='" . date('m-d-Y h:i:s A', $row['jointime']) . "'>" . relativeTime($row["jointime"]) . "</a></span>" . "<span class='userstat'><label class='shortlabel'>Last active:</label><a title='" . date('m-d-Y h:i:s A', $row['lastactive']) . "'>" . relativeTime($row["lastactive"]) . "</a></span>" . "<span class='userstat'><label class='shortlabel'>Posts:</label>" . $uposts . "</span><span class='userstat'><label class='shortlabel'>Threads:</label>" . $uthreads . "</span><span class='userstat'><label class='shortlabel'>Verified:</label>" . $verified . "</span></div></div>";

			// If the viewing user is logged in, update their last action.
			if ($_SESSION['signed_in'] == true)
			{
				$action = "Viewing: <a href='/user/" . $row["userid"] . "/'>" . $row["username"] . "'s Profile</a>";
				update_last_action($action);
			}
		}
	}
}

include "footer.php";

?>
