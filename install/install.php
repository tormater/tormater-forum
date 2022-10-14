<?php
// install.php
// Installs the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// If the forum is already installed, exit.
if ($config["installed"] == "yes") exit;

// Make sure the MySQL details have been inputted.
if (!$config["MySQLServer"] or !$config["MySQLUser"] or !$config["MySQLPass"] or !$config["MySQLDatabase"]) {
	require "pages/header.php";
	message("Error: Please fill out the config with your MySQL details before trying to install the forum.");
	require "pages/footer.php";
	exit;
}

$db->query("CREATE TABLE `categories` (
	`categoryid` int unsigned NOT NULL AUTO_INCREMENT,
	`categoryname` varchar(255) NOT NULL,
	`categorydescription` varchar(255) NOT NULL,
	PRIMARY KEY (`categoryid`),
	UNIQUE KEY `category_name` (`categoryname`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3;");

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
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb3;");

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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb3;");

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
  	PRIMARY KEY (`userid`),
  	UNIQUE KEY `user_name` (`username`),
  	UNIQUE KEY `user_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;");

require "pages/header.php";
message("Database successfully written. It's recommended you change the installed option in the config to yes so that nobody else can run this script again.");
require "pages/footer.php";

?>
