<?php
// newthread.php
// Creates a new thread.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

if($_SESSION['signed_in'] == false)
{
	message('Sorry, you have to be <a href="/login/">logged in</a> to create a thread.');
	include "footer.php";
	exit;
}

else
{
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$cat = $db->query("SELECT 1 FROM categories WHERE categoryid='" . $db->real_escape_string($_POST["category"]) . "'");
		
		if (strlen($_POST["title"]) < 1)
		{
			message("Your title cannot be blank.");
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		elseif (strlen($_POST["title"]) > $config["maxCharsPerTitle"])
		{
			message("Your title was too long. The maximum number of characters a title may contain is currently set to " . $config["maxCharsPerTitle"] . ".");
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		elseif (strlen($_POST["content"]) < 1)
		{
			message("Your post cannot be blank.");
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
			
		elseif (strlen($_POST["content"]) > $config["maxCharsPerPost"])
		{
			message("Your post was too long. The maximum number of characters a post may contain is currently set to " . $config["maxCharsPerPost"] . ".");
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		elseif ((!$cat) or ($cat->num_rows < 1))
		{
			message("Invalid category selection. Please select a category that actually exists.");
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		else
		{
			// First check and see if the user has made a post too recently according to the post delay.
			$delaycheck = $db->query("SELECT 1 FROM posts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - $config["postDelay"]) . "'");
			
			if ($delaycheck->num_rows > 0)
			{
				message("You tried to post too soon after a previous post. The post delay is currently " . $config["postDelay"] . " seconds between posts.");
				$catSave = $_POST["category"];
				$titleSave = $_POST["title"];
				$contentSave = $_POST["content"];
			}
			
			else
			{
		
				$beginwork = $db->query("BEGIN WORK");
		
				if(!$beginwork)
				{
					message("An error occured while creating your thread. Please try again later.");
				}
		
				else
				{
					$justnow = time();
					$userid = $_SESSION["userid"];
		
					$threadresult = $db->query("INSERT INTO threads (title, sticky, locked, posts, startuser, starttime, lastpostuser, lastposttime, category) VALUES ('" . $db->real_escape_string($_POST["title"]) . "', '0', '0', '1', '$userid', '$justnow', '$userid', '$justnow', '" . $db->real_escape_string($_POST["category"]) . "')");
			
					if (!$threadresult)
					{
						echo 'An error occured while inserting your thread. Please try again later.';
						$db->query("ROLLBACK");
					}
			
					else
					{
						$threadid = $db->insert_id;
					 	
						$result = $db->query("INSERT INTO posts (thread, user, timestamp, content) VALUES ('$threadid', '$userid', '$justnow', '" . $db->real_escape_string($_POST["content"]) . "')");
				
						if(!$result)
						{
							echo 'An error occured while inserting your post. Please try again later.';
							$db->query("ROLLBACK");
						}
						else
						{
							$db->query("COMMIT");
					
							message('You have successfully created <a href="/thread/'. $threadid . '/">your new thread</a>.');
							include("footer.php");
							exit;
						}
					}
				}
			}
		}
	}
	
	echo '<h2>Create a thread</h2>';
		
	$result = $db->query("SELECT * FROM categories");
		
	if(!$result)
	{
		message('Error while selecting from database. Please try again later.');
	}
		
	else
	{
		if(!$result->num_rows)
		{
			if($_SESSION['role'] == "Administrator")
			{
				message('You have not created categories yet.');
			}
				
			else
			{
				message('Before you can post a topic, you must wait for an admin to create some categories.');
			}
		}
			
		else
		{
		
			echo '<form method="post" action=""><label>Title: </label><input type="text" name="title"';
			if (isset($titleSave)) echo "value='" . $titleSave . "'";
			echo '></br></br><label>Category: </label>'; 

			echo '<select name="category">';
				while($row = $result->fetch_assoc())
				{
					echo '<option ';
					if (isset($catSave) && $catSave == $row["categoryid"]) echo "selected ";
                    elseif (isset($q2) && $q2 == $row["categoryid"]) echo "selected ";
					echo 'value="' . $row['categoryid'] . '">' . $row['categoryname'] . '</option>';
				}
			echo '</select></br></br>';	
					
			echo 'Content:</br><textarea name="content" />';
			if (isset($contentSave)) echo $contentSave;
			echo '</textarea></br><input type="submit" value="Create thread"></form>';
		}
	}
}

include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("Creating a thread");
}

?>
