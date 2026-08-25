<?php
// panel.page.php
// The admin panel, which is important for forum administration.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$panel_pages = array(
    "settings" => array("panel/settings.page.php", $lang["panel.ForumSettings"]),
    "category" => array("panel/category.page.php", $lang["panel.Categories"]),
    "newcategory" => array("panel/newcategory.page.php", ""),
    "extensions" => array("panel/extensions.page.php", $lang["panel.Extensions"]),
    "auditlog" => array("panel/auditlog.page.php", $lang["panel.AuditLog"]),
    "useradmin" => array("panel/useradmin.page.php",""),
    "restoreuser" => array("panel/useradmin.page.php","")
);

if (!(get_role_permissions() & PERM_EDIT_FORUM))
{
    include "header.php";
    message($lang["nav.AdminsOnly"]);
    include "footer.php";
    exit;
}

listener("panelBeforeRender");

include "header.php";

if (array_key_exists($q2,$panel_pages)) {
    $panel_page = $q2;
}
else $panel_page = "settings";

$data = array(
  "buttons" => ""
);

foreach($panel_pages as $k => $v) {
    if (!strlen($v[1])) continue;
    $b_data = array("url" => genURL("panel/" . $k), "title" => $v[1]);
    if ($k == $panel_page) $data["buttons"] .= $template->render("templates/panel/panel_button_active.html",$b_data);
    else $data["buttons"] .= $template->render("templates/panel/panel_button.html",$b_data);
}

echo $template->render("templates/panel/panel_navigation.html",$data);

include $panel_pages[$panel_page][0];

include "footer.php";

update_last_action("action.Panel");

?>
