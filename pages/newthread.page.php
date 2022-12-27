<?php
// newthread.page.php
// Creates a new thread.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

if($_SESSION['signed_in'] == false)
{
	message(sprintf($lang["newthread.LoginToCreate"], genURL("login")));
	include "footer.php";
	exit;
}

if ($_SESSION["role"] == "Suspended")
{
	message($lang["newthread.SuspendCantCreate"]);
}
else
{
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$cat = $db->query("SELECT 1 FROM categories WHERE categoryid='" . $db->real_escape_string($_POST["category"]) . "'");
		
		if (mb_strlen($_POST["title"]) < 1)
		{
			message($lang["newthread.TitleEmpty"]);
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		elseif (mb_strlen($_POST["title"]) > $config["maxCharsPerTitle"])
		{
            message(sprintf($lang["newthread.TitleBig1"], $config["maxCharsPerTitle"]));
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		elseif (mb_strlen($_POST["content"]) < 1)
		{
			message($lang["newthread.PostEmpty"]);
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
			
		elseif (mb_strlen($_POST["content"]) > $config["maxCharsPerPost"])
		{
            message(sprintf($lang["newthread.PostBig1"], $config["maxCharsPerPost"]));
			$catSave = $_POST["category"];
			$titleSave = $_POST["title"];
			$contentSave = $_POST["content"];
		}
		
		elseif ((!$cat) or ($cat->num_rows < 1))
		{
			message($lang["newthread.InvalidCategory"]);
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
                message(sprintf($lang["newthread.PostSoon1"], $config["postDelay"]));
				$catSave = $_POST["category"];
				$titleSave = $_POST["title"];
				$contentSave = $_POST["content"];
			}
			
			else
			{
		
				$beginwork = $db->query("BEGIN WORK");
		
				if(!$beginwork)
				{
					message($lang["newthread.CreateError"]);
				}
		
				else
				{
					$justnow = time();
					$userid = $_SESSION["userid"];
		
					$threadresult = $db->query("INSERT INTO threads (title, sticky, locked, posts, startuser, starttime, lastpostuser, lastposttime, category) VALUES ('" . $db->real_escape_string($_POST["title"]) . "', '0', '0', '1', '$userid', '$justnow', '$userid', '$justnow', '" . $db->real_escape_string($_POST["category"]) . "')");
			
					if (!$threadresult)
					{
						echo $lang["newthread.InsertThreadError"];
						$db->query("ROLLBACK");
					}
			
					else
					{
						$threadid = $db->insert_id;
					 	
						$result = $db->query("INSERT INTO posts (thread, user, timestamp, content) VALUES ('$threadid', '$userid', '$justnow', '" . $db->real_escape_string($_POST["content"]) . "')");
				
						if(!$result)
						{
							echo $lang["newthread.InsertPostError"];
							$db->query("ROLLBACK");
						}
						else
						{
							$db->query("COMMIT");
					
							message($lang["newthread.SuccessCreate1"] . ' <a href="' . genURL('thread/' . $threadid) . '/">' . $lang["newthread.SuccessCreate2"] . '</a>');
							include("footer.php");
							header("Refresh:1; url=" . genURL("thread/" . $threadid));
							exit;
						}
					}
				}
			}
		}
	}
	
	echo '<h2>'.$lang["newthread.Header"].'</h2>';
		
	$result = $db->query("SELECT * FROM categories");
		
	if(!$result)
	{
		message($lang["newthread.DataError"]);
	}
		
	else
	{
		if(!$result->num_rows)
		{
			if($_SESSION['role'] == "Administrator")
			{
				message($lang["newthread.NoCategoryAdmin"]);
			}
				
			else
			{
				message($lang["newthread.NoCategoryUser"]);
			}
		}
			
		else
		{
		
			echo '<form method="post" action="">';
			echo '<div class="forminput"><label>' . $lang["newthread.Title"] . '</label><input type="text" maxlength="' . $config["maxCharsPerTitle"] . '" name="title"';
			if (isset($titleSave)) echo "value='" . $titleSave . "'";
			echo '></div><div class="forminput"><label>' . $lang["newthread.Category"] . '</label>'; 

			echo '<select name="category">';
				while($row = $result->fetch_assoc())
				{
					echo '<option ';
					if (isset($catSave) && $catSave == $row["categoryid"]) echo "selected ";
                    elseif (isset($q2) && $q2 == $row["categoryid"]) echo "selected ";
					echo 'value="' . $row['categoryid'] . '">' . htmlspecialchars($row['categoryname']) . '</option>';
				}
			echo '</select></div>';	
					
			echo '<div class="forminput">' . $lang["newthread.Content"] . '</div>';
			BBCodeButtons();
			echo '<div class="forminput"><textarea name="content" id="textbox">';
			if (isset($contentSave)) echo $contentSave;
			echo '</textarea></div>';
			echo '<div class="forminput"><input type="submit" class="buttonbig" value="' . $lang["newthread.CreateBtn"] . '"></form></div>';
		}
	}
}

include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("action.CreateAThread");
}

?>
