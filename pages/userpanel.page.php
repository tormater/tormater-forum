<?php
// userpanel.page.php
// The one-stop shop for everything a user needs.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if ($_SESSION["signed_in"] == false)
{
	message(sprintf($lang["nav.LoginRequired"], genURL("login")));
    include "footer.php";
    exit;
}

echo '<div class="panelMenu"><a class="buttonbig" href="' . genURL("userpanel/accountsettings") . '">'.$lang["userpanel.AccountSettings"].'</a> <a class="buttonbig" href="' . genURL("userpanel/profilesettings") . '">'.$lang["userpanel.ProfileSettings"].'</a></div>';

// Find out which page we're loading

if ($q2 == "accountsettings")
{
    include "userpanelaccountsettings.page.php";
}
elseif ($q2 == "profilesettings")
{
    include "userpanelprofilesettings.page.php";
}
else
{
    include "userpanelaccountsettings.page.php";
}

include "footer.php";

?>
