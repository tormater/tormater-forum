<?php
// header.php
// Placed at the top of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" lang="nl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="A short description." />
	<meta name="keywords" content="put, keywords, here" />
	<title><?php echo $config["forumName"]; ?></title>

<?php
if(!isset($config["forumTheme"]))
{
	echo '<link rel="stylesheet" href="/../themes/Blue/style.css" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="/../themes/Blue/favicon.png">';
}
else
{
	echo '<link rel="stylesheet" href="/../themes/' . $config["forumTheme"] . '/style.css" type="text/css">';
    echo '<link rel="icon" type="image/x-icon" href="/../themes/' . $config["forumTheme"] . '/favicon.png">';
}
?>

</head>
<body>
<h1><?php echo $config["forumName"]; ?></h1>
	<div id="wrapper">
	<div id="menu">
		<?php
			echo '<a class="item" href="/">Home</a> ';
			echo '<a class="item" href="/userlist/">Userlist</a> ';
		
		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="/settings/">Settings</a> ';
            if(isset($categoryID))
            {
			    echo '<a class="item" href="/newthread/' . $categoryID . '">Create a thread</a> ';
            }
            else
            {
                echo '<a class="item" href="/newthread/">Create a thread</a> ';
            }
        }
        
		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true && $_SESSION["role"] == "Administrator")
		{
			echo '<a class="item" href="/newcategory/">Create a category</a> ';
			echo '<a class="item" href="/panel/">Admin Panel</a> ';
		}
		

		if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
		{
			echo '<a class="item" href="/logout/">Log out</a> ';
		}
		
		echo '<div id="userbar">';
 			if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
 			{
 	 			echo "Hello, <a href='/user/" . $_SESSION["userid"] . "/' id='" . $_SESSION["role"] . "'>" . $_SESSION["username"] . "</a>.";
 			}
 			else
 			{
 				echo '<a href="/login/">Log in</a> or <a href="/signup/">Sign up</a>.';
 			}
		echo "</div>";
		?>
	</div>
		<div id="content">
