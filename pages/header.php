<?php
// header.php
// Placed at the top of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Powered by tormater-forum" />
	<meta name="keywords" content="tormater-forum, tormater, forum" />
    <title><?= $config["forumName"]; ?></title>

<?php
if(!isset($config["forumTheme"]) or !file_exists(dirname(__DIR__,1) . "/themes/" . $config["forumTheme"] . "/style.css"))
{
	echo '<link rel="stylesheet" href="' . genURL("themes/Skyline/style.css?v=0.1") . '" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="' . genURL("themes/Skyline/icon.ico") . '">';
}
else
{
	echo '<link rel="stylesheet" href="' . genURL('themes/' . $config["forumTheme"] . '/style.css?v=0.1') . '" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="' . genURL('themes/' . $config["forumTheme"] . '/icon.ico') . '">';
}
?>

</head>
<body>
	<div class="tormater-forum">
    <div id="forumheader">
    <?php echo '<a class="forumtitle" href="' . genURL("") . '">' . $config["forumName"] . '</a>';
    		echo '<div id="userbar">';
 			if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
 			{
 	 			echo $lang["header.Hello"] . '<b><a href="' . genURL('user/' . $_SESSION["userid"]) . '" id="' . $_SESSION["role"] . '">' . $_SESSION["username"] . '</a></b>';
 			}
 			else
 			{
 				echo '<a href="' . genURL("login") . '">' . $lang["header.Login"] . '</a>'.$lang["header.or"].'<a href="' . genURL("signup") . '">' . $lang["header.Signup"] . '</a>';
 			}
		echo "</div>";
    ?>
    </div>
	<div id="menu">
		<?php
			echo '<a class="item" href="' . genURL("") . '">' . $lang["header.Home"] . '</a> ';
			
			if ($config["userlistEnabled"] == true) {
			    echo '<a class="item" href="' . genURL("userlist") . '">' . $lang["header.Userlist"] . '</a> ';
            }
		
		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="' . genURL("settings") . '">' . $lang["header.Settings"] . '</a> ';
            if(isset($categoryID))
            {
			    echo '<a class="item" href="' . genURL('newthread/' . $categoryID) . '">' . $lang["header.NewThread"] . '</a> ';
            }
            else
            {
                echo '<a class="item" href="' . genURL("newthread") . '">' . $lang["header.NewThread"] . '</a> ';
            }
        }
        
		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $_SESSION["role"] == "Administrator")
		{
			echo '<a class="item" href="' . genURL("panel") . '">' . $lang["header.Panel"] . '</a> ';
		}
		

		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="' . genURL("logout") . '">' . $lang["header.Logout"] . '</a> ';
		}

		?>
	</div>
<div id="pageBar"><div class="pagination">
<?php 
echo '<a class="pageButton" href="' . genURL("") . '">' . $config["forumName"] . '</a>';
echo '<span class="paginationdots">/</span>';

if (!$q1)
{
    echo '<span class="pageButtonDisabled pageButtonLast">' . $lang["page.homepage"] . '</span>';
}
elseif ($q1 == "thread")
{
    // Get the category information.
    $categoryDB = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($category) . "'");


    while ($row = $categoryDB->fetch_assoc()) {
	    $categoryName = $row['categoryname'];
	    $categoryDescription = $row['categorydescription'];
    }
    echo '<a class="pageButton" href="' . genURL('category/' . $category). '">' . htmlspecialchars($categoryName) . '</a>';
    echo '<span class="paginationdots">/</span>';
    echo '<span class="pageButtonDisabled pageButtonLast">' . htmlspecialchars($title) . '</span>';
}
elseif ($q1 == "category")
{
    echo '<span class="pageButton pageButtonLast">' . htmlspecialchars($categoryName) . '</span>';
}
elseif ($q1 == "user")
{
    echo '<a class="pageButton" href="' . genURL("userlist") . '">' . $lang["page.userlist"] . '</a>';
    echo '<span class="paginationdots">/</span>';
    echo '<span class="pageButtonDisabled pageButtonLast">' . htmlspecialchars($username) . '</span>';
}
elseif ($q1 == "panel")
{
    if ($q2 == "user")
    {
        echo '<a class="pageButton" href="' . genURL("panel") . '">' . $lang["page.Panel"] . '</a>';
        echo '<span class="paginationdots">/</span>';
        echo '<span class="pageButtonDisabled pageButtonLast">' . $lang["page.PanelUsers"] . '</span>';
    }
    elseif ($q2 == "category")
    {
        echo '<a class="pageButton" href="' . genURL("panel") . '">' . $lang["page.Panel"] . '</a>';
        echo '<span class="paginationdots">/</span>';
        echo '<span class="pageButtonDisabled pageButtonLast">' . $lang["page.PanelCats"] . '</span>';
    }
    else
    {
        echo '<span class="pageButtonDisabled pageButtonLast">' . $lang["page.Panel"] . '</span>';
    }
}
else
{ 
    if (!$lang["page." . $q1])
    {
        $name = $lang["page.homepage"];
    }
    else
    {
        $name = $lang["page." . $q1];
    }
    echo '<span class="pageButtonDisabled pageButtonLast">' . $name . '</span>';
}
?>
</div></div>
<div id="content">
