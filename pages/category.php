<?php
// category/php
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

$posts = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' ORDER BY timestamp LIMIT " . $config["postsPerPage"] . " OFFSET " . $offset . "");

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
		echo '<h2>' . $lang["category.ThreadsIn"] . htmlspecialchars($categoryName) . '</h2>';
	
		$result = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' ORDER BY sticky DESC, lastposttime DESC LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");
		
		
		if(!$result)
		{
			echo $lang["error.CategoryThreadMisc"];
		}
		
		else
		{
			if($result->num_rows == 0)
			{
				echo $lang["error.CategoryEmpty"];
			}
			
			else
			{

    // Draw page bar
	pagination(category);

				echo '<table><tr><th>' . $lang["category.Thread"] . '</th><th>' . $lang["category.Posts"] . '</th><th>' . $lang["category.CreatedBy"] . '</th><th>' . $lang["category.LastPost"] . '</th></tr>';
					
				while($row = $result->fetch_assoc())
				{				
					echo '<tr><td class="leftpart"><b><a href="/thread/' . $row['threadid'] . '/">' . htmlspecialchars($row['title']) . "</a></b>";	
					if ($row["locked"] == 1)
					{
						echo '<span class="locked">' . $lang["label.Locked"] . '</span>';
					}
	
					if ($row["sticky"] == 1)
					{
						echo '<span class="sticky">' . $lang["label.Sticky"] . '</span>';
					}

					echo '</td><td><center>' . $row['posts'] . '</center></td><td>';
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						echo '<a href="/user/' . $row['startuser'] . '/" id="' . $u["role"] . '">' . $u['username'] . '</a>';
					}
					
					echo "<div class='tddate' title='" . date('m-d-Y h:i:s A', $row['starttime']) . "'>" . relativeTime($row["starttime"]) . "</div>";
					
					echo '</td><td>';
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["lastpostuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						echo '<a href="/user/' . $row['lastpostuser'] . '/" id="' . $u["role"] . '">' . $u['username'] . '</a>';
					}
					
					echo '<div class="tddate" title="' . date('m-d-Y h:i:s A', $row['lastposttime']) . '">' . relativeTime($row["lastposttime"]) . '</div></td></tr>';
				}	
				echo "</table>";
				
    // Draw page bar
	pagination(category);
    
			}
		}
	}
}

include "footer.php";

?>
