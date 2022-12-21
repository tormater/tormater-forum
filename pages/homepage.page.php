<?php
// homepage.page.php
// Initializes the home page.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>' . $lang["homepage.Title"] . '</h2>';

$result = $db->query("SELECT * FROM categories");

$threads = $db->query("SELECT * FROM threads ORDER BY lastposttime DESC LIMIT 5");

echo '<table><tr><th>' . $lang["homepage.Cats"] . '</th><th><center>' . $lang["homepage.CatThreads"] . '</center></th></tr>';
while($row = $result->fetch_assoc()) {
	$numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "'");
	$number = $numthreads->num_rows;
	echo '<tr><td><h3><a href="' . genURL("category/" . $row["categoryid"]) . '">' . htmlspecialchars($row["categoryname"]) . '</a></h3>';
	echo '<div>' . htmlspecialchars($row["categorydescription"]) . '</div></td>';
	echo '<td class="tdthreads"><div><center>' . $number . '</center></div></td></tr>';
}
echo '</table>';
echo '<table><tr><th>' . $lang["homepage.Threads"] . '</th><th><center>' . $lang["category.Posts"] . '</center></th><th>' . $lang["category.CreatedBy"] . '</th><th>' . $lang["category.LastPost"] . '</th></tr>';
					
while($row = $threads->fetch_assoc())
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

    echo '<b><a href="' . genURL('thread/' . $row['threadid']) . '">' . htmlspecialchars($row['title']) . "</a></b>";	


	echo '</td><td><center>' . $row['posts'] . '</center></td><td>';
					
	$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
					
	while ($u = $uinfo->fetch_assoc())
	{
		echo '<a href="' . genURL('user/' . $row['startuser']) . '" id="' . $u["role"] . '">' . $u['username'] . '</a>';
	}
					
	echo "<div class='tddate' title='" . date('m-d-Y h:i:s A', $row['starttime']) . "'>" . relativeTime($row["starttime"]) . "</div>";
					
	echo '</td><td>';
					
	$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["lastpostuser"] . "'");
					
	while ($u = $uinfo->fetch_assoc())
	{
		echo '<a href="' . genURL('user/' . $row['lastpostuser']) . '" id="' . $u["role"] . '">' . $u['username'] . '</a>';
	}
					
	echo '<div class="tddate" title="' . date('m-d-Y h:i:s A', $row['lastposttime']) . '">' . relativeTime($row["lastposttime"]) . '</div></td></tr>';
}

echo "</table>";


// Include "latest threads"

include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("action.Homepage");
}

?>
