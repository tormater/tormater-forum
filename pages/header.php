<?php
// header.php
// Placed at the top of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Powered by tormater-forum" />
	<meta name="keywords" content="tormater-forum, tormater, forum" />
	<title><?php echo $config["forumName"]; ?></title>

<?php
if(!isset($config["forumTheme"]))
{
	echo '<link rel="stylesheet" href="/../themes/Blue/style.css" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="/../themes/Blue/icon.ico">';
}
else
{
	echo '<link rel="stylesheet" href="/../themes/' . $config["forumTheme"] . '/style.css" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="/../themes/' . $config["forumTheme"] . '/icon.ico">';
}
?>

</head>
<body>
<h1><?php echo $config["forumName"]; ?></h1>
	<div id="wrapper">
	<div id="menu">
		<?php
			echo '<a class="item" href="/">' . $lang["header.Home"] . '</a> ';
			echo '<a class="item" href="/userlist/">' . $lang["header.Userlist"] . '</a> ';
		
		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="/settings/">' . $lang["header.Settings"] . '</a> ';
            if(isset($categoryID))
            {
			    echo '<a class="item" href="/newthread/' . $categoryID . '">' . $lang["header.NewThread"] . '</a> ';
            }
            else
            {
                echo '<a class="item" href="/newthread/">' . $lang["header.NewThread"] . '</a> ';
            }
        }
        
		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $_SESSION["role"] == "Administrator")
		{
			echo '<a class="item" href="/newcategory/">' . $lang["header.NewCategory"] . '</a> ';
			echo '<a class="item" href="/panel/">' . $lang["header.Panel"] . '</a> ';
		}
		

		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="/logout/">' . $lang["header.Logout"] . '</a> ';
		}
		
		echo '<div id="userbar">';
 			if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
 			{
 	 			echo "Hello, <a href='/user/" . $_SESSION["userid"] . "/' id='" . $_SESSION["role"] . "'>" . $_SESSION["username"] . "</a>.";
 			}
 			else
 			{
 				echo '<a href="/login/">' . $lang["header.Login"] . '</a> or <a href="/signup/">' . $lang["header.Signup"] . '</a>.';
 			}
		echo "</div>";
		?>
	</div>
		<div id="content">
