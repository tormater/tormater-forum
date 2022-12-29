<?php
// user.page.php
// Displays a given user's profile.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

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
		if (($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST["role"])) and (($_SESSION["role"] == "Administrator") or ($_SESSION["role"] == "Moderator")))
		{
            		if (is_numeric($q2) and ($_SESSION["userid"] != $q2) and ($config["mainAdmin"] != $q2)) {
                		if (($_SESSION["role"] == "Moderator") and ($_POST["role"] != "Member") and ($_POST["role"] != "Suspended")) {
                    			// Do nothing
                		}
                	else {
			        $setrole = $db->query("UPDATE users SET role='" . $db->real_escape_string($_POST["role"]) . "' WHERE userid='" . $db->real_escape_string($q2) . "'");
			
			        if (!$setrole)
			        {
				        echo $lang["user.FaildChangeRole"];
			        }
			
			        refresh(0);
                		}
            		}
		}

		while ($row = $result->fetch_assoc())
		{
            		if ($row["deleted"] == "1") {
                		$username = $lang["user.Deleted"] . $row["userid"];
            		}
            		else {
                		$username = $row["username"];
            		}
            		$userid = $row["userid"];
            		$color = $row["color"];
            		$role = $row["role"];
            		$verified = $row["verified"];
            		$lastactive = $row["lastactive"];
            		$jointime = $row["jointime"];
			$deleted = $row["deleted"];
			$avatar = $row["avatar"];
			$avatarTime = $row["avataruploadtime"];
        	}

        	include "header.php";

		if ($verified == "1") $verified = $lang["user.VerifiedYes"];
		else $verified = $lang["user.VerifiedNo"];
		
		if ($deleted == "1") {
            		$deleted = $lang["user.DeletedYes"];
            		$delClass = " deleteduser";
        	}
		else $deleted = $lang["user.DeletedNo"];
		
		if ($avatar == "none") {
            		$uAvatar = "";
        	}
        	else {
            		$uAvatar = '<img class="avatar" src="' . genURL("avatars/" . $userid . "." . $avatar . "?t=" . $avatarTime) . '">';
        	}
			
            	echo '<h2>'.$lang["user.ViewingProfile"].' "' . htmlspecialchars($username) . '"</h2>';
            	echo '<div class="post' . $delClass . '"><div class="usertop" postcolor="' . htmlspecialchars($color) . '">' . $uAvatar . '<b id="' . $role . '">' . htmlspecialchars($username) . '</b>';
		if (($_SESSION['role'] == "Administrator") and ($_SESSION["userid"] != $userid) and ($config["mainAdmin"] != $userid))
		{
			echo '<div class="forminput"><form method="post" class="changerole" action=""><select name="role">';
				
			if ($role == "Administrator")
			{
				echo '<option value="Administrator" selected>'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Moderator")
			{
				echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator" selected>'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Member")
			{
				echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member" selected>'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Suspended")
			{
				echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended" selected>'.$lang["user.OptionSuspended"].'</option>';
			}
				
			echo '</select><div class="forminput"><input type="submit" class="buttonrole" value="'.$lang["user.ChangeRole"].'"></div></form></div></div>';
			
				
		}

            	elseif (($_SESSION['role'] == "Moderator") and ($role != "Administrator") and ($role != "Moderator") and ($_SESSION["userid"] != $userid) and ($config["mainAdmin"] != $userid))
		{
			echo '<div class="forminput"><form method="post" class="changerole" action=""><select name="role">';

				
			if ($role == "Member")
			{
				echo '<option value="Member" selected>'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Suspended")
			{
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended" selected>'.$lang["user.OptionSuspended"].'</option>';
			}
				
			echo '</select><div class="forminput"><input type="submit" class="buttonrole" value="'.$lang["user.ChangeRole"].'"></div></form></div></div>';
			
				
		}
			
		else
		{				
			echo '<div class="userrole">' . $lang["role." . $role] . '</div></div>';
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
            		<h3>' . $lang["user.UserInformation"] . '</h3>
			<span class="userstat"><label class="shortlabel">'.$lang["user.TitleRegistered"].'</label><a title="' . date('m-d-Y h:i:s A', $jointime) . '">' . relativeTime($jointime) . '</a></span>
			<span class="userstat"><label class="shortlabel">'.$lang["user.TitleLastActive"].'</label><a title="' . date('m-d-Y h:i:s A', $lastactive) . '">' . relativeTime($lastactive) . '</a></span>
			<span class="userstat"><label class="shortlabel">'.$lang["user.TitlePosts"].'</label>' . $uposts . '</span>
			<span class="userstat"><label class="shortlabel">'.$lang["user.TitleThreads"].'</label>' . $uthreads . '</span>
			<span class="userstat"><label class="shortlabel">'.$lang["user.TitleVerified"].'</label>' . $verified . '</span>
			<span class="userstat"><label class="shortlabel">'.$lang["user.TitleDeleted"].'</label>' . $deleted . '</span>';

        $bioCheck = $db->query("SELECT bio FROM users WHERE userid='" . $db->real_escape_string($q2) . "'");
        
        while ($b = $bioCheck->fetch_assoc()) {
            if ((!$b["bio"]) or (!isset($b["bio"])) or ($b["bio"] == "")) {
                // Do nothing
            }
            else {
                echo("<h3>" . $lang["userpanel.Bio"] . "</h3><span class='userstat'>" . formatPost($b["bio"]) . "</span>");
            }
        }
        echo '</div></div>';

	}

echo '<div class="userposts" postcolor="' . htmlspecialchars($color) . '">';
echo '<span class="userpostsh">' . $lang["user.RecentPosts"] . '</span>';
$posts = $db->query("SELECT * FROM posts WHERE user='" . $db->real_escape_string($q2) . "' AND deletedby IS NULL ORDER BY timestamp DESC LIMIT 5");

if($posts->num_rows == 0)
{
	message($lang["thread.NoPosts"]);
}
	
else
{
	while($row = $posts->fetch_assoc())
	{
		$userinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["user"] . "'");
			
		while($u = $userinfo->fetch_assoc())
		{
			echo '<div class="userpost">' . formatPost($row["content"]) . '</div>';
            echo "<span class='postdate' title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "'>" . relativeTime($row["timestamp"]) . "</span> - <a class='userpostlink' href='" . genURL("thread/" . $row["thread"]) . "'>" . $lang["user.ViewThread"] . "</a>";
		}
	}
}
echo "</div>";
}

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	$action = $lang["action.Generic"]. '<a href="' . genURL('user/' . $userid) . '/">' . $username . $lang["action.UserProfile"] . '</a>';
	update_last_action($action);
}

include "footer.php";

?>


?>
