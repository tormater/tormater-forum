<?php
// panelcategory.php
// Creates a new category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Check how many categories there are. If the limit has been reached show a message.
$catcheck = $db->query("SELECT 1 FROM categories");
if (($catcheck->num_rows) >= $config["maxCats"]) {
	message("Sorry, no more new categories can be created at this time.");
	include "footer.php";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_POST["edit"] or $_POST["edit_edit_cat_name"] or $_POST["edit_cat_description"])
	{
		$id = $_POST["edit"];
		$category = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($id) . "'");

		while($row = $category->fetch_assoc()) {
			// category information.
			$name = $row['categoryname'];
			$description = $row['categorydescription'];
			$id = $row['categoryid'];
		}

		echo '<h2>Categories</h2>';
		echo '<h3>Edit category</h3>';

		if ($_POST["edit_edit_cat_name"] or $_POST["edit_cat_description"])
		{
			$id = $_POST["cat_num"];
			$name = $_POST["edit_edit_cat_name"];
			$description = $_POST["edit_cat_description"];
			
			if (!$_POST["edit_edit_cat_name"])
			{
				message("The category name cannot be blank.");
			}
			elseif (!$_POST["edit_cat_description"])
			{
				message("The category description cannot be blank.");
			}
			elseif (isset($_POST["edit_edit_cat_name"]) && isset($_POST["edit_cat_description"]))
			{
				$result = $db->query("UPDATE categories SET categoryname='" . $name . "',categorydescription='" . $description . "' WHERE categoryid='" . $db->real_escape_string($id) . "'");
				if (!$result)
				{
					message("Sorry, can't update category.");
				}
				
				else
				{
					message("Successfully update.");
					refresh(1);
				}
			}
		}
		echo'
		<div class="formcontainer"><form method="post" action="">
		<input style="display:none;" type="text" name="cat_num" value="' . htmlspecialchars($id) . '">
		<div class="forminput"><label>Title</label><input type="text" name="edit_edit_cat_name" value="' . htmlspecialchars($name) . '"></div>
		<div class="forminput"><label>Description</label></div>
		<div class="forminput"><textarea name="edit_cat_description">' . htmlspecialchars($description) . '</textarea></div>
		<div class="forminput"><input type="submit" class="buttonbig" value="Edit category"> <a class="buttonbig" href="">Discard edit</a></div>
		</form></div>';

			include "footer.php";
			exit;
	}

	if ($_POST["delete"] or $_POST["deleteit"])
	{
		$id = $_POST["delete"];
		$category = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($id) . "'");

		while($row = $category->fetch_assoc()) {
			// category information.
			$name = $row['categoryname'];
			$id = $row['categoryid'];
		}

		echo '<h2>Categories</h2>';
		
		if ($_POST["deleteit"])
		{
			$id = $_POST["deleteit"];
			$result = $db->query("DELETE FROM categories WHERE categoryid='" . $id . "'");
			
			if (!$result) {
				message("Sorry, can't delete category.");
			}
			else
			{
				$threads = $db->query("SELECT * FROM threads WHERE category='" . $id . "'");
				while($row = $threads->fetch_assoc()) {
					// thread information.
					$threadid = $row['threadid'];
				}
				if (!$threads)
				{
					message("Sorry, can't delete category.");
				}
				else
				{
					$result = $db->query("DELETE FROM posts WHERE thread='" . $threadid . "'");
					if (!$result)
					{
						message("Sorry, can't delete category.");
					}
					else
					{
						$result = $db->query("DELETE FROM threads WHERE threadid='" . $threadid . "'");
						if (!$result)
						{
							message("Sorry, can't delete category.");
						}
						else
						{
							message("Successfully delete.");
							refresh(1);
						}
					}
				}
			}
		}

		echo '<h3>Delete category "'.$name.'"</h3>';
		echo'
		<div class="formcontainer"><form method="post" action="">
		<input style="display:none;" type="text" name="deleteit" value="' . htmlspecialchars($id) . '">
		<div class="forminput">All threads and posts in this category will be gone forever...</div>
		<div class="forminput">Are you sure?</div><br>
		<div class="forminput"><button type="submit" class="buttonbig">Delete it</button> <a class="buttonbig" href="">Go back</a></div>
		</form></div>';

		include "footer.php";
		exit;
	}
	
	if ($_POST["cat_name"])
	{
		// Ensure the name and description aren't empty.
		if (!$_POST["cat_name"])
		{
			message("The category name cannot be blank.");
		}
		
		elseif (!$_POST["cat_description"])
		{
			message("The category description cannot be blank.");
		}
		
		else {
		$result = $db->query("INSERT INTO categories (categoryname, categorydescription) VALUES ('" . $db->real_escape_string($_POST['cat_name']) . "', '" . $db->real_escape_string($_POST['cat_description']) . "')");
		
			if(!$result)
			{
				message("Something went wrong.");
			}
		
			else
			{
				message("New category successfully added.");
			}
		}
	}
}

	$result = $db->query("SELECT * FROM categories");
	echo '<h2>Categories</h2>';
    while($row = $result->fetch_assoc()) {
    	$numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "'");
    	$number = $numthreads->num_rows;
    	echo '<div class="category"><tr>';
    		echo '<td class="leftpart">';
    		echo '<h3><span>' . htmlspecialchars($row["categoryname"]) . ' (#' . $row["categoryid"] . ')</span></h3>';
			echo '<div>' . $row["categorydescription"] . '</div>';
    		echo '<div><div style="float:right">';
			echo '<form style="display:inline-block;" method="post" action=""><button name="edit" value="' . $row["categoryid"] . '">Edit</button></form> 
				<form style="display:inline-block;" method="post" action=""><button name="delete" value="' . $row["categoryid"] . '">Delete</button></form>';
			echo '</div>';
			echo $lang["homepage.CatThreads"] . $number . '</div>';
    	echo '</tr></div>';
    }
	echo "
    <h3>Create a category</h3>
    <div class='formcontainer'><form method='post' action=''>
		<div class='forminput'><label>Title</label><input type='text' name='cat_name'></div>
		<div class='forminput'><label>Description</label></div>
		<div class='forminput'><textarea name='cat_description'></textarea></div>
		<div class='forminput'><input type='submit' class='buttonbig' value='Create category'></div>
		</form></div>";

?>