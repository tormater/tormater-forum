<?php
// homepage.php
// Initializes the home page.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo '<h2>' . $lang["homepage.Cats"] . '</h2>';

$result = $db->query("SELECT * FROM categories");

while($row = $result->fetch_assoc()) {
	$numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "'");
	$number = $numthreads->num_rows;
	echo '<div class="category"><tr>';
		echo '<td class="leftpart">';
			echo '<h3><a href="/category/' . $row["categoryid"] . '/">' . htmlspecialchars($row["categoryname"]) . '</a></h3>';
		echo '<div>' . $row["categorydescription"] . '</div>';
		echo '<div>' . $lang["homepage.CatThreads"] . $number . '</div>';
	echo '</tr></div>';
}

include 'footer.php';

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("action.Homepage");
}

?>
