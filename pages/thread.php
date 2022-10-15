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
	message("The specified thread doesn't exist.");
	include "footer.php";
	exit;
}

if(!$posts)
{
	message("There are no posts in this thread.");
	include "footer.php";
	exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(!$_SESSION['signed_in'])
	{
		echo 'You must be signed in for any action within a thread.';
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
				message("You tried to post too soon after a previous post. The post delay is currently " . $config["postDelay"] . " seconds between posts.");
				$contentSave = $_POST["content"];
			}
			
			else
			{
				if (strlen($_POST["content"]) < 1)
				{
					message("Your post cannot be blank.");
				}
			
				elseif (strlen($_POST["content"]) > $config["maxCharsPerPost"])
				{
					message("Your post was too long. The maximum number of characters a post may contain is currently set to " . $config["maxCharsPerPost"] . ".");
				}
		
				else
				{
					$result = $db->query("INSERT INTO posts (thread, user, timestamp, content) VALUES ('" . $db->real_escape_string($q2) . "', '" . $_SESSION["userid"] . "', '" . time() . "', '" . $db->real_escape_string($_POST["content"]) . "')");
					$update = $db->query("UPDATE threads SET posts=posts+1, lastpostuser='" . $_SESSION["userid"] . "', lastposttime='" . time() . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
						
					if(!$result)
					{
						echo 'Your reply has not been saved, please try again later.';
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
				echo "Sorry, post couldn't be deleted.";
			}
			
			else
			{
				// Now we need to update the thread's data to be in sync with the remaining posts.
				$lastpost = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp DESC LIMIT 1");
				if ((!$lastpost) or ($lastpost->num_rows == 0))
				{
					echo "Something went wrong with resynchronizing the conversation. Perhaps there are no posts left?";
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
				message("Sorry, post couldn't be hidden.");
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
				message("Sorry, post couldn't be restored.");
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
					message("Hey, you don't have permission to edit that post.");
				}
				
				else
				{
					$result = $db->query("UPDATE posts SET content='" . $db->real_escape_string($_POST["saveedit"]) . "' WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
			
					if (!$result)
					{
						message("Sorry, post couldn't be restored.");
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
				message("Sorry, thread couldn't be deleted.");
			}
			
			else
			{
				$result = $db->query("DELETE FROM posts WHERE thread='" . $db->real_escape_string($q2) . "'");
				
				if (!$result)
				{
					message("Sorry, the thread's posts couldn't be deleted.");
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
				message("Sorry, thread couldn't be locked.");
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
				message("Sorry, thread couldn't be stickied.");
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
				message("Sorry, thread couldn't be unlocked.");
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
				message("Sorry, thread couldn't be unstickied.");
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
	message("The specified thread doesn't exist.");
}
	
elseif($posts->num_rows == 0)
{
	message("Couldn't find any posts.");
}
	
else
{
	echo '</br><a class="item" href="/category/' . $category . '/">Back to category</a>';
	if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))
	{
		echo '<form style="display:inline;" action="" method="post"><button name="deletethread" class="threadbutton" value="' . $q2 . '">Delete thread</button></form>';
		if ($locked == 1)
		{
			echo '<form style="display:inline;" action="" method="post"><button name="unlockthread" class="threadbutton" value="' . $q2 . '">Unlock thread</button></form>';
		}
		else
		{
			echo '<form style="display:inline;" action="" method="post"><button name="lockthread" class="threadbutton" value="' . $q2 . '">Lock thread</button></form>';
		}
		if ($stickied == 1)
		{
			echo '<form style="display:inline;" action="" method="post"><button name="unstickythread" class="threadbutton" value="' . $q2 . '">Unsticky thread</button></form>';
		}
		else
		{
			echo '<form style="display:inline;" action="" method="post"><button name="stickythread" class="threadbutton" value="' . $q2 . '">Sticky thread</button></form>';
		}
	}
	echo '<h2>Posts in "' . htmlspecialchars($title) . '"</h2>';
	
	if ($locked == 1 && $stickied == 1)
	{
		echo '<font class="sticky">Sticky</font> <font class="locked">Locked</font></br></br>';
	}
	
	elseif ($locked == 1)
	{
		echo '<font class="locked">Locked</font></br></br>';
	}
	
	elseif ($stickied == 1)
	{
		echo '<font class="sticky">Sticky</font></br></br>';
	}
		
	echo "<div class='paginationleft'>";
		
	if ($currentPage == 1)
	{
		echo "<font color=white>First page</font> <font color=white>Previous page</font>";
	}
	else
	{
		echo "<a href='/thread/" . $q2 . "/1/'>First page</a> <a href='/thread/" . $q2 . "/" . ($currentPage - 1) . "/'>Previous page</a>";
	}
		
	echo "</div><div class='paginationright'>";
		
	if ($currentPage == $pages)
	{
		echo "<font color=white>Next page</font> <font color=white>Last page</font>";
	}
	else
	{
		echo "<a href='/thread/" . $q2 . "/" . ($currentPage + 1) . "/'>Next page</a> <a href='/thread/" . $q2 . "/" . $pages ."/'>Last page</a>";
	}
		
	echo "</div></br>";
					
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
					echo '<div class="hiddenpost"><b><a href="/user/' . $u["userid"] . '/" id="' . $u["role"] . '">' . htmlspecialchars($u["username"]) . "</a></b> <a title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "'>" . relativeTime($row["timestamp"]) . '</a> (hidden by <a href="/user/' . $row["deletedby"] . '/" id="' . $h["role"] . '">' . $h["username"] . '</a>)';
					if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator") or ($u["userid"] == $_SESSION["userid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
					{
						echo '<form class="postc" action="" method="post"><button name="restore" value="' . $row["postid"] . '">Restore</button></form>';
					}
					echo '</div></br>';
				}
			}
			
			else
			{   // <a href='/user/" . $_SESSION["userid"] . "/' id='" . $_SESSION["role"] . "'>" . $_SESSION["username"] . "</a>."
				echo '<div postcolor="' . $u["color"] . '" class="thread">';
				echo '<b><a href="/user/' . $u["userid"] . '/" id="' . $u["role"] . '">' . htmlspecialchars($u["username"]) . "</a></b> <a title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "'>" . relativeTime($row["timestamp"]) . "</a>";
				if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator") or ($u["userid"] == $_SESSION["userid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
				{
					echo '<form class="postc" action="" method="post"><button name="delete" value="' . $row["postid"] . '">Delete</button></form>';
					echo '<form class="postc" action="" method="post"><button name="hide" value="' . $row["postid"] . '">Hide</button></form>';
					echo '<form class="postc" action="" method="post"><button name="edit" value="' . $row["postid"] . '">Edit</button></form>';
				}
				
				if (isset($_POST["edit"]) && ($_POST["edit"] == $row["postid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
				{
					echo '</div><form method="post" action="">';				
					echo '<textarea name="saveedit" />' . ($row["content"]) . '</textarea><textarea style="display:none;" name="saveeditpostid">' . $row["postid"] . '</textarea></br><input type="submit" value="Save edit"></form></br>';
				}
				
				else
				{
					echo '</div><div class="threadcontent">' . htmlspecialchars($row["content"]) . '</div></br>';
				}
			}
		}
	}
		
	echo "<div class='paginationleft'>";
		
	if ($currentPage == 1)
	{
		echo "<font color=white>First page</font> <font color=white>Previous page</font>";
	}
	else
	{
		echo "<a href='/thread/" . $q2 . "/1/'>First page</a> <a href='/thread/" . $q2 . "/" . ($currentPage - 1) . "/'>Previous page</a>";
	}
		
	echo "</div><div class='paginationright'>";
		
	if ($currentPage == $pages)
	{
		echo "<font color=white>Next page</font> <font color=white>Last page</font>";
	}
	else
	{
		echo "<a href='/thread/" . $q2 . "/" . ($currentPage + 1) . "/'>Next page</a> <a href='/thread/" . $q2 . "/" . $pages ."/'>Last page</a>";
	}
		
	echo "</div></br>";
	
	if ($_SESSION["role"] == "Suspended")
	{
		message("Unfortunately, you're suspended. Suspended users cannot post, sorry.");
	}
		
	elseif (($_SESSION['signed_in'] == true) && ($locked == 0))
	{
		echo '<form method="post" action="">';				
		echo 'Content:</br><textarea name="content" />'; if (isset($contentSave)) echo $contentSave; echo '</textarea></br>
			<input type="submit" class="postreply" value="Post reply">
			</form>';
	}
	
	elseif ($_SESSION['signed_in'] == true && $locked == 1 && ((!$_SESSION["role"] == "Moderator") or (!$_SESSION["role"] == "Administrator")))
	{
		message("Sorry, this thread is locked. Only moderators and administrators can post in it.");
	}
	
	elseif ((($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) && $locked == 1)
	{
		echo '<form method="post" action="">';				
		echo 'Content:</br><textarea name="content" />'; if (isset($contentSave)) echo $contentSave; echo '</textarea></br>
			<input type="submit" class="postreply" value="Post reply">
			</form>';
	}
	
	else
	{
		message("You must be signed in to post.");
	}
}

include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	while($row = $thread->fetch_assoc())
	{
		$action = "Viewing: <a href='/thread/" . $row["threadid"] . "/'>" . $row["title"] . "</a>";
		
		update_last_action($action);
	}
}

?>
