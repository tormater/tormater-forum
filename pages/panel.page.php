<?php
// panel.page.php
// The admin panel, which is important for forum administration.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if ($_SESSION["role"] != "Administrator")
{
	message($lang["nav.AdminsOnly"]);
	include "footer.php";
	exit;
}

echo '<div class="panelMenu"><a class="buttonbig" href="' . genURL("panel") . '">'.$lang["panel.ForumSettings"].'</a> <a class="buttonbig" href="' . genURL("panel/category") . '">'.$lang["panel.Categories"].'</a> <a class="buttonbig" href="' . genURL("panel/extensions") . '">'.$lang["panel.Extensions"].'</a> <a class="buttonbig" href="' . genURL("panel/auditlog") . '">'.$lang["panel.AuditLog"].'</a></div>';

// Find out which page we're loading

if ($q2 == "category")
{
	include "panelcategory.page.php";
}
elseif ($q2 == "extensions")
{
	include "panelextensions.page.php";
}
elseif ($q2 == "deleteuser")
{
	include "paneluseradmin.page.php";
}
elseif ($q2 == "useradmin")
{
	include "paneluseradmin.page.php";
}
elseif ($q2 == "restoreuser")
{
    $db->query("UPDATE users SET deleted=0 WHERE userid='" . $db->real_escape_string($q3) . "'");
    include "paneluser.page.php";
}
elseif ($q2 == "auditlog")
{
	include "panelauditlog.page.php";
}
else
{
	include "panelsettings.page.php";
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action("action.Panel");
}

?>
