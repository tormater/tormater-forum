<?php
// upgrade.page.php
// Upgrade the database.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;
// `deleted` tinyint(1) NOT NULL DEFAULT '0',

$deleteduser = $db->query("SHOW COLUMNS FROM `users` LIKE 'deleted'");

if ($deleteduser->num_rows == 0)
{
    $addTable = $db->query("ALTER TABLE `users` ADD `deleted` tinyint(1) NOT NULL DEFAULT '0'");
    $upgraded = true;
}

require "pages/header.php";

if ($upgraded == true)
{
    message($lang["upgrade.Success"]);
}
else
{
    message($lang["upgrade.None"]);
}

require "pages/footer.php";
?>
