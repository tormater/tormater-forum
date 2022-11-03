<?php
// index.php
// Initializes the forum and loads the requested page.

// Define a constant to ensure pages are only loaded through this index file.
define("INDEXED", "1");

// Require all the necessary files for the forum to function.

require "libs/functions.php";

// Config handling, stop your config from being overwritten on upgrade

if (file_exists("config/config-setup.php")) {
    require "config/config-setup.php";
} 

if (file_exists("config/config.php")) {
    require "config/config.php";
} 
else {
    saveConfig("config/config.php", $config);
}


require "libs/database.php";
require "libs/formatter.php";

// Get our language file
if(!isset($config["forumLang"]) or !file_exists("lang/" . $config["forumLang"] . ".php"))
{
	require 'lang/EN_US.php';
}
else
{
	require 'lang/' . $config["forumLang"] . '.php';
}


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
elseif ($q1 == "installer") require "install/install.php";
elseif ($q1 == "signup") require "pages/signup.php";
elseif ($q1 == "login") require "pages/login.php";
elseif ($q1 == "logout") logout();
elseif ($q1 == "newthread") require "pages/newthread.php";
elseif ($q1 == "category") require "pages/category.php";
elseif ($q1 == "thread") require "pages/thread.php";
elseif (($q1 == "userlist") && ($config["userlistEnabled"] == true) && ((($config["userlistMembersOnly"] == true) && ($_SESSION["signed_in"] == true)) || ($config["userlistMembersOnly"] == false))) require "pages/userlist.php";
elseif ($q1 == "user") require "pages/user.php";
elseif ($q1 == "settings") require "pages/settings.php";
elseif ($q1 == "panel") require "pages/panel.php";
elseif (($q1 == "userlist") && ($config["userlistEnabled"] == false)) {
    message($lang["error.UserlistDisabled"]);
    require "pages/homepage.php";
}
elseif (($q1 == "userlist") && ($config["userlistEnabled"] == true) && (($config["userlistMembersOnly"] == true) && ($_SESSION["signed_in"] == false))) {
    message($lang["error.UserlistMembersOnly"]);
    require "pages/homepage.php";
}
else {
	message($lang["error.PageNotFound"]);
	require "pages/homepage.php";
}

?>
