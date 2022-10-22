<?php
// panel.php
// The admin panel, which is important for forum administration.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if (!$_SESSION["role"] == "Administrator")
{
	message($lang["nav.AdminsOnly"]);
    include "footer.php";
    exit;
}

// Find out which page we're loading

if ($q2 == "category")
{
    include "panelcategory.php";
}
elseif ($q2 == "users")
{
    include "panelusers.php";
}
else
{
    include "panelsettings.php";
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("action.Panel");
}

?>
