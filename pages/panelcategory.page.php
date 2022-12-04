<?php
// panelcategory.page.php
// Creates a new category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Check how many categories there are. If the limit has been reached show a message.
$catcheck = $db->query("SELECT 1 FROM categories");
if (($catcheck->num_rows) >= $config["maxCats"]) {
	message($lang["panel.NoNewCategoriesCreate"]);
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

		echo '<h2>'.$lang["panel.Categories"].'</h2>';
		echo '<h3>'.$lang["panel.EditCategory"].'</h3>';

		if ($_POST["edit_edit_cat_name"] or $_POST["edit_cat_description"])
		{
			$id = $_POST["cat_num"];
			$name = $_POST["edit_edit_cat_name"];
			$description = $_POST["edit_cat_description"];
			
			if (!$_POST["edit_edit_cat_name"])
			{
				message($lang["panel.CategoryNameBlank"]);
			}
			elseif (!$_POST["edit_cat_description"])
			{
				message($lang["panel.CategoryDescBlank"]);
			}
			elseif (isset($_POST["edit_edit_cat_name"]) && isset($_POST["edit_cat_description"]))
			{
				$result = $db->query("UPDATE categories SET categoryname='" . $name . "',categorydescription='" . $description . "' WHERE categoryid='" . $db->real_escape_string($id) . "'");
				if (!$result)
				{
					message($lang["panel.CantUpdateCategory"]);
				}
				
				else
				{
					message($lang["panel.SuccessUpdateCategory"]);
					refresh(1);
				}
			}
		}
		echo'
		<div class="formcontainer"><form method="post" action="">
		<input style="display:none;" type="text" name="cat_num" value="' . htmlspecialchars($id) . '">
		<div class="forminput"><label>'.$lang["panel.InputCategoryTitle"].'</label><input type="text" name="edit_edit_cat_name" value="' . htmlspecialchars($name) . '"></div>
		<div class="forminput"><label>'.$lang["panel.InputCategoryDesc"].'</label></div>
		<div class="forminput"><textarea name="edit_cat_description">' . htmlspecialchars($description) . '</textarea></div>
		<div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["panel.EditCategoryBtn"].'"> <a class="buttonbig" href="">'.$lang["panel.EditCategoryDisBtn"].'</a></div>
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

		echo '<h2>'.$lang["panel.Categories"].'</h2>';
		
		if ($_POST["deleteit"])
		{
			$id = $_POST["deleteit"];
			$result = $db->query("DELETE FROM categories WHERE categoryid='" . $id . "'");
			
			if (!$result) {
				message($lang["CantDeleteCategory"]);
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
					message($lang["CantDeleteCategory"]);
				}
				else
				{
					$result = $db->query("DELETE FROM posts WHERE thread='" . $threadid . "'");
					if (!$result)
					{
						message($lang["CantDeleteCategory"]);
					}
					else
					{
						$result = $db->query("DELETE FROM threads WHERE threadid='" . $threadid . "'");
						if (!$result)
						{
							message($lang["CantDeleteCategory"]);
						}
						else
						{
							message($lang["panel.SuccessDeleteCategory"]);
							refresh(1);
						}
					}
				}
			}
		}

		echo '<h3>'.$lang["panel.DeleteCategory"].'('.htmlspecialchars($name).')</h3>';
		echo'
		<div class="formcontainer"><form method="post" action="">
		<input style="display:none;" type="text" name="deleteit" value="' . htmlspecialchars($id) . '">
		<div class="forminput">'.$lang["panel.CategoryDataWillGone"].'</div>
		<div class="forminput">'.$lang["panel.CategoryDeleteLastCheck"].'</div><br>
		<div class="forminput"><button type="submit" class="buttonbig">'.$lang["panel.DeleteCategoryBtn"].'</button> <a class="buttonbig" href="">'.$lang["panel.DeleteCategoryBackBtn"].'</a></div>
		</form></div>';

		include "footer.php";
		exit;
	}
	
	if ($_POST["cat_name"])
	{
		// Ensure the name and description aren't empty.
		if (!$_POST["cat_name"])
		{
			message($lang["panel.CategoryNameBlank"]);
		}
		
		elseif (!$_POST["cat_description"])
		{
			message($lang["panel.CategoryDescBlank"]);
		}
		
		else {
		$result = $db->query("INSERT INTO categories (categoryname, categorydescription) VALUES ('" . $db->real_escape_string($_POST['cat_name']) . "', '" . $db->real_escape_string($_POST['cat_description']) . "')");
		
			if(!$result)
			{
				message($lang["error.SomethingWentWrong"]);
			}
		
			else
			{
				message($lang["panel.SuccessAddedCategory"]);
			}
		}
	}
}

	$result = $db->query("SELECT * FROM categories");
	echo '<h2>'.$lang["panel.Categories"].'</h2>';
    while($row = $result->fetch_assoc()) {
    	$numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "'");
    	$number = $numthreads->num_rows;
    	echo '<div class="category"><tr>';
    		echo '<td class="leftpart">';
    		echo '<h3><span>' . htmlspecialchars($row["categoryname"]) . ' (#' . $row["categoryid"] . ')</span></h3>';
			echo '<div>' . htmlspecialchars($row["categorydescription"]) . '</div>';
    		echo '<div><div style="float:right">';
			echo '<form style="display:inline-block;" method="post" action=""><button name="edit" value="' . $row["categoryid"] . '">'.$lang["panel.CategoryEditBtn"].'</button></form> 
				<form style="display:inline-block;" method="post" action=""><button name="delete" value="' . $row["categoryid"] . '">'.$lang["panel.CategoryDeleteBtn"].'</button></form>';
			echo '</div>';
			echo $lang["homepage.CatThreads"] . $number . '</div>';
    	echo '</tr></div>';
    }
	echo '
    <h3>'.$lang["panel.CreateACategory"].'</h3>
    <div class="formcontainer"><form method="post" action="">
		<div class="forminput"><label>'.$lang["panel.InputCategoryTitle"].'</label><input type="text" name="cat_name"></div>
		<div class="forminput"><label>'.$lang["panel.InputCategoryDesc"].'</label></div>
		<div class="forminput"><textarea name="cat_description"></textarea></div>
		<div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["panel.CreateCategoryBtn"].'"></div>
		</form></div>';

?>