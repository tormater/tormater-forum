<?php
// upgrade.page.php
// Upgrade the database.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$upgraded = false;

$deleteduser = $db->query("SHOW COLUMNS FROM `users` LIKE 'deleted'");
$usersignature = $db->query("SHOW COLUMNS FROM `users` LIKE 'signature'");
$userbio = $db->query("SHOW COLUMNS FROM `users` LIKE 'bio'");
$userpassword = $db->query("SHOW COLUMNS FROM `users` LIKE 'password' varchar(128) NOT NULL");
$userip = $db->query("SHOW COLUMNS FROM `users` LIKE 'ip' varchar(128) NOT NULL");
$usersalt = $db->query("SHOW COLUMNS FROM `users` LIKE 'salt' char(64) NOT NULL");
$useravatar = $db->query("SHOW COLUMNS FROM `users` LIKE 'avatar'");
$useravatartime = $db->query("SHOW COLUMNS FROM `users` LIKE `avataruploadtime`");
$logins = $db->query("SHOW TABLES LIKE `logins`");
$drafts = $db->query("SHOW TABLES LIKE `drafts`");

if ($deleteduser->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `deleted` tinyint(1) NOT NULL DEFAULT '0'");
    $upgraded = true;
}
if ($usersignature->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `signature` varchar(512) DEFAULT NULL");
    $upgraded = true;
}
if ($userbio->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `bio` varchar(2048) DEFAULT NULL");
    $upgraded = true;
}
if ($userpassword->num_rows < 1)
{
    $db->query("ALTER TABLE `users` MODIFY `password` varchar(128) NOT NULL");
    $upgraded = true;
}
if ($userip->num_rows < 1)
{
    $db->query("ALTER TABLE `users` MODIFY `ip` varchar(128) NOT NULL");
    $upgraded = true;
}
if ($usersalt->num_rows < 1)
{
    $db->query("ALTER TABLE `users` MODIFY `salt` char(64) NOT NULL");
    $upgraded = true;
}
if ($useravatar->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `avatar` enum('none', 'png', 'jpg', 'gif') NOT NULL DEFAULT 'none'");
    $upgraded = true;
}
if ($useravatartime->num_rows < 1)
{
    $db->query("ALTER TABLE `users` ADD `avataruploadtime` int unsigned DEFAULT NULL");
    $upgraded = true;
}
if ($logins->num_rows < 1)
{
    $db->query("CREATE TABLE `logins` (
        `ip` varchar(128) NOT NULL,
        `time` int unsigned NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");
    $upgraded = true;
}
if ($drafts->num_rows < 1)
{
    $db->query("CREATE TABLE `drafts` (
        `user` int unsigned NOT NULL,
        `thread` int unsigned NOT NULL,
        `timestamp` int unsigned NOT NULL,
        `content` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;");
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
