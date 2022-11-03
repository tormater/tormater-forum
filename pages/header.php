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
	echo '<link rel="stylesheet" href="/../themes/Skyline/style.css" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="/../themes/Skyline/icon.ico">';
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
			
			if ($config["userlistEnabled"] == true) {
			    echo '<a class="item" href="/userlist/">' . $lang["header.Userlist"] . '</a> ';
            }
		
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
			echo '<a class="item" href="/panel/">' . $lang["header.Panel"] . '</a> ';
		}
		

		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="/logout/">' . $lang["header.Logout"] . '</a> ';
		}
		
		echo '<div id="userbar">';
 			if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
 			{
 	 			echo "Hello, <b><a href='/user/" . $_SESSION["userid"] . "/' id='" . $_SESSION["role"] . "'>" . $_SESSION["username"] . "</a></b>.";
 			}
 			else
 			{
 				echo '<a href="/login/">' . $lang["header.Login"] . '</a> or <a href="/signup/">' . $lang["header.Signup"] . '</a>.';
 			}
		echo "</div>";
		?>
	</div>
		<div id="content">
