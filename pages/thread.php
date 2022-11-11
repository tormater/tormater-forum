<?php
// thread.php
// Shows the inside of a thread and allows users to post.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

// Get the thread's information.
$thread = $db->query("SELECT * FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");

// Find out what page we're on.
if (isset($q3) && is_numeric($q3)) {
	$currentPage = $q3;
}
else
{
	// If no valid page is specified then always assume we're on the first page.
	$currentPage = 1;
}

while($row = $thread->fetch_assoc()) {
	// Thread information.
	$category = $row['category'];
	$title = $row['title'];
	$locked = $row['locked'];
	$stickied = $row['sticky'];
	
	// Important details for sorting the thread into pages.
	$numPosts = $row['posts'];
	$pages = ceil($numPosts / $config["postsPerPage"]);
}

// Calculate the offset for the posts query.
$offset = (($currentPage * $config["postsPerPage"]) - $config["postsPerPage"]);

$posts = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp LIMIT " . $config["postsPerPage"] . " OFFSET " . $offset . "");

if(!$thread)
{
	message($lang["thread.ThreadDoesntExist"]);
	include "footer.php";
	exit;
}

if(!$posts)
{
	message($lang["thread.ThreadsNoPosts"]);
	include "footer.php";
	exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(!$_SESSION['signed_in'])
	{
		echo $lang["thread.LoginFirst"];
	}
	else
	{
		// If the user is posting...
		if (($_POST["content"]) && ($_SESSION['signed_in'] == true) && (!($_SESSION["role"] == "Suspended")) && ($locked == 0 or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))))
		{
			// First check and see if the user has made a post too recently according to the post delay.
			$delaycheck = $db->query("SELECT 1 FROM posts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - $config["postDelay"]) . "'");
			
			if ($delaycheck->num_rows > 0)
			{
				message($lang["thread.PostSoon1"] . ' ' . $config["postDelay"] . ' ' . $lang["thread.PostSoon2"]);
				$contentSave = $_POST["content"];
			}
			
			else
			{
				if (strlen($_POST["content"]) < 1)
				{
					message($lang["thread.PostEmpty"]);
				}
			
				elseif (strlen($_POST["content"]) > $config["maxCharsPerPost"])
				{
					message($lang["thread.PostBig1"] . ' ' . $config["maxCharsPerPost"] . ' ' . $lang["thread.PostBig2"]);
				}
		
				else
				{
					$result = $db->query("INSERT INTO posts (thread, user, timestamp, content) VALUES ('" . $db->real_escape_string($q2) . "', '" . $_SESSION["userid"] . "', '" . time() . "', '" . $db->real_escape_string($_POST["content"]) . "')");
					$update = $db->query("UPDATE threads SET posts=posts+1, lastpostuser='" . $_SESSION["userid"] . "', lastposttime='" . time() . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
						
					if(!$result)
					{
						echo $lang["thread.PostError"];
					}
					else
					{
						refresh(0);
					}
				}
			}
		}
		
		// If the user is requesting to delete a post...
		elseif (($_POST["delete"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("DELETE FROM posts WHERE postid='" . $db->real_escape_string($_POST["delete"]) . "'");
			
			if (!$result)
			{
				echo $lang["thread.PostDeleteError"];
			}
			else
			{
				// Now we need to update the thread's data to be in sync with the remaining posts.
				$lastpost = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp DESC LIMIT 1");
				if ((!$lastpost) or ($lastpost->num_rows == 0))
				{
					echo $lang["thread.ThreadDataError"];
				}
				
				else
				{
					while($row = $lastpost->fetch_assoc())
					{
						$update = $db->query("UPDATE threads SET posts=posts-1, lastpostuser='" . $row["user"] . "', lastposttime='" . $row["timestamp"] . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
					}
					
					refresh(0);
				}
			}
		}
		
		// If the user is requesting to hide a post...
		elseif (($_POST["hide"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("UPDATE posts SET deletedby='" . $_SESSION["userid"] . "' WHERE postid='" . $db->real_escape_string($_POST["hide"]) . "'");
			
			if (!$result)
			{
				message($lang["thread.PostHiddenError"]);
			}
			
			else
			{
				refresh(0);
			}
		}
		
		// If the user is requesting to restore a post...
		elseif (($_POST["restore"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("UPDATE posts SET deletedby=NULL WHERE postid='" . $db->real_escape_string($_POST["restore"]) . "'");
			
			if (!$result)
			{
				message($lang["thread.PostRestoredError"]);
			}
			
			else
			{
				refresh(0);
			}
		}
		
		// If the user is requesting to save an edit...
		elseif (($_POST["saveedit"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
		{
			// First make sure the user has permission to edit the specified post.
			$permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
			while ($p = $permission->fetch_assoc())
			{
				if ((!$p["user"] == $_SESSION["userid"]) && ((!$_SESSION["role"] == "Moderator") or (!$_SESSION["role"] == "Administrator")))
				{
					message($lang["thread.PostEditError"]);
				}
				
				else
				{
					$result = $db->query("UPDATE posts SET content='" . $db->real_escape_string($_POST["saveedit"]) . "' WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
			
					if (!$result)
					{
						message($lang["thread.PostRestoredError"]);
					}
			
					else
					{
						refresh(0);
					}
				}
			}
		}
		
		// If the user is requesting to delete the thread...
		elseif (($_POST["deletethread"]) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
			if (!$result)
			{
				message($lang["thread.ThreadDeleteError"]);
			}
			
			else
			{
				$result = $db->query("DELETE FROM posts WHERE thread='" . $db->real_escape_string($q2) . "'");
				
				if (!$result)
				{
					message($lang["thread.ThreadPostDeleteError"]);
				}
				
				else
				{
					redirect("category/" . $category . "/");
				}
			}
		}
		
		// If the user is requesting to lock the thread...
		elseif (($_POST["lockthread"]) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("UPDATE threads SET locked='1' WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
			if (!$result)
			{
				message($lang["thread.ThreadLockError"]);
			}
			
			else
			{
				refresh(0);
			}
		}
		
		// If the user is requesting to sticky the thread...
		elseif (($_POST["stickythread"]) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("UPDATE threads SET sticky='1' WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
			if (!$result)
			{
				message($lang["thread.ThreadStickError"]);
			}
			
			else
			{
				refresh(0);
			}
		}
		
		// If the user is requesting to unlock the thread...
		elseif (($_POST["unlockthread"]) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("UPDATE threads SET locked='0' WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
			if (!$result)
			{
				message($lang["thread.ThreadUnlockError"]);
			}
			
			else
			{
				refresh(0);
			}
		}
		
		// If the user is requesting to unsticky the thread...
		elseif (($_POST["unstickythread"]) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
		{
			$result = $db->query("UPDATE threads SET sticky='0' WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
			if (!$result)
			{
				message($lang["thread.ThreadUnstickError"]);
			}
			
			else
			{
				refresh(0);
			}
		}
	}
}

if($thread->num_rows == 0)
{
	message($lang["thread.ThreadDoesntExist"]);
}
	
elseif($posts->num_rows == 0)
{
	message($lang["thread.NoPosts"]);
}
	
else
{
	echo '<div><a class="item" href="/category/' . $category . '/">'.$lang["thread.BackToCategory"].'</a></div>';
	if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))
	{
		echo '<div class="modtools">';
		echo '<form action="" method="post"><button name="deletethread" class="threadbutton" value="' . $q2 . '">'.$lang["thread.DeleteThreadBtn"].'</button></form>';
		if ($locked == 1)
		{
			echo '<form action="" method="post"><button name="unlockthread" class="threadbutton" value="' . $q2 . '">'.$lang["thread.UnlockThreadBtn"].'</button></form>';
		}
		else
		{
			echo '<form action="" method="post"><button name="lockthread" class="threadbutton" value="' . $q2 . '">'.$lang["thread.LockThreadBtn"].'</button></form>';
		}
		if ($stickied == 1)
		{
			echo '<form action="" method="post"><button name="unstickythread" class="threadbutton" value="' . $q2 . '">'.$lang["thread.UnstickyThreadBtn"].'</button></form>';
		}
		else
		{
			echo '<form action="" method="post"><button name="stickythread" class="threadbutton" value="' . $q2 . '">'.$lang["thread.StickyThreadBtn"].'</button></form>';
		}
		echo '</div>';
	}
	echo '<h2>'. htmlspecialchars($title) . '</h2>'; // $lang["thread.PostInTitle"]
	
	if ($locked == 1 or $stickied == 1)
	{
		echo '<div>'.$lang["thread.Labels"];
	
	
	    if ($locked == 1)
	    {
	    	echo '<font class="locked">'.$lang["label.Locked"].'</font>';
	    }
	
	    if ($stickied == 1)
	    {
	    	echo '<font class="sticky">'.$lang["label.Sticky"].'</font>';
        }
	    echo '</div>';
    }

    // Draw page bar
	pagination(thread);

	while($row = $posts->fetch_assoc())
	{
		$userinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["user"] . "'");
			
		while($u = $userinfo->fetch_assoc())
		{
			if (isset($row["deletedby"]))
			{
				$hider = $db->query("SELECT * FROM users WHERE userid='" . $row["deletedby"] . "'");
				
				while ($h = $hider->fetch_assoc())
				{ 
					echo '<div class="hiddenpost"><b><a href="/user/' . $u["userid"] . '/" id="' . $u["role"] . '">' . htmlspecialchars($u["username"]) . "</a></b> <span title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "' class='postdate'>" . relativeTime($row["timestamp"]) . '</span> (hidden by <a href="/user/' . $row["deletedby"] . '/" id="' . $h["role"] . '">' . $h["username"] . '</a>)';
					if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator") or ($u["userid"] == $_SESSION["userid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
					{
						echo '<form class="rpostc" action="" method="post"><button name="restore" value="' . $row["postid"] . '">'.$lang["post.RestoreHiddenBtn"].'</button></form>';
					}
					echo '</div>';
				}
			}
			
			else
			{   // <a href='/user/" . $_SESSION["userid"] . "/' id='" . $_SESSION["role"] . "'>" . $_SESSION["username"] . "</a>."
				echo '<div class="post"><div postcolor="' . $u["color"] . '" class="thread">';
				echo '<b><a href="/user/' . $u["userid"] . '/" id="' . $u["role"] . '">' . htmlspecialchars($u["username"]) . "</a></b><span class='postdate' title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "'>" . relativeTime($row["timestamp"]) . "</span>";
				if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator") or ($u["userid"] == $_SESSION["userid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
				{
                    echo '<div>';
					echo '<form class="postc" action="" method="post"><button name="edit" value="' . $row["postid"] . '">'.$lang["post.EditBtn"].'</button></form>';
					if((!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
					{
						echo '<form class="postc" action="" method="post"><button name="hide" value="' . $row["postid"] . '">'.$lang["post.HideBtn"].'</button></form>';
						echo '<form class="postc" action="" method="post"><button name="delete" value="' . $row["postid"] . '">'.$lang["post.DeleteBtn"].'</button></form>';
					}
					echo '</div>';
				}
				
				if (isset($_POST["edit"]) && ($_POST["edit"] == $row["postid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
				{
					echo '</div><form method="post" action="" class="editbox">';				
					echo '<div class="forminput"><textarea name="saveedit" />' . ($row["content"]) . '</textarea><textarea style="display:none;" name="saveeditpostid">' . $row["postid"] . '</textarea></div>';
					echo '<div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["post.SaveEditBtn"].'"> <a class="buttonbig" href="">'.$lang["post.DiscardEditBtn"].'</a></form></div>';
				}
				
				else
				{
					echo '</div><div class="threadcontent">' . formatPost($row["content"]) . '</div>';
				}
                echo "</div>";
			}
		}
	}
		
    // Draw page bar
	pagination(thread);
	
	if ($_SESSION["role"] == "Suspended")
	{
		message($lang["thread.SuspendCantPost"]);
	}
		
	elseif (($_SESSION['signed_in'] == true) && ($locked == 0) or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) && $locked == 1)
	{
		echo '<form method="post" action="">';				
		echo '<div class="forminput">'.$lang["thread.ContentTitle"].'</div>';
		echo '<div class="forminput"><textarea name="content" />';
		if (isset($contentSave)) echo $contentSave;
		echo '</textarea></div>
			<div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["thread.PostReplyBtn"].'"></div>
			</form>';
	}
	
	elseif ($_SESSION['signed_in'] == true && $locked == 1)
	{
		message($lang["thread.ThreadLocked"]);
	}
	
	else
	{
		message($lang["thread.LoginToPost"]);
	}
}

include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	while($row = $thread->fetch_assoc())
	{
		$action = $lang["action.Generic"] . ' <a href="/thread/' . $row["threadid"] . '/">' . $row["title"] . '</a>';
		
		update_last_action($action);
	}
}

?>
