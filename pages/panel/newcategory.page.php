<?php
// newcategory.page.php
// Creates a new category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if (isset($_POST["cat_name"]))
    {
        if ($_POST["cat_name"] == "") {
            message($lang["panel.CategoryNameBlank"]);
            goto err;
        }
        if (!isset($_POST["cat_description"]) or $_POST["cat_description"] == "") {
            message($lang["panel.CategoryDescBlank"]);
            goto err;
        }
        if (strlen($_POST["cat_description"]) > 255) {
            message($lang["panel.CategoryDescTooLong"]);
            goto err;
        }
        if (strlen($_POST["cat_name"]) > 255) {
            message($lang["panel.CategoryNameTooLong"]);
            goto err;
        }
        
        
        if (isset($_GET["at"]) && $_GET["at"] == "top") {
            $db->query("UPDATE `categories` SET `order`=`order` + 1");
            $getSmallestOrder = $db->query("SELECT MIN(`order`) AS 'order' FROM `categories`");
            if ($getSmallestOrder->num_rows < 1) {
                $largestOrder = 0;
            }
            while ($r = $getSmallestOrder->fetch_assoc()) {
                $largestOrder = $r["order"] - 1;
            }
        }
        else {
            $getLargestOrder = $db->query("SELECT MAX(`order`) AS 'order' FROM `categories`");

            if ($getLargestOrder->num_rows < 1) {
                $largestOrder = 0;
            }
            while ($r = $getLargestOrder->fetch_assoc()) {
                $largestOrder = $r["order"] + 1;
            }
        }

        $result = $db->query("INSERT INTO `categories` (`categoryname`, `categorydescription`, `order`) VALUES ('" . $db->real_escape_string($_POST['cat_name']) . "', '" . $db->real_escape_string($_POST['cat_description']) . "', '" . $db->real_escape_string($largestOrder) . "')");
        
        if (!$result)
        {
            message($lang["error.SomethingWentWrong"]);
            goto err;
        }
        message($lang["panel.SuccessAddedCategory"]);
        redirect("panel/category");
    }
}

err:

echo '<h2>'.$lang["panel.CreateACategory"].'</h2>
    <form method="post" action="">
        <div class="forminput"><label>'.$lang["panel.InputCategoryTitle"].'</label><input type="text" name="cat_name"></div>
        <div class="forminput"><label>'.$lang["panel.InputCategoryDesc"].'</label></div>';
        
BBCodeButtons(1);
        
echo'    <div class="forminput"><textarea name="cat_description" id="textbox1"></textarea></div>
        <div class="forminput"><input type="submit" class="buttonbig" value="'.$lang["panel.CreateCategoryBtn"].'"></div>
        </form>';
