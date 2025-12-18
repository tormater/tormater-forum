<?php
// panel.page.php
// The admin panel, which is important for forum administration.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if (!(get_role_permissions() & PERM_EDIT_FORUM))
{
    message($lang["nav.AdminsOnly"]);
    include "footer.php";
    exit;
}

$panel_pages = array(
    "settings" => array("panelsettings.page.php", $lang["panel.ForumSettings"]),
    "category" => array("panelcategory.page.php", $lang["panel.Categories"]),
    "extensions" => array("panelextensions.page.php", $lang["panel.Extensions"]),
    "auditlog" => array("panelauditlog.page.php", $lang["panel.AuditLog"]),
    "useradmin" => array("paneluseradmin.page.php",""),
    "restoreuser" => array("paneluseradmin.page.php","")
);

listener("panelBeforeRender");

$data = array(
  "buttons" => ""
);

foreach($panel_pages as $k => $v) {
    if (!strlen($v[1])) continue;
    $b_data = array("url" => genURL("panel/" . $k), "title" => $v[1]);
    $data["buttons"] .= $template->render("templates/panel/panel_button.html",$b_data);
}


echo $template->render("templates/panel/panel_navigation.html",$data);

if (array_key_exists($q2,$panel_pages)) {
    include $panel_pages[$q2][0];
}
else include $panel_pages["settings"][0];


include "footer.php";

update_last_action("action.Panel");

?>
