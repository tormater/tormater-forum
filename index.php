<?php
// index.php
// Initializes the forum and loads the requested page.

// Define a constant to ensure pages are only loaded through this index file.
define("INDEXED", "1");

// Define all the accessable pages

$pages = array(
    "upgrade" => "upgrade/upgrade.page.php",
    "signup" => "pages/signup.page.php",
    "register" => "pages/signup.page.php",
    "login" => "pages/login.page.php",
    "newthread" => "pages/newthread.page.php",
    "category" => "pages/category.page.php",
    "thread" => "pages/thread.page.php",
    "userlist" => "pages/userlist.page.php",
    "user" => "pages/user.page.php",
    "userpanel" => "pages/userpanel.page.php",
    "panel" => "pages/panel.page.php",
    "search" => "pages/search.page.php",
    "post" => "pages/post.page.php"
);
$functionPages = array(
    "logout" => "logout",
);
$fallbackPage = "pages/homepage.page.php";

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

// Now that we have the config, connect to the MySQL database if the forum is installed.
if ($config["installed"] == "yes") {
	$db = mysqli_connect($config["MySQLServer"], $config["MySQLUser"],  $config["MySQLPass"], $config["MySQLDatabase"]);
}

// Load up the formatter.
require "libs/formatter.php";
require "libs/permission.php";

// Extensions config
if (file_exists("config/extensions.php")) {
    require "config/extensions.php";
} 
else {
    if (file_exists("config/extensions-default.php")) {
        require "config/extensions-default.php";
    } 
    saveExtensionConfig("config/extensions.php", $extensions);
}

// Check for malformed baseURLs.
$config["baseURL"] = rtrim($config["baseURL"], '/');

// Get our language file
require 'lang/EN_US.php';
$lang_compat = $lang;
unset($lang);
if(is_null($config["forumLang"]) or !isset($config["forumLang"]) or !file_exists("lang/" . $config["forumLang"] . ".php"))
{
	require 'lang/EN_US.php';
}
else
{
	require 'lang/' . $config["forumLang"] . '.php';
}

foreach($lang_compat as $k => $v) {
    if (!array_key_exists($k, $lang)) {
        $lang[$k] = "Missing: &#39;" . $k . "&#39;";
    }
}

// If a session doesn't exist, set one.
if (!session_id()) {
	session_name($config["cookieName"] . "_Session");
	session_start();
}

// Check the user's role, verification status, and deletion status. Then ensure their session reflects it accordingly.
if ($config['installed'] != "no" && isset($_SESSION["signed_in"]) && $_SESSION["signed_in"] == true) {
	$rolecheck = $db->query("SELECT role, verified, deleted, ip FROM users WHERE userid='" . $_SESSION["userid"] . "'");
	while ($r = $rolecheck->fetch_assoc())
	{
		// Prevent session stealing, log the user out if their IP is different from the one they logged in with.
		if (hashstring($_SERVER["REMOTE_ADDR"]) != $r["ip"]) {
			logout();
		}
		if ($r["role"] != $_SESSION["role"]) {
			$_SESSION["role"] = $r["role"];
		}
		if (isset($_SESSION["verified"]) && ($r["verified"] != $_SESSION["verified"])) {
			$_SESSION["verified"] = $r["verified"];
		}
		if (isset($_SESSION["deleted"]) && ($r["deleted"] != $_SESSION["deleted"])) {
			$_SESSION["deleted"] = $r["deleted"];
		}
	}
	// Log out any suspended, unverified, or deleted users.
	if (($_SESSION["role"] == "Suspended")
	or (isset($_SESSION["verified"]) && $_SESSION["verified"] == "0")
	or (isset($_SESSION["deleted"]) && $_SESSION["deleted"] == "1")) {
	    logout();
	}
}

require "libs/language.php";

// Process the URL and set a couple of variables for easy use.
if (isset($_GET['url'])) $url = explode('/', $_GET['url']);
$q1 = $q2 = $q3 = $q4 = "";
if (isset($url[0])) $q1 = $url[0];
if (isset($url[1])) $q2 = $url[1];
if (isset($url[2])) $q3 = $url[2];
if (isset($url[3])) $q4 = $url[3];

require "libs/templates.php";
require "libs/extensions.php";

listener("beforePageLoad");

// Based on the URL, serve the user with a corresponding page.
if ($config['installed'] != "yes") require "install/install.php";
elseif (isset($pages[$q1])) require $pages[$q1];
elseif (isset($functionPages[$q1])) call_user_func($functionPages[$q1]);
elseif (!$q1) require $fallbackPage;
else {
	http_response_code(404);
	include "pages/header.php";
	$data = array("title" => $lang["error.SomethingWentWrong"], "message" => message($lang["error.PageNotFound"],true), "back" => $lang["error.GoBack"]);
	echo $template->render("templates/fatal.html", $data);
	include "pages/footer.php";
}

listener("afterPageLoad");

?>
