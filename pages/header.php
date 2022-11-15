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
	<div id="wrapper">
    <div id="forumheader"><span class="forumtitle"><?php echo $config["forumName"]; ?></span></div>
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
		<div id="content">
