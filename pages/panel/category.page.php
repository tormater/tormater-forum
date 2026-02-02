<?php
// panelcategory.page.php
// Creates a new category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // Move a category up in order.
    if (isset($_POST["moveup"])) {
        // Only proceed if the requested category id is valid.
        if (is_numeric($_POST["moveup"])) {
            // Get the category.
            $category = $db->query("SELECT * FROM `categories` WHERE `categoryid`='" . $db->real_escape_string($_POST["moveup"]) . "'");

            // Only proceed if the category exists.
            if ($category && $category->num_rows > 0) {
                // Get any category information we need.
                while ($row = $category->fetch_assoc()) {
                    $id = $row["categoryid"];
                    $order = $row["order"];
                }

                // Only proceed if the category isn't the top one already.
                if ($order > 0) {
                    // Get the id of the category ordered above this one.
                    $above = $db->query("SELECT * FROM `categories` WHERE `order`='" . $db->real_escape_string(($order - 1)) . "'");

                    if ($above && $above->num_rows > 0) {

                        while ($row = $above->fetch_assoc()) {
                            $aboveid = $row["categoryid"];
                            $aboveorder = $row["order"];
                        }

                        // Change the order of this category.
                        $db->query("UPDATE `categories` SET `order`='" . $db->real_escape_string($aboveorder) . "' WHERE `categoryid`='" . $db->real_escape_string($id) . "'");

                        // Now change the order of the category formerly above this one.
                        $db->query("UPDATE `categories` SET `order`='" . $db->real_escape_string($order) . "' WHERE `categoryid`='" . $db->real_escape_string($aboveid) . "'");
                    }
                }
            }
        }
    }
    // Move a category down in order.
    if (isset($_POST["movedown"])) {
        // Only proceed if the requested category id is valid.
        if (is_numeric($_POST["movedown"])) {
            // Get the category.
            $category = $db->query("SELECT * FROM `categories` WHERE `categoryid`='" . $db->real_escape_string($_POST["movedown"]) . "'");

            // Only proceed if the category exists.
            if ($category && $category->num_rows > 0) {
                // Get any category information we need.
                while ($row = $category->fetch_assoc()) {
                    $id = $row["categoryid"];
                    $order = $row["order"];
                }

                $getLargest = $db->query("SELECT MAX(`order`) AS 'order' FROM `categories`");

                while ($l = $getLargest->fetch_assoc()) {
                    $largest = $l["order"];
                }

                // Only proceed if the category isn't on the bottom.
                if ($order < $largest) {
                    // Get the id of the category ordered below this one.
                    $below = $db->query("SELECT * FROM `categories` WHERE `order`='" . $db->real_escape_string(($order + 1)) . "'");

                    if ($below && $below->num_rows > 0) {

                        while ($row = $below->fetch_assoc()) {
                            $belowid = $row["categoryid"];
                            $beloworder = $row["order"];
                        }

                        // Change the order of this category.
                        $db->query("UPDATE `categories` SET `order`='" . $db->real_escape_string($beloworder) . "' WHERE `categoryid`='" . $db->real_escape_string($id) . "'");

                        // Now change the order of the category formerly above this one.
                        $db->query("UPDATE `categories` SET `order`='" . $db->real_escape_string($order) . "' WHERE `categoryid`='" . $db->real_escape_string($belowid) . "'");
                    }
                }
            }
        }
    }
    if (isset($_POST["edit"]) or isset($_POST["edit_edit_cat_name"]) or isset($_POST["edit_cat_description"]))
    {
        $id = $_POST["edit"];
        $category = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($id) . "'");

        while($row = $category->fetch_assoc()) {
            // category information.
            $name = $row['categoryname'];
            $description = $row['categorydescription'];
            $id = $row['categoryid'];
        }

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
                $result = $db->query("UPDATE categories SET categoryname='" . $db->real_escape_string($name) . "',categorydescription='" . $db->real_escape_string($description) . "' WHERE categoryid='" . $db->real_escape_string($id) . "'");
                if (!$result)
                {
                    message($lang["panel.CantUpdateCategory"]);
                }
                
                else
                {
                    message($lang["panel.SuccessUpdateCategory"]);
                    include "footer.php";
                    echo("<meta http-equiv='refresh' content='1'>");
                    exit;
                }
            }
        }
        echo'
        <div class="formcontainer"><form method="post" action="">
        <input style="display:none;" type="text" name="cat_num" value="' . htmlspecialchars($id) . '">
        <div class="forminput"><label>'.$lang["panel.InputCategoryTitle"].'</label><input type="text" name="edit_edit_cat_name" value="' . htmlspecialchars($name) . '"></div>
        <div class="forminput"><label>'.$lang["panel.InputCategoryDesc"].'</label></div>';
        
        BBCodeButtons(1);
        
        echo '<div class="forminput"><textarea name="edit_cat_description" id="textbox1">' . htmlspecialchars($description) . '</textarea></div>
        <div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["panel.EditCategoryBtn"].'"> <a class="buttonbig" href="">'.$lang["panel.EditCategoryDisBtn"].'</a></div>
        </form></div>';

            include "footer.php";
            exit;
    }

    if (isset($_POST["delete"]) or isset($_POST["deleteit"]))
    {
        $id = $_POST["delete"];
        $category = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($id) . "'");

        while($row = $category->fetch_assoc()) {
            // category information.
            $name = $row['categoryname'];
            $id = $row['categoryid'];
        }
        
        if ($_POST["deleteit"])
        {
            $id = $_POST["deleteit"];
            $result = $db->query("DELETE FROM categories WHERE categoryid='" . $db->real_escape_string($id) . "'");
            
            if (!$result) {
                message($lang["CantDeleteCategory"]);
            }
            else
            {
                $threads = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($id) . "'");
                while($row = $threads->fetch_assoc()) {
                    $result = $db->query("DELETE FROM posts WHERE thread='" . $row['threadid'] . "'");
                    $result = $db->query("DELETE FROM threads WHERE threadid='" . $row['threadid'] . "'");
                }
                message($lang["panel.SuccessDeleteCategory"]);
                include "footer.php";
                echo("<meta http-equiv='refresh' content='1'>");
                exit;
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
    
    if (isset($_POST["cat_name"]))
    {
        // Ensure the name and description aren't empty.
        if ($_POST["cat_name"] == "")
        {
            message($lang["panel.CategoryNameBlank"]);
        }
        
        elseif (!isset($_POST["cat_description"]) or $_POST["cat_description"] == "")
        {
            message($lang["panel.CategoryDescBlank"]);
        }
        
        elseif (strlen($_POST["cat_description"]) > 255) {
            message($lang["panel.CategoryDescTooLong"]);
        }
        elseif (strlen($_POST["cat_name"]) > 255) {
            message($lang["panel.CategoryNameTooLong"]);
        }
        else {
            
            // Check if there are any categories at all.
            $categoryCheck = $db->query("SELECT 1 FROM `categories`");

            // If there aren't, set the order index to 0.
            if ($categoryCheck->num_rows < 1) {
                $largestOrder = 0;
            }
            // If there are, get the highest order index, and add 1 to it.
            else {
                $getLargestOrder = $db->query("SELECT MAX(`order`) AS 'order' FROM `categories`");

                while ($r = $getLargestOrder->fetch_assoc()) {
                    $largestOrder = $r["order"];
                }

                $largestOrder++;
            }

            $result = $db->query("INSERT INTO `categories` (`categoryname`, `categorydescription`, `order`) VALUES ('" . $db->real_escape_string($_POST['cat_name']) . "', '" . $db->real_escape_string($_POST['cat_description']) . "', '" . $db->real_escape_string($largestOrder) . "')");
        
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

    $result = $db->query("SELECT * FROM categories ORDER BY `order` ASC");
    $getLargest = $db->query("SELECT MAX(`order`) AS 'order' FROM `categories`");

    while ($l = $getLargest->fetch_assoc()) {
        $largest = $l["order"];
    }

    while($row = $result->fetch_assoc()) {
        $numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "'");
        $number = $numthreads->num_rows;
        echo '<div class="category"><tr>';
            echo '<td class="leftpart">';
            echo '<h3><span>' . htmlspecialchars($row["categoryname"]) . ' (#' . $row["categoryid"] . ')</span></h3>';
            echo '<div>' . formatPost($row["categorydescription"]) . '</div>';
            echo '<div><div style="float:right">';

            // Only show the move up button if this isn't the top category already.
            if ($row["order"] > 0) {
                echo '<form style="display:inline-block;" method="post" action=""><button name="moveup" value="' . $row["categoryid"] . '">'.$lang["panel.CategoryUpBtn"].'</button></form> ';
            }
            // Only show the move down button if this isn't the bottom category already.
            if ($row["order"] < $largest) {
                echo '<form style="display:inline-block;" method="post" action=""><button name="movedown" value="' . $row["categoryid"] . '">'.$lang["panel.CategoryDownBtn"].'</button></form> ';
            }

            echo '<form style="display:inline-block;" method="post" action=""><button name="edit" value="' . $row["categoryid"] . '">'.$lang["panel.CategoryEditBtn"].'</button></form> 
                <form style="display:inline-block;" method="post" action=""><button name="delete" value="' . $row["categoryid"] . '">'.$lang["panel.CategoryDeleteBtn"].'</button></form>';
            echo '</div>';
            echo $lang["panel.CatThreads"] . $number . '</div>';
        echo '</tr></div>';
    }
    echo '
    <h3>'.$lang["panel.CreateACategory"].'</h3>
    <div class="formcontainer"><form method="post" action="">
        <div class="forminput"><label>'.$lang["panel.InputCategoryTitle"].'</label><input type="text" name="cat_name"></div>
        <div class="forminput"><label>'.$lang["panel.InputCategoryDesc"].'</label></div>';
        
    BBCodeButtons(1);
        
    echo'    <div class="forminput"><textarea name="cat_description" id="textbox1"></textarea></div>
        <div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["panel.CreateCategoryBtn"].'"></div>
        </form></div>';

?>
