<?php
// install.php
// Installs the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// If the forum is already installed, exit.
if ($config["installed"] == "yes") exit;

echo "<!DOCTYPE html><html><head>";
echo "<title>" . $lang["installer.Title"] . "</title>";
echo "<link rel='stylesheet' href='install/install.css'>";
echo "<link rel='icon' type='image/svg+xml' href='install/install.svg'>";
echo '<link rel="icon" type="image/x-icon" href="install/install.ico">';
echo "</head>";
echo "<body>";
echo "<center><img src='install/logo.svg'></center>";
echo "<div id='content'>";

// Make sure directories that need to be written to are writeable.
if (!is_writeable("config")) message($lang["installer.ConfigWarning"]);
if (!is_writeable("avatars")) message($lang["installer.AvatarWarning"]);

// Handle post requests.
if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_POST["MySQLServer"]))) {
    // Make sure the MySQL details have been inputted.
    if (!$_POST["MySQLServer"] or !$_POST["MySQLUser"] or !$_POST["MySQLPassword"] or !$_POST["MySQLDatabase"] or ($_POST["MySQLServer"] == "") or ($_POST["MySQLUser"] == "") or ($_POST["MySQLPassword"] == "") or ($_POST["MySQLDatabase"] == "")) {
        message($lang["installer.SQLMissing"]);
        $db = null;
    }
    else {
        // Make sure we can actually establish a connection to the database.
        try {
            $db = mysqli_connect($_POST["MySQLServer"], $_POST["MySQLUser"],    $_POST["MySQLPassword"], $_POST["MySQLDatabase"]);
        }
	// If the connection failed, print the SQL (or PHP) error.
        catch (Exception $e) {
            message($e);
            $db = null;
        }
    }

    // Check if this database is already populated with forum-related tables.
    if ($db !== null) {
        $dbName = $db->real_escape_string($_POST["MySQLDatabase"]);
        $tablesExist = $db->query("SHOW TABLES FROM {$dbName} WHERE tables_in_{$dbName} like 'categories' OR
	tables_in_{$dbName} like 'posts' OR
        tables_in_{$dbName} like 'threads' OR
	tables_in_{$dbName} like 'users' OR
        tables_in_{$dbName} like 'logins' OR
	tables_in_{$dbName} like 'drafts' OR
        tables_in_{$dbName} like 'auditlog'");
    }

    // This is here to inform the admin that the MySQL connection failed.
    if ($db === null) {
        message($lang["installer.SQLConnectFail"]);
    }

    // If any forum-related tables (or tables with the same names) exist, print an error.
    elseif ($tablesExist->num_rows > 0) {
        message($lang["installer.DBAlreadyExists"]);
    }

    // Make sure the admin's username isn't too short or empty.
    elseif ((strlen($_POST["adminUsername"]) < 1) or (!$_POST["adminUsername"]) or ($_POST["adminUsername"] == "")) {
        message($lang["installer.UsernameTooShort"]);
    }

    // Make sure the admin's username isn't too long.
    elseif (strlen($_POST["adminUsername"]) > 26) {
        message($lang["installer.UsernameTooLong"]);
    }

    // Make sure the admin's password isn't too short or empty.
    elseif ((strlen($_POST["adminPassword"]) < 1) or (!$_POST["adminPassword"]) or ($_POST["adminPassword"] == "")) {
        message($lang["installer.PasswordTooShort"]);
    }

    // Make sure the admin's email isn't too short or empty.
    elseif ((strlen($_POST["adminEmail"]) < 1) or (!$_POST["adminEmail"]) or ($_POST["adminEmail"] == "")) {
        message($lang["installer.EmailTooShort"]);
    }

    // Make sure the admin's email isn't too long.
    elseif (strlen($_POST["adminEmail"]) > 255) {
        message($lang["installer.EmailTooLong"]);
    }

    // Make sure the admin's password and confirm password entries match.
    elseif ($_POST["adminPassword"] != $_POST["adminConfirm"]) {
        message($lang["installer.PasswordsMismatch"]);
    }

    // If everything checks out, write the database and create the admin's account.
    else {
        $db->query("CREATE TABLE `categories` (
            `categoryid` int unsigned NOT NULL AUTO_INCREMENT,
            `categoryname` varchar(255) NOT NULL,
            `categorydescription` varchar(255) NOT NULL,
            `order` int unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (`categoryid`),
            UNIQUE KEY `category_name` (`categoryname`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE `threads` (
            `threadid` int unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `sticky` tinyint(1) NOT NULL DEFAULT '0',
            `locked` tinyint(1) NOT NULL DEFAULT '0',
            `pinned` tinyint(1) NOT NULL DEFAULT '0',
            `posts` int unsigned NOT NULL,
            `startuser` int unsigned NOT NULL,
            `starttime` int unsigned NOT NULL,
            `lastpostuser` int unsigned NOT NULL,
            `lastposttime` int unsigned NOT NULL,
            `category` int unsigned NOT NULL,
            `draft` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`threadid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE `users` (
            `userid` int unsigned NOT NULL AUTO_INCREMENT,
            `username` varchar(26) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password` varchar(128) NOT NULL,
            `role` enum('Administrator','Moderator','Member','Suspended') NOT NULL DEFAULT 'Member',
            `jointime` int unsigned NOT NULL,
            `lastactive` int unsigned DEFAULT NULL,
            `lastaction` varchar(255) DEFAULT NULL,
            `color` tinyint unsigned NOT NULL DEFAULT '1',
            `ip` varchar(128) NOT NULL,
            `salt` char(64) NOT NULL,
            `verified` tinyint(1) NOT NULL DEFAULT '0',
            `deleted` tinyint(1) NOT NULL DEFAULT '0',
            `signature` varchar(512) DEFAULT NULL,
            `bio` varchar(2048) DEFAULT NULL,
            `avatar` enum('none', 'png', 'jpg', 'gif', 'webp') NOT NULL DEFAULT 'none',
            `avataruploadtime` int unsigned DEFAULT NULL,
            PRIMARY KEY (`userid`),
            UNIQUE KEY `user_name` (`username`),
            UNIQUE KEY `user_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	        
        $db->query("CREATE TABLE `logins` (
            `ip` varchar(128) NOT NULL,
            `time` int unsigned NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
	        
        $db->query("CREATE TABLE `drafts` (
            `user` int unsigned NOT NULL,
            `thread` int unsigned NOT NULL,
            `timestamp` int unsigned NOT NULL,
            `content` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE `auditlog` (
            `time` int unsigned DEFAULT NULL,
            `actionid` int unsigned NOT NULL AUTO_INCREMENT,
            `action` enum('edit_post','delete_post','hide_post','rename_thread','delete_thread','move_thread','edit_labels','delete_avatar','edit_role','delete_user','change_forum_setting') DEFAULT NULL,
            `userid` int unsigned NOT NULL,
            `victimid` int unsigned NOT NULL,
            `before` text DEFAULT NULL,
            `after` text DEFAULT NULL,
            `context` text DEFAULT NULL,
            PRIMARY KEY (`actionid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Create the admin's account, first generating the salt, password hash, and IP address hash.
        $adminSalt = "";
        $adminHash = password_hash($_POST["adminPassword"], PASSWORD_DEFAULT);
        $adminIP = hashstring($_SERVER["REMOTE_ADDR"]);
        $adminEmail = $_POST["adminEmail"];
        $db->query("INSERT INTO `users` (username, email, password, role, jointime, color, ip, salt, verified) VALUES ('" . $db->real_escape_string($_POST["adminUsername"]) . "', '" . $db->real_escape_string($adminEmail) . "', '" . $db->real_escape_string($adminHash) . "', 'Administrator', '" . time() . "', '1', '" . $db->real_escape_string($adminIP) . "', '" . $db->real_escape_string($adminSalt) . "' ,'1')");
        // Create the default category.
        $db->query("INSERT INTO `categories` (`categoryname`, `categorydescription`, `order`) VALUES ('" . $db->real_escape_string($lang["installer.GeneralCategoryName"]) . "', '" . $db->real_escape_string($lang["installer.GeneralCategoryDescription"]) . "', '" . 0 . "')");

        // Now write our MySQL details to the config as well as whether the forum has been installed or not.
        $config["installed"] = "yes";
        $config["MySQLServer"] = $_POST["MySQLServer"];
        $config["MySQLDatabase"] = $_POST["MySQLDatabase"];
        $config["MySQLUser"] = $_POST["MySQLUser"];
        $config["MySQLPass"] = $_POST["MySQLPassword"];
        $config["baseURL"] = rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "/");
        $config["modRewriteDisabled"] = 1;

        // Only try to use the mod rewrite if we detect that it's enabled.
        if (function_exists("apache_get_modules")) {
            if (in_array("mod_rewrite", apache_get_modules())) {
                $config["modRewriteDisabled"] = 0;
            }
        }

	// If everything worked, display a message saying so.
        if (saveConfig("./config/config.php", $config)) {
            // Log the administrator in.
            $_SESSION["signed_in"] = "true";
            $_SESSION["userid"] = "1";
            $_SESSION["username"] = $_POST["adminUsername"];
            $_SESSION["role"] = "Administrator";

            message($lang["installer.InstallSuccess"]);
            echo "</div></body></html>";
	    refresh(4);
            exit;
	}
	// If the config file failed to write, display an error message.
	else {
	    message($lang["installer.ConfigWriteFail"]);
	}
    }
}

echo($lang["installer.Welcome"]);

echo("<hr>
<form method='post' autocomplete='on'>
<h3>" . $lang["installer.SQLDetails"] . "</h3><br/>
<div class='field'><label>" . $lang["installer.SQLServer"] . "</label>
<input type='text' id='MySQLServer' name='MySQLServer' placeholder='localhost' value='" . htmlspecialchars($_POST["MySQLServer"] ?? "") . "'></div>
<div class='field'><label>" . $lang["installer.SQLDatabase"] . "</label>
<input type='text' id='MySQLDatabase' name='MySQLDatabase' value='" . htmlspecialchars($_POST["MySQLDatabase"] ?? "") . "'></div>
<div class='field'><label>" . $lang["installer.SQLUser"] . "</label>
<input type='text' id='MySQLUser' name='MySQLUser' value='" . htmlspecialchars($_POST["MySQLUser"] ?? "") . "'></div>
<div class='field'><label>" . $lang["installer.SQLPassword"] . "</label>
<input type='text' id='MySQLPassword' name='MySQLPassword' value='" . htmlspecialchars($_POST["MySQLPassword"] ?? "") . "'></div>
<h3>" . $lang["installer.AdministratorAccount"] . "</h3><br/>
<div class='field'><label>" . $lang["register.Username"] . "</label>
<input type='text' id='adminUsername' name='adminUsername' autocomplete='username' value='" . htmlspecialchars($_POST["adminUsername"] ?? "") . "'></div>
<div class='field'><label>" . $lang["register.Email"] . "</label>
<input type='email' id='adminEmail' name='adminEmail' autocomplete='email' value='" . htmlspecialchars($_POST["adminEmail"] ?? "") . "'></div>
<div class='field'><label>" . $lang["register.Password"] . "</label>
<input type='password' id='adminPassword' name='adminPassword' autocomplete='new-password' value='" . htmlspecialchars($_POST["adminPassword"] ?? "") . "'></div>
<div class='field'><label>" . $lang["register.PasswordConf"] . "</label>
<input type='password' id='adminConfirm' name='adminConfirm' autocomplete='new-password' value='" . htmlspecialchars($_POST["adminConfirm"] ?? "") . "'></div>
<br>
<input class='buttonbig' type='submit' value='" . $lang["installer.InstallButton"] . "'>
</form>");

echo "</div></p>";
echo($languageSelector);
echo "</body></html>";
?>
