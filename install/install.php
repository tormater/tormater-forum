<?php
// install.php
// Installs the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// If the forum is already installed, exit.
if ($config["installed"] == "yes") exit;

// Handle post requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make sure the MySQL details have been inputted.
    if (!$_POST["MySQLServer"] or !$_POST["MySQLUser"] or !$_POST["MySQLPassword"] or !$_POST["MySQLDatabase"] or ($_POST["MySQLServer"] == "") or ($_POST["MySQLUser"] == "") or ($_POST["MySQLPassword"] == "") or ($_POST["MySQLDatabase"] == "")) {
	    message("Error: Please input MySQL details.");
    }

    // Make sure we can actually establish a connection to the database.
    elseif (!$db = mysqli_connect($_POST["MySQLServer"], $_POST["MySQLUser"],  $_POST["MySQLPassword"], $_POST["MySQLDatabase"])) {
        message("Error: Couldn't connect to the MySQL Database.");
    }

    // Make sure the admin's username isn't too short or empty.
    elseif ((strlen($_POST["adminUsername"]) < 1) or (!$_POST["adminUsername"]) or ($_POST["adminUsername"] == "")) {
        message("Error: Your username must be at least 1 character in length.");
    }

    // Make sure the admin's username isn't too long.
    elseif (strlen($_POST["adminUsername"]) > 26) {
        message("Error: Your username is too long.");
    }

    // Make sure the admin's password isn't too short or empty.
    elseif ((strlen($_POST["adminPassword"]) < 1) or (!$_POST["adminPassword"]) or ($_POST["adminPassword"] == "")) {
        message("Error: Your password must be at least 1 character in length.");
    }

    // Make sure the admin's email isn't too short or empty.
    elseif ((strlen($_POST["adminEmail"]) < 1) or (!$_POST["adminEmail"]) or ($_POST["adminEmail"] == "")) {
        message("Error: Your email must be at least 1 character in length.");
    }

    // Make sure the admin's email isn't too long.
    elseif (strlen($_POST["adminEmail"]) > 255) {
        message("Error: Your email is too long.");
    }

    // Make sure the admin's password and confirm password entries match.
    elseif ($_POST["adminPassword"] != $_POST["adminConfirm"]) {
        message("Error: Your passwords don't match.");
    }

    // If everything checks out, write the database and create the admin's account.
    else {
        $db->query("CREATE TABLE `categories` (
	        `categoryid` int unsigned NOT NULL AUTO_INCREMENT,
	        `categoryname` varchar(255) NOT NULL,
	        `categorydescription` varchar(255) NOT NULL,
	        PRIMARY KEY (`categoryid`),
	        UNIQUE KEY `category_name` (`categoryname`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");

        $db->query("CREATE TABLE `posts` (
	        `postid` int unsigned NOT NULL AUTO_INCREMENT,
	        `thread` int unsigned NOT NULL,
  	        `user` int unsigned NOT NULL,
  	        `timestamp` int unsigned NOT NULL,
  	        `editedby` int unsigned DEFAULT NULL,
  	        `edittime` int unsigned DEFAULT NULL,
  	        `deletedby` int unsigned DEFAULT NULL,
  	        `content` text NOT NULL,
  	        PRIMARY KEY (`postid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");

        $db->query("CREATE TABLE `threads` (
	        `threadid` int unsigned NOT NULL AUTO_INCREMENT,
  	        `title` varchar(255) NOT NULL,
  	        `sticky` tinyint(1) NOT NULL DEFAULT '0',
  	        `locked` tinyint(1) NOT NULL DEFAULT '0',
  	        `posts` int unsigned NOT NULL,
  	        `startuser` int unsigned NOT NULL,
  	        `starttime` int unsigned NOT NULL,
  	        `lastpostuser` int unsigned NOT NULL,
  	        `lastposttime` int unsigned NOT NULL,
  	        `category` int unsigned NOT NULL,
  	        PRIMARY KEY (`threadid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");

        $db->query("CREATE TABLE `users` (
	        `userid` int unsigned NOT NULL AUTO_INCREMENT,
	        `username` varchar(26) NOT NULL,
	        `email` varchar(255) NOT NULL,
	        `password` char(32) NOT NULL,
  	        `role` enum('Administrator','Moderator','Member','Suspended') NOT NULL DEFAULT 'Member',
  	        `jointime` int unsigned NOT NULL,
  	        `lastactive` int unsigned DEFAULT NULL,
  	        `lastaction` varchar(255) DEFAULT NULL,
  	        `color` tinyint unsigned NOT NULL DEFAULT '1',
  	        `ip` char(32) NOT NULL,
  	        `salt` varchar(255) NOT NULL,
  	        `verified` tinyint(1) NOT NULL DEFAULT '0',
		`deleted` tinyint(1) NOT NULL DEFAULT '0',
		`signature` varchar(512) DEFAULT NULL,
		`bio` varchar(2048) DEFAULT NULL,
  	        PRIMARY KEY (`userid`),
  	        UNIQUE KEY `user_name` (`username`),
  	        UNIQUE KEY `user_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");

        // Create the admin's account, first generating the salt, password hash, and IP address hash.
        $adminSalt = random_str(32);
        $adminHash = md5($adminSalt . $_POST["adminPassword"]);
        $adminIP = md5($_SERVER["REMOTE_ADDR"]);
        $db->query("INSERT INTO `users` (username, email, password, role, jointime, color, ip, salt, verified) VALUES ('" . $db->real_escape_string($_POST["adminUsername"]) . "', '" . $db->real_escape_string($adminEmail) . "', '" . $db->real_escape_string($adminHash) . "', 'Administrator', '" . time() . "', '1', '" . $db->real_escape_string($adminIP) . "', '" . $db->real_escape_string($adminSalt) . "' ,'1')");

        // Now write our MySQL details to the config as well as whether the forum has been installed or not.
        $config["installed"] = "yes";
        $config["MySQLServer"] = $_POST["MySQLServer"];
        $config["MySQLDatabase"] = $_POST["MySQLDatabase"];
        $config["MySQLUser"] = $_POST["MySQLUser"];
        $config["MySQLPass"] = $_POST["MySQLPassword"];
        $config["baseURL"] = rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "/");
        saveConfig("./config/config.php", $config);

        require "pages/header.php";
        message("Database and config successfully written. You can now view your forum.");
        require "pages/footer.php";
        exit;
    }
}

require "pages/header.php";

echo("<title>Installer</title>
<link rel='stylesheet' href='themes/Skyline/style.css'>
<form method='post' autocomplete='on'>
<fieldset>
<legend>Installation Information</legend>
<h3>MySQL Details</h3>
<label for='MySQLServer'>MySQL Server:</label></br>
<input type='text' id='MySQLServer' name='MySQLServer' value='" . $_POST["MySQLServer"] . "'></p>
<label for='MySQLDatabase'>MySQL Database:</label></br>
<input type='text' id='MySQLDatabase' name='MySQLDatabase' value='" . $_POST["MySQLDatabase"] . "'></p>
<label for='MySQLUser'>MySQL User:</label></br>
<input type='text' id='MySQLUser' name='MySQLUser' value='" . $_POST["MySQLUser"] . "'></p>
<label for='MySQLPassword'>MySQL Password:</label></br>
<input type='text' id='MySQLPassword' name='MySQLPassword' value='" . $_POST["MySQLPassword"] . "'></p>
<h3>Administrator Account</h3>
<label for='adminUsername'>Username:</label></br>
<input type='text' id='adminUsername' name='adminUsername' autocomplete='username' value='" . $_POST["adminUsername"] . "'></p>
<label for='adminEmail'>Email:</label></br>
<input type='email' id='adminEmail' name='adminEmail' autocomplete='email' value='" . $_POST["adminEmail"] . "'></p>
<label for='adminPassword'>Password:</label></br>
<input type='password' id='adminPassword' name='adminPassword' autocomplete='new-password' value='" . $_POST["adminPassword"] . "'></p>
<label for='adminConfirm'>Confirm Password:</label></br>
<input type='password' id='adminConfirm' name='adminConfirm' autocomplete='new-password' value='" . $_POST["adminConfirm"] . "'></p>
<input type='submit' value='Submit'>
</fieldset>
</form>");

require "pages/footer.php";

?>
