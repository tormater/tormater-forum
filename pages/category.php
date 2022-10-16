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
	echo 'The category could not be displayed, please try again later.';
}

else
{
	if($category->num_rows == 0)
	{
		echo 'This category does not exist.';
	}
	
	else
	{
		echo '<h2>Threads in ' . htmlspecialchars($categoryName) . '</h2>';
	
		$result = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' ORDER BY sticky DESC, lastposttime DESC LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");
		
		
		if(!$result)
		{
			echo 'The threads could not be displayed, please try again later.';
		}
		
		else
		{
			if($result->num_rows == 0)
			{
				echo 'There are no threads in this category yet.';
			}
			
			else
			{
				echo "<div class='paginationleft'>";
		
				if ($currentPage == 1)
				{
					echo "<font color=white>First page</font> <font color=white>Previous page</font>";
				}
				else
				{
					echo "<a href='/category/" . $q2 . "/1/'>First page</a> <a href='/category/" . $q2 . "/" . ($currentPage - 1) . "/'>Previous page</a>";
				}
		
				echo "</div><div class='paginationright'>";
		
				if ($currentPage == $pages)
				{
					echo "<font color=white>Next page</font> <font color=white>Last page</font>";
				}
				else
				{
					echo "<a href='/category/" . $q2 . "/" . ($currentPage + 1) . "/'>Next page</a> <a href='/category/" . $q2 . "/" . $pages ."/'>Last page</a>";
				}
		
				echo "</div></br>";

				echo '<table><tr><th>Thread</th><th>Posts</th><th>Created by</th><th>Last post</th></tr>';
					
				while($row = $result->fetch_assoc())
				{				
					echo '<tr><td class="leftpart"><b><a href="/thread/' . $row['threadid'] . '/">' . htmlspecialchars($row['title']) . "</a></b>";
					if ($row["locked"] == 1 && $row["sticky"] == 1)
					{
						echo ' <small><font class="sticky">Sticky</font> <font class="locked">Locked</font></small></br></br>';
					}
	
					elseif ($row["locked"] == 1)
					{
						echo ' <small><font class="locked">Locked</font></small></br></br>';
					}
	
					elseif ($row["sticky"] == 1)
					{
						echo ' <small><font class="sticky">Sticky</font></small></br></br>';
					}

					echo '</td><td><center>' . $row['posts'] . '</center></td><td>';
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						echo '<a href="/user/' . $row['startuser'] . '/" id="' . $u["role"] . '">' . $u['username'] . '</a>';
					}
					
					echo "</br><small><a title='" . date('m-d-Y h:i:s A', $row['starttime']) . "'>" . relativeTime($row["starttime"]) . "</a></small>";
					
					echo '</td><td>';
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["lastpostuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						echo '<a href="/user/' . $row['lastpostuser'] . '/" id="' . $u["role"] . '">' . $u['username'] . '</a>';
					}
					
					echo '</br><small><a title="' . date('m-d-Y h:i:s A', $row['lastposttime']) . '">' . relativeTime($row["lastposttime"]) . '</a></small></td></tr>';
				}	
				echo "</table></br>";
				
				echo "<div class='paginationleft'>";
		
				if ($currentPage == 1)
				{
					echo "<font color=white>First page</font> <font color=white>Previous page</font>";
				}
				else
				{
					echo "<a href='/category/" . $q2 . "/1/'>First page</a> <a href='/category/" . $q2 . "/" . ($currentPage - 1) . "/'>Previous page</a>";
				}
		
				echo "</div><div class='paginationright'>";
		
				if ($currentPage == $pages)
				{
					echo "<font color=white>Next page</font> <font color=white>Last page</font>";
				}
				else
				{
					echo "<a href='/category/" . $q2 . "/" . ($currentPage + 1) . "/'>Next page</a> <a href='/category/" . $q2 . "/" . $pages ."/'>Last page</a>";
				}
		
				echo "</div>";
			}
		}
	}
}

include "footer.php";

?>
