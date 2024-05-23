<?php
// category.page.php
// Shows all the threads inside a category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Get the category information.
$category = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($q2) . "'");

// Find out what page we're on.
if (isset($q3) && is_numeric($q3)) {
	$currentPage = $q3;
}
else
{
	// If no valid page is specified then always assume we're on the first page.
	$currentPage = 1;
}

while ($row = $category->fetch_assoc()) {
	// Category information.
    $categoryID = $row['categoryid'];
	$categoryName = $row['categoryname'];
	$categoryDescription = $row['categorydescription'];
}

include "header.php";

$threads = $db->query("SELECT 1 FROM threads WHERE category='" . $db->real_escape_string($q2) . "'");

// Important details for sorting the threads into pages.
$numThreads = $threads->num_rows;
$pages = ceil($numThreads / $config["threadsPerPage"]);

// Calculate the offset for the threads query.
$offset = (($currentPage * $config["threadsPerPage"]) - $config["threadsPerPage"]);

$posts = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' ORDER BY lastposttime LIMIT " . $config["postsPerPage"] . " OFFSET " . $offset . "");

if(!$category)
{
	echo $lang["error.CategoryMisc"];
}

else
{
	if($category->num_rows == 0)
	{
		echo $lang["error.CategoryNotFound"];
	}
	
	else
	{
		echo '<span class="categorytitle">' . $lang["category.ThreadsIn"] . htmlspecialchars($categoryName) . '</span>';
        echo '<span class="categorydesc">' . formatPost($categoryDescription) .'</span>';

        if ($_SESSION["signed_in"] != true) {
            $result = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' AND draft='0' UNION SELECT * FROM threads WHERE pinned='1' AND draft='0' ORDER BY pinned DESC, sticky DESC, lastposttime DESC LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");
        }
        elseif (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) {
            $result = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' UNION SELECT * FROM threads WHERE pinned='1' ORDER BY pinned DESC, sticky DESC, lastposttime DESC LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");
        }
        else {
            $result = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' AND draft='0' OR (draft='1' AND startuser='" . $_SESSION["userid"] . "') UNION SELECT * FROM threads WHERE pinned='1' AND draft='0' OR (draft='1' AND startuser='" . $_SESSION["userid"] . "') ORDER BY pinned DESC, sticky DESC, lastposttime DESC LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");
        }
		
		if(!$result)
		{
			echo $lang["error.CategoryThreadMisc"];
		}
		
		else
		{
			if($result->num_rows == 0)
			{
				message($lang["error.CategoryEmpty"]);
			}
			
			else
			{

			// Draw page bar
			pagination("category");

				echo '<table><tr><th>' . $lang["category.Thread"] . '</th><th><center>' . $lang["category.Posts"] . '</center></th><th>' . $lang["category.LastPost"] . '</th></tr>';
					
				while($row = $result->fetch_assoc())
				{				
					echo '<tr><td class="leftpart">';

					if ($row["locked"] == 1)
					{
						echo '<span class="locked">' . $lang["label.Locked"] . '</span>';
					}
	
					if ($row["sticky"] == 1)
					{
						echo '<span class="sticky">' . $lang["label.Sticky"] . '</span>';
					}

                    if ($row["draft"] == 1)
                    {
                        echo '<span class="draft">' . $lang["label.Draft"] . '</span>';
                    }

                    if ($row["pinned"] == 1)
                    {
                        echo '<span class="pinned">' . $lang["label.Pinned"] . '</span>';
                    }

                    			echo '<b><a href="' . genURL('thread/' . $row['threadid']) . '">' . htmlspecialchars($row['title']) . "</a></b>";	
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						if ($u["deleted"] == 1) {
                            				$username = $lang["user.Deleted"] . $u["userid"];
                        			}
						else {
                            				$username = $u["username"];
                        			}
                        echo "<div class='tdinfo'>";
						printf("<span>" .$lang["thread.Info"] . "</span>", $u["role"], genURL("user/" . htmlspecialchars($row["startuser"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $row['starttime']), relativeTime($row["starttime"]));
                        echo "</div>";
					}
                    
					
                    
                    echo '</td><td class="tdposts"><center>' . $row['posts'] . '</center></td><td>';
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $db->real_escape_string($row["lastpostuser"]) . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						if ($u["deleted"] == 1) {
                            				$username = $lang["user.Deleted"] . $u["userid"];
                        			}
                        			else {
                            				$username = $u["username"];
                        			}
						echo '<a href="' . genURL('user/' . $row['lastpostuser']) . '" id="' . $u["role"] . '">' . htmlspecialchars($username) . '</a>';
					}
					
					echo '<div class="tdinfo" title="' . date('m-d-Y h:i:s A', $row['lastposttime']) . '">' . relativeTime($row["lastposttime"]) . '</div></td></tr>';
				}	
				echo "</table>";
				
    // Draw page bar
	pagination("category");
    
			}
		}
	}
}

include "footer.php";

?>
