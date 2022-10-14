<?php
//database.php
// Allows the forum to access the MySQL database.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$db = mysqli_connect($config["MySQLServer"], $config["MySQLUser"],  $config["MySQLPass"], $config["MySQLDatabase"]);

class Database {

// Connect to a database given its credentials.
function connect($server, $database, $username, $password)
{
	if(!$db = mysqli_connect($config["MySQLServer"], $config["MySQLUser"],  $config["MySQLPass"], $config["MySQLDatabase"])) return false;
	return true;
}

}

?>

