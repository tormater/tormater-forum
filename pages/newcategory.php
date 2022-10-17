<?php
// newcategory.php
// Creates a new category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

// Disallow non-admins from using this page.
if (!($_SESSION["role"] == "Administrator")) {
	message("This page is unavailable to non-admins.");
	include "footer.php";
	exit;
}

// Check how many categories there are. If the limit has been reached show a message.
$catcheck = $db->query("SELECT 1 FROM categories");
if (($catcheck->num_rows) >= $config["maxCats"]) {
	message("Sorry, no more new categories can be created at this time.");
	include "footer.php";
	exit;
}

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
	echo '<h2>Create a category</h2>';
	echo "<form method='post' action=''>
 		Category name: <input type='text' name='cat_name'></br>
 		Category description:</br>
 		<textarea name='cat_description'></textarea>
 		</br><input type='submit' class='buttonbig' value='Add category'>
 	</form>";
}

else
{
    // Ensure the name and description aren't empty.
    if (!$_POST["cat_name"]) {
    	message("The category name cannot be blank.");
    }
    
    elseif (!$_POST["cat_description"]) {
    	message("The category description cannot be blank.");
    }
    
    else {
    $result = $db->query("INSERT INTO categories (categoryname, categorydescription) VALUES ('" . $db->real_escape_string($_POST['cat_name']) . "', '" . $db->real_escape_string($_POST['cat_description']) . "')");
    
    	if(!$result)
    	{
        	echo "Something went wrong.";
    	}
    
    	else
    	{
        	echo "New category successfully added. Return to the <a href='/'>main page</a>?";
    	}
    }
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("Creating a category");
}

?>
