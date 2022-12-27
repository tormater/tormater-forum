<?php
// upgrade.page.php
// Upgrade the database.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$deleteduser = $db->query("SHOW COLUMNS FROM `users` LIKE 'deleted'");
$usersignature = $db->query("SHOW COLUMNS FROM `users` LIKE 'signature'");
$userbio = $db->query("SHOW COLUMNS FROM `users` LIKE 'bio'");
$userpassword = $db->query("SHOW COLUMNS FROM `users` LIKE 'password' varchar(128) NOT NULL");
$userip = $db->query("SHOW COLUMNS FROM `users` LIKE 'ip' varchar(128) NOT NULL");
$usersalt = $db->query("SHOW COLUMNS FROM `users` LIKE 'salt' char(64) NOT NULL");

if ($deleteduser->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `deleted` tinyint(1) NOT NULL DEFAULT '0'");
    $upgraded = true;
}
elseif ($usersignature->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `signature` varchar(512) DEFAULT NULL");
    $upgraded = true;
}
elseif ($userbio->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `bio` varchar(2048) DEFAULT NULL");
    $upgraded = true;
}
elseif ($userpassword->num_rows < 1)
{
    $db->query("ALTER TABLE `users` MODIFY `password` varchar(128) NOT NULL");
    $upgraded = true;
}
elseif ($userip->num_rows < 1)
{
    $db->query("ALTER TABLE `users` MODIFY `ip` varchar(128) NOT NULL");
    $upgraded = true;
}
elseif ($usersalt->num_rows < 1)
{
    $db->query("ALTER TABLE `users` MODIFY `salt` char(64) NOT NULL");
    $upgraded = true;
}
else
{
    $upgraded = false;
}

require "pages/header.php";

if ($upgraded === true)
{
    message($lang["upgrade.Success"]);
}
else
{
    message($lang["upgrade.None"]);
}

require "pages/footer.php";

?>
