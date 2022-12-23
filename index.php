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

// Check the user's role, verification status, and deletion status. Then ensure their session reflects it accordingly.
if ($_SESSION["signed_in"] == true) {
	$rolecheck = $db->query("SELECT role, verified, deleted FROM users WHERE userid='" . $_SESSION["userid"] . "'");
	while ($r = $rolecheck->fetch_assoc())
	{
		if ($r["role"] != $_SESSION["role"]) {
			$_SESSION["role"] = $r["role"];
		}
        if ($r["verified"] != $_SESSION["verified"]) {
            $_SESSION["verified"] = $r["verified"];
        }
        if ($r["deleted"] != $_SESSION["deleted"]) {
            $_SESSION["deleted"] = $r["deleted"];
        }
	}
}

// Log out any suspended, unverified, or deleted users.
if (($_SESSION["role"] == "Suspended") or ($_SESSION["verified"] == "0") or ($_SESSION["deleted"] == "1")) {
    logout();
}

// Process the URL and set a couple of variables for easy use.
$url = explode('/', $_GET['url']);
if (isset($url[0])) $q1 = $url[0];
if (isset($url[1])) $q2 = $url[1];
if (isset($url[2])) $q3 = $url[2];
if (isset($url[3])) $q4 = $url[3];

// Based on the URL, serve the user with a corresponding page.
if ($config['installed'] == "no") require "install/install.php";
elseif (!$q1) require "pages/homepage.page.php";
elseif ($q1 == "upgrade") require "upgrade/upgrade.page.php";
elseif ($q1 == "signup") require "pages/signup.page.php";
elseif ($q1 == "register") require "pages/signup.page.php";
elseif ($q1 == "login") require "pages/login.page.php";
elseif ($q1 == "logout") logout();
elseif ($q1 == "newthread") require "pages/newthread.page.php";
elseif ($q1 == "category") require "pages/category.page.php";
elseif ($q1 == "thread") require "pages/thread.page.php";
elseif ($q1 == "userlist") require "pages/userlist.page.php";
elseif ($q1 == "user") require "pages/user.page.php";
elseif ($q1 == "settings") require "pages/settings.page.php";
elseif ($q1 == "panel") require "pages/panel.page.php";
else {
	message($lang["error.PageNotFound"]);
	require "pages/homepage.page.php";
}

?>
