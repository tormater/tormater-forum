<?php
// ajax.php
// Initializes the forum's libraries for use in AJAX scripts.

chdir(dirname(__DIR__, 1));

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

require "libs/database.php";
require "libs/formatter.php";

// Extensions config
if (file_exists("config/extensions.php")) {
    require "config/extensions.php";
} 
else {
    if (file_exists("config/extensions-default.php")) {
        require "config/extensions-default.php";
    }
}

// Get our language file
if(!isset($config["forumLang"]) or !file_exists("lang/" . $config["forumLang"] . ".php"))
{
	require 'lang/EN_US.php';
}
else
{
	require 'lang/' . $config["forumLang"] . '.php';
}

require "libs/extensions.php";

?>
