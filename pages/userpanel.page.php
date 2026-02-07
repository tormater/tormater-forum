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

include "header.php";


$data = array(
  "buttons" => ""
);

foreach($panel_pages as $k => $v) {
    if (!strlen($v[1])) continue;
    $b_data = array("url" => genURL("userpanel/" . $k), "title" => $v[1]);
    $data["buttons"] .= $template->render("templates/panel/panel_button.html",$b_data);
}

echo $template->render("templates/panel/panel_navigation.html",$data);

if (array_key_exists($q2,$panel_pages)) {
    include $panel_pages[$q2][0];
}
else include $panel_pages["avatarsettings"][0];

include "footer.php";

?>
