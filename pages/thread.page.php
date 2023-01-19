<?php
// thread.page.php
// Shows the inside of a thread and allows users and mods/admins to perform many actions involving posts and the thread itself.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

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

include 'header.php';

echo ("<script type='text/javascript'>
function quotePost(id) {
    var Field = document.getElementById('textbox1');
    var val = Field.value;
    var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
    var before_txt = val.substring(0, Field.selectionStart);
    var after_txt = val.substring(Field.selectionEnd, val.length);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            Field.value += this.responseText;;
            Field.focus();
            Field.setSelectionRange(Field.selectionStart,Field.selectionEnd);
       }
    };
    xhttp.open('POST', '" . genURL("pages/thread.ajax.php") . "', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('postid=' + id); 
}
</script>");

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
		if (($_POST["postReply"]) && ($_POST["content"]) && ($_SESSION['signed_in'] == true) && ($_SESSION["role"] != "Suspended") && ($locked == 0 or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))))
		{
			// First check and see if the user has made a post too recently according to the post delay.
			$delaycheck = $db->query("SELECT 1 FROM posts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - $config["postDelay"]) . "'");
			
			if ($delaycheck->num_rows > 0)
			{
				message(sprintf($lang["thread.PostSoon"], $config["postDelay"]));
				$contentSave = $_POST["content"];
			}
			
			else
			{
				if (mb_strlen($_POST["content"]) < 1)
				{
					message($lang["thread.PostEmpty"]);
				}
			
				elseif (mb_strlen($_POST["content"]) > $config["maxCharsPerPost"])
				{
                    			message(sprintf($lang["thread.PostBig"], $config["maxCharsPerPost"]));
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
                        // If the post was successful, remove any drafts the user might have.
                        $db->query("DELETE FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND thread='" . $db->real_escape_string($q2) . "'");

						redirect("thread/" . $q2 . "/" . $pages . "/#footer");
					}
				}
			}
		}

        // If the user is saving a draft...
		elseif (($_POST["saveDraft"]) && ($_POST["content"]) && ($_SESSION['signed_in'] == true) && ($_SESSION["role"] != "Suspended") && ($locked == 0 or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))))
        {
            // Check and make sure the user hasn't made too many drafts in the past minute.
            $draftCheck = $db->query("SELECT 1 FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - 60) . "'");

            // If the user made too many drafts, print an error message.
            if ($draftCheck->num_rows >= $config["draftsPerMinute"]) {
                echo $lang["thread.DraftError"];
            }

            // Otherwise go ahead and save the user's draft.
            else {
                // Check and make sure the draft content isn't too small...
                if (mb_strlen($_POST["content"]) < 1)
				{
					message($lang["thread.PostEmpty"]);
				}
			
                // ...and isn't too big.
				elseif (mb_strlen($_POST["content"]) > $config["maxCharsPerPost"])
				{
                    message(sprintf($lang["thread.PostBig"], $config["maxCharsPerPost"]));
				}

                // Now actually save the draft post.
                else {
                    $result = $db->query("INSERT INTO drafts (user, thread, timestamp, content) VALUES ('" . $_SESSION["userid"] . "', '" . $db->real_escape_string($q2) . "', '" . time() . "', '" . $db->real_escape_string($_POST["content"]) . "')");

                    if(!$result)
					{
						echo $lang["thread.PostError"];
					}
					else
					{
						redirect("thread/" . $q2 . "/" . $pages . "/#footer");
					}
                }
            }
        }

        // If the user is discarding a draft...
		elseif (($_POST["discardDraft"]) && ($_SESSION['signed_in'] == true) && ($_SESSION["role"] != "Suspended"))
        {
            $db->query("DELETE FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND thread='" . $db->real_escape_string($q2) . "'");
        }
		
		// If the user is requesting to delete a post...
		elseif (($_POST["delete"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true))
		{
            		// First make sure the user has permission to delete the specified post.
			$permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["delete"]) . "'");
			while ($p = $permission->fetch_assoc()) {
                		if (($_SESSION["userid"] == $p["user"]) or ($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) {
			        	$result = $db->query("DELETE FROM posts WHERE postid='" . $db->real_escape_string($_POST["delete"]) . "'");
			
                    			$postCheck = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp DESC LIMIT 1");

			        	if (!$result)
			        	{
				        	echo $lang["thread.PostDeleteError"];
			        	}

                    			// If this is the last post in the thread, delete the thread too.
			        	if ((!$postCheck) or ($postCheck->num_rows == 0))
			        	{
				        	$resultd = $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
				        	if (!$resultd)
				        	{
					        	message($lang["thread.ThreadDeleteError"]);
				        	}
			
				        	else
				        	{
					        	$resultd = $db->query("DELETE FROM posts WHERE thread='" . $db->real_escape_string($q2) . "'");
				
					        	if (!$resultd)
					        	{
						        	message($lang["thread.ThreadPostDeleteError"]);
					        	}
				
					        	else
					        	{
						        	redirect("category/" . $category . "/");
					        	}
                        			}
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
			}
		}
		
		// If the user is requesting to hide a post...
		elseif (($_POST["hide"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true))
		{
            		// First make sure the user has permission to hide the specified post.
			$permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["hide"]) . "'");
			while ($p = $permission->fetch_assoc()) {
                		if (($_SESSION["userid"] == $p["user"]) or ($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) {
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
            		}
		}
		
		// If the user is requesting to restore a post...
		elseif (($_POST["restore"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true))
		{
            		// First make sure the user has permission to restore the specified post.
			$permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["restore"]) . "'");
			while ($p = $permission->fetch_assoc()) {
                		if (($_SESSION["userid"] == $p["user"]) or ($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) {
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
            		}
		}
		
		// If the user is requesting to save an edit...
		elseif (($_POST["saveedit"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true))
		{
			// First make sure the user has permission to edit the specified post.
			$permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
			while ($p = $permission->fetch_assoc())
			{
				if (($p["user"] != $_SESSION["userid"]) && (($_SESSION["role"] != "Moderator") or ($_SESSION["role"] != "Administrator")))
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

        	// If the user is requesting to move the thread...
		elseif (($_POST["movethread"]) && ($_POST["category"]) && ($_SESSION['signed_in'] == true) && (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))
        	{
            		// Only proceed if the values the user entered are valid.
            		if (is_numeric($_POST["category"]) or is_numeric($_POST["movethread"])) {
                		// Make sure the category is valid.
                		$categoryCheck = $db->query("SELECT * FROM categories");

                 		while($row = $categoryCheck->fetch_assoc()) {
                     			if ($row["categoryid"] == $_POST["category"]) {
                         			$db->query("UPDATE threads set category='" . $row["categoryid"] . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
                         
                         			refresh(0);
                     			}
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
		// If the user is requesting to edit the thread title...
		elseif (($_POST["editthread"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true))
		{
			// First make sure the user has permission to edit the specified post.
			$permission = $db->query("SELECT startuser FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");
			while ($p = $permission->fetch_assoc())
			{
				if ($p["startuser"] == $_SESSION["userid"] or $_SESSION["role"] == "Moderator" or $_SESSION["role"] == "Administrator")
				{
					$result = $db->query("UPDATE threads SET title='" . $db->real_escape_string($_POST["editthread"]) . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
			
					if (!$result)
					{
						message($lang["thread.PostRestoredError"]);
					}
			
					else
					{
						refresh(0);
					}
				}
                else
                {
					message($lang["thread.PostEditError"]);
				}
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

    $titleEdit = $db->query("SELECT startuser, starttime FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");
	while ($row = $titleEdit->fetch_assoc())
	{
		if ($row["startuser"] == $_SESSION["userid"] or $_SESSION["role"] == "Moderator" or $_SESSION["role"] == "Administrator")
		{
			echo '<form action="" method="post"><input type="text" id="editthread" name="editthread" autocomplete="off" onchange="form.submit();" value="' . htmlspecialchars($title) . '"></form>';
        }
        else
        {
            echo '<span class="threadtitle">' . htmlspecialchars($title) . '</span>';
        }
        $userinfo = $db->query("SELECT username, role, deleted FROM users WHERE userid='" . $row["startuser"] . "'");
        while ($u = $userinfo->fetch_assoc()) {
            if ($u["deleted"] == 1) {
                $username = $lang["user.Deleted"] . $u["userid"];
            }
			else {
                $username = $u["username"];
            }
            printf("<span class='threadinfo'>" .$lang["thread.Info"] . "</span>", $u["role"], genURL("user/" . htmlspecialchars($row["startuser"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $row['starttime']), relativeTime($row["starttime"]));
        }
        
    }

	if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))
	{
		echo '<div class="modtools">';

        	$categories = $db->query("SELECT * FROM categories");

        	if ($categories->num_rows or $categories) {
            		echo '<form action="" method="post"><select name="category">';
            		while($row = $categories->fetch_assoc())
			{
				echo '<option ';
                    		if ($category == $row["categoryid"]) echo "selected ";
				echo 'value="' . $row['categoryid'] . '">' . htmlspecialchars($row['categoryname']) . '</option>';
			}
			echo '</select> <button name="movethread" class="threadbutton" value="' . $q2 . '">'.$lang["thread.MoveThreadBtn"].'</button></form> ';	
        	}

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
	pagination("thread");

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
					echo '<div class="hiddenpost"><b><a href="' . genURL('user/' . $u["userid"]) . '/" id="' . $u["role"] . '">' . htmlspecialchars($u["username"]) . "</a></b> <span title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "' class='postdate'>" . relativeTime($row["timestamp"]) . '</span> ' . sprintf($lang["thread.HiddenBy"], (genURL('user/' . $row["deletedby"] . '/')), $h["role"], $h["username"]);
					if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator") or ($u["userid"] == $_SESSION["userid"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true))
					{
						echo '<form class="rpostc" action="" method="post"><button name="restore" value="' . $row["postid"] . '">'.$lang["post.RestoreHiddenBtn"].'</button></form>';
					}
					echo '</div>';
				}
			}
			
			else
			{
				if ($u["deleted"] == "1") {
                    			$username = $lang["user.Deleted"] . $u["userid"];
                    			$deletedClass = " deleteduser";
                		}
                		else {
                    			$username = $u["username"];
                    			$deletedClass = "";
                		}
				if ($u["avatar"] == "none") {
                    			$displayAvatar = "";
                		}
                		else {
                    			$displayAvatar = "<img class='avatar' src='" . genURL("avatars/" . $u["userid"] . "." . $u["avatar"] . "?t=" . $u["avataruploadtime"]) . "'>";
                		}
				echo '<div class="post' . $deletedClass . '"><div postcolor="' . $u["color"] . '" class="thread">';
				echo $displayAvatar;
				echo '<b><a href="' . genURL('user/' . $u["userid"]) . '/" id="' . $u["role"] . '">' . htmlspecialchars($username) . "</a></b>";
				if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator") or ($u["userid"] == $_SESSION["userid"]) && (!($_SESSION["role"] == "Suspended")) && ($_SESSION['signed_in'] == true))
				{
                    echo '<div>';
					echo '<form class="postc" action="" method="post"><button name="edit" value="' . $row["postid"] . '">'.$lang["post.EditBtn"].'</button></form>';
					echo '<form class="postc" action="" method="post"><button name="hide" value="' . $row["postid"] . '">'.$lang["post.HideBtn"].'</button></form>';
					echo '<form class="postc" action="" method="post"><button name="delete" value="' . $row["postid"] . '">'.$lang["post.DeleteBtn"].'</button></form>';
					echo '</div>';
				}
				
				if (isset($_POST["edit"]) && ($_POST["edit"] == $row["postid"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true) && ((($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) or ($u["userid"] == $_SESSION["userid"]) && ($_SESSION["role"] != "Suspended") && ($_SESSION['signed_in'] == true)))
				{
					echo '</div><div class="editbox" id="edit"><form method="post" action="" class="editbox">';
					BBCodeButtons(2);
					echo '<div class="forminput"><textarea name="saveedit" id="textbox2">' . ($row["content"]) . '</textarea><textarea style="display:none;" name="saveeditpostid">' . $row["postid"] . '</textarea></div>';
					echo '<div class="forminput"><input type="submit" class="buttonbig buttonYes" value="'.$lang["post.SaveEditBtn"].'"> <a class="buttonbig buttonNo" href="">'.$lang["post.DiscardEditBtn"].'</a></form></div></div>';
				}
				
				else
				{
                    echo '</div><div class="threadcontent">';
                    echo '<div class="infobar">';
                    echo "<span class='postdate' title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "'>" . relativeTime($row["timestamp"]) . "</span>";
                    if ($_SESSION['signed_in'] == true)
                    {
                        echo "<button class='buttoninput buttonquote' onclick='quotePost(" . $row["postid"] . ")'>" . $lang["thread.QuotePost"] . "</button>";
                    }
                    echo '</div>';
                    			if ((!$u["signature"]) or (!isset($u["signature"])) or ($u["signature"] == "")) {
                        			$signature = "";
                    			}
                    			else {
                        			$signature = '<hr class="sigline"><p class="signature">' . formatPost($u["signature"]) . '</p>';
                    			}
					echo formatPost($row["content"]) . $signature . '</div>';
				}
                		echo "</div>";
			}
		}
	}
		
    	// Draw page bar
	pagination("thread");
	
	if ($_SESSION["role"] == "Suspended")
	{
		message($lang["thread.SuspendCantPost"]);
	}
		
	elseif (($_SESSION['signed_in'] == true) && ($locked == 0) or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) && $locked == 1)
	{
        // Check if the user has a draft post in this thread.
        $draft = $db->query("SELECT content FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND thread='" . $db->real_escape_string($q2) . "'");

        if ($draft->num_rows > 0) {
            while ($d = $draft->fetch_assoc()) {
                $draftPost = $d["content"];
            }
        }

		echo '<form method="post" action="">';
		echo '<div class="forminput">'.$lang["thread.ContentTitle"].'</div>';
		BBCodeButtons(1);
		echo '<div class="forminput"><textarea name="content" id="textbox1">';
		if (isset($contentSave)) echo $contentSave;
        elseif (isset($draftPost)) echo $draftPost;
		echo '</textarea></div>';
		echo '<div class="forminput left"><input type="submit" class="buttonbig" name="postReply" value="'.$lang["thread.PostReplyBtn"].'">';
        echo '</div><div class="draftbuttons"><input type="submit" class="buttonbig" name="saveDraft" value="'.$lang["thread.PostSaveDraftBtn"].'">';
        if (isset($draftPost)) {
            echo '<input type="submit" class="buttonbig buttonNo" name="discardDraft" value="'.$lang["thread.PostDiscardDraftBtn"].'">';
        }
		echo '</div></form>';
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

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if ($_POST["edit"])
    {
        echo "<script>document.getElementById('edit').scrollIntoView();</script>";
    }
}
include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	while($row = $thread->fetch_assoc())
	{
		$action = $lang["action.Generic"] . ' <a href="' . genURL('/thread/' . $row["threadid"]) . '/">' . $row["title"] . '</a>';
		
		update_last_action($action);
	}
}

?>
