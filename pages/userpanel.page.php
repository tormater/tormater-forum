<?php
// userpanel.page.php
// The one-stop shop for everything a user needs.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$panel_pages = array(
    "avatarsettings" => array("userpanel/avatarsettings.page.php", $lang["userpanel.AvatarSettings"]),
    "accountsettings" => array("userpanel/accountsettings.page.php", $lang["userpanel.AccountSettings"]),
    "profilesettings" => array("userpanel/profilesettings.page.php", $lang["userpanel.ProfileSettings"])
);

if ($_SESSION["signed_in"] == false)
{
    include "header.php";
    message(sprintf($lang["nav.LoginRequired"], genURL("login")));
    include "footer.php";
    exit;
}

listener("userpanelBeforeRender");

if (array_key_exists($q2,$panel_pages)) {
    $panel_page = $q2;
}
else $panel_page = "avatarsettings";

$up_data = array(
  "buttons" => ""
);

foreach($panel_pages as $k => $v) {
    if (!strlen($v[1])) continue;
    $b_data = array("url" => genURL("userpanel/" . $k), "title" => $v[1]);
    if ($k == $panel_page) $up_data["buttons"] .= $template->render("templates/panel/panel_button_active.html",$b_data);
    else $up_data["buttons"] .= $template->render("templates/panel/panel_button.html",$b_data);
}
ob_start();
include $panel_pages[$panel_page][0];
$page_output = ob_get_contents();
ob_end_clean();

include "header.php";

echo $template->render("templates/panel/panel_navigation.html",$up_data);
echo $page_output;

include "footer.php";

?>
