<?php
// index.php
// Initializes the forum and loads the requested page.

// Define a constant to ensure pages are only loaded through this index file.
define("INDEXED", "1");

// Require all the necessary files for the forum to function.
require "config/config.php";
require "libs/database.php";
require "libs/functions.php";

// If a session doesn't exist, set one.
if (!session_id()) {
	session_name($config["cookieName"] . "_Session");
	session_start();
}

// Check the user's role and ensure their session reflects it accordingly.
if ($_SESSION["signed_in"] == true) {
	$rolecheck = $db->query("SELECT role FROM users WHERE userid='" . $_SESSION["userid"] . "'");
	while ($r = $rolecheck->fetch_assoc())
	{
		if (!$r["role"] == $_SESSION["role"])
		{
			$_SESSION["role"] == $r["role"];
		}
	}
}

// Process the URL and set a couple of variables for easy use.
$url = explode('/', $_GET['url']);
if (isset($url[0])) $q1 = $url[0];
if (isset($url[1])) $q2 = $url[1];
if (isset($url[2])) $q3 = $url[2];
if (isset($url[3])) $q4 = $url[3];

// Based on the URL, serve the user with a corresponding page.
if (!$q1) require "pages/homepage.php";
elseif ($q1 == "signup") require "pages/signup.php";
elseif ($q1 == "login") require "pages/login.php";
elseif ($q1 == "logout") logout();
elseif ($q1 == "newcategory") require "pages/newcategory.php";
elseif ($q1 == "newthread") require "pages/newthread.php";
elseif ($q1 == "category") require "pages/category.php";
elseif ($q1 == "thread") require "pages/thread.php";
elseif ($q1 == "userlist") require "pages/userlist.php";
elseif ($q1 == "user") require "pages/user.php";
elseif ($q1 == "settings") require "pages/settings.php";
elseif ($q1 == "panel") require "pages/panel.php";
else {
	echo "Error: requested page not found.";
	require "pages/homepage.php";
}

?>
